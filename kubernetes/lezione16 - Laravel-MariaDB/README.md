# Lezione 16 — Laravel, MariaDB e Kubernetes

In questa lezione deployiamo una piccola applicazione Laravel 12 che legge
alcuni studenti da MariaDB. L'obiettivo è mostrare il collegamento tra
applicazione, Service Kubernetes, Secret, ConfigMap e volume persistente.

## Prerequisiti

- Docker Desktop oppure Docker Engine;
- `kubectl`;
- un cluster Kubernetes locale, ad esempio Minikube o Docker Desktop;
- PHP 8.4 o superiore, coerente con le dipendenze presenti nel `composer.lock`;
- Composer;
- Laravel Installer;
- Node.js e npm, utili per gli asset Laravel;
- un terminale con accesso a Internet durante la creazione dell'app e il primo
  `docker build`.

Verificare gli strumenti:

```bash
php --version
composer --version
laravel --version
node --version
npm --version
docker --version
kubectl version --client
```

Installare Laravel Installer, se necessario:

```bash
composer global require laravel/installer
```

## 1. Creare il progetto Laravel da zero

I comandi seguenti sono quelli da usare per ricreare l'applicazione partendo
da una directory vuota. La directory `app/` presente in questa lezione
contiene già il risultato minimo necessario per seguire il laboratorio.

```bash
laravel new app
cd app
php artisan make:model Student -m
php artisan make:controller StudentController
php artisan make:seeder StudentSeeder
npm install
npm run build
```

Durante `laravel new` è sufficiente scegliere il database SQLite di default:
la configurazione verrà sostituita da Kubernetes tramite variabili d'ambiente.

## 2. Funzionalità dell'applicazione

L'applicazione usa il modello `Student`, la migration
`database/migrations/*_create_students_table.php`, il seeder
`database/seeders/StudentSeeder.php`, il controller
`app/Http/Controllers/StudentController.php` e la vista
`resources/views/students.blade.php`.

Per questa demo le sessioni Laravel sono salvate nel filesystem del container
tramite `SESSION_DRIVER=file`; in questo modo non è necessaria una tabella
`sessions` aggiuntiva nel database.

Il canale `stderr` invia i log al flusso standard del container, quindi sono
visibili con `kubectl logs`.

In locale, per provare la stessa applicazione con un database già disponibile,
configurare il file `.env` e lanciare:

```bash
php artisan migrate --seed
php artisan serve
```

## 3. Costruire l'immagine Laravel

Dalla directory della lezione:

```bash
docker build -t lezione16-laravel:1.0 ./app
```

Con Minikube, rendere disponibile l'immagine al runtime del cluster:

```bash
eval "$(minikube docker-env)"
docker build -t lezione16-laravel:1.0 ./app
```

Con Docker Desktop Kubernetes il comando `docker build` normale è sufficiente.
In un cluster remoto bisogna invece pubblicare l'immagine in un registry e
modificare `image` in `laravel-deployment.yaml`.

Il Dockerfile crea `.env` solo temporaneamente durante la build per permettere
la generazione della chiave Laravel e lo elimina prima di terminare. Nel pod
Kubernetes la configurazione arriva esclusivamente da ConfigMap e Secret;
`APP_KEY` è conservata in una Secret dedicata a Laravel, separata dalle
credenziali di MariaDB.

## 4. Avviare MariaDB e Laravel su Kubernetes

Assicurarsi di avere un contesto Kubernetes corretto:

```bash
kubectl config current-context
kubectl get nodes
```

Applicare le risorse nell'ordine seguente:

```bash
kubectl apply -f mariadb-secret.yaml
kubectl apply -f mariadb-configmap.yaml
kubectl apply -f mariadb-pvc.yaml
kubectl apply -f mariadb-service.yaml
kubectl apply -f mariadb-deployment.yaml
kubectl wait --for=condition=Available deployment/lezione16-mariadb --timeout=180s
kubectl apply -f laravel-configmap.yaml
kubectl apply -f laravel-secret.yaml
kubectl apply -f laravel-deployment.yaml
kubectl apply -f laravel-service.yaml
```

Controllare lo stato:

```bash
kubectl get pods,svc,pvc
kubectl logs deployment/lezione16-mariadb
kubectl logs deployment/lezione16-laravel
```

L'init container dell'applicazione attende che MariaDB risponda, esegue
`php artisan migrate --seed --force` e poi avvia il server Laravel.

## 5. Aprire la pagina

Con Minikube:

```bash
minikube service lezione16-laravel --url
```

In alternativa recuperare la porta NodePort e aprire l'indirizzo del nodo:

```bash
kubectl get service lezione16-laravel
minikube ip
```

La pagina deve mostrare gli studenti inseriti dal seeder e indicare che i dati
sono stati letti da MariaDB.

## 6. Verificare il PVC

```bash
kubectl get pvc
kubectl delete pod -l app=lezione16-mariadb
kubectl get pods -w
```

Dopo la ricreazione del pod, la pagina deve continuare a mostrare i dati: il
database è montato sul PVC e non nel filesystem effimero del pod.

## 7. Troubleshooting

```bash
kubectl describe pod -l app=lezione16-laravel
kubectl logs deployment/lezione16-laravel -c migrate
kubectl get events --sort-by=.lastTimestamp
kubectl exec deployment/lezione16-laravel -- php artisan about
```

Se il pod Laravel è `ImagePullBackOff`, verificare che l'immagine sia stata
costruita nel runtime usato dal cluster. Se la migration fallisce, controllare
Secret, ConfigMap e log del deployment MariaDB.

## 8. Cleanup

```bash
kubectl delete -f laravel-service.yaml
kubectl delete -f laravel-configmap.yaml
kubectl delete -f laravel-deployment.yaml
kubectl delete -f mariadb-deployment.yaml
kubectl delete -f mariadb-service.yaml
kubectl delete -f mariadb-pvc.yaml
kubectl delete -f mariadb-configmap.yaml
kubectl delete -f mariadb-secret.yaml
```

Il PVC viene eliminato esplicitamente dal comando precedente. Per un laboratorio
reale valutare prima se i dati devono essere conservati.
