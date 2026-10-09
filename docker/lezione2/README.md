# Lezione 2 — Persistenza dei dati con i volumi Docker

In questa esercitazione avviamo Nginx usando un volume Docker nominato. Il volume contiene i file del sito e permette di conservarli anche quando il container viene rimosso.

## Obiettivi

- distinguere il filesystem effimero di un container da un volume persistente;
- montare un volume nella directory servita da Nginx;
- verificare che i dati sopravvivano alla rimozione del container;
- ispezionare e rimuovere un volume Docker.

## Prerequisiti

- Docker Desktop oppure Docker Engine avviato;
- un terminale aperto nella directory `lezione2`.

Verificare l'installazione:

```bash
docker --version
```

Spostarsi nella directory dell'esercizio e verificare che la pagina iniziale sia presente:

```bash
cd lezione2
ls -l index.html
```

## 1. Esaminare i file dell'esempio

```text
lezione2/
├── index.html
└── README.md
```

Il servizio usa l'immagine ufficiale `nginx:alpine`. Il volume nominato `lezione2-dati` viene montato in `/usr/share/nginx/html`, cioè la directory da cui Nginx serve le pagine.

## 2. Creare e inizializzare il volume

```bash
docker volume create lezione2-dati
```

Copiamo la pagina iniziale nel volume usando un container temporaneo Alpine. Il file locale viene montato soltanto come sorgente e il container temporaneo viene rimosso automaticamente:

```bash
test -f "$PWD/index.html" || { echo "Errore: eseguire il comando dalla directory lezione2"; exit 1; }

docker run --rm \
  -v lezione2-dati:/data \
  -v "$PWD/index.html:/seed/index.html:ro" \
  alpine sh -c 'cp /seed/index.html /data/index.html'
```

## 3. Avviare il container Nginx

```bash
docker run -d \
  --name lezione2-web \
  -p 8080:80 \
  -v lezione2-dati:/usr/share/nginx/html \
  nginx:alpine
```

Controllare container e volume:

```bash
docker ps
docker volume ls
```

Aprire [http://localhost:8080](http://localhost:8080) oppure verificare dal terminale:

```bash
curl http://localhost:8080
```

## 4. Modificare un file salvato nel volume

La pagina può essere modificata direttamente nel container, nel volume montato:

```bash
docker exec -it lezione2-web sh
```

Dentro al container:

```sh
apk add --no-cache vim
vi /usr/share/nginx/html/index.html
exit
```

Aggiornare il browser e verificare che la nuova pagina sia visibile.

## 5. Verificare la persistenza

Fermare e rimuovere il container:

```bash
docker stop lezione2-web
docker rm lezione2-web
```

Il volume non viene rimosso. Avviare un nuovo container usando lo stesso volume:

```bash
docker run -d \
  --name lezione2-web \
  -p 8080:80 \
  -v lezione2-dati:/usr/share/nginx/html \
  nginx:alpine
```

Aprire di nuovo [http://localhost:8080](http://localhost:8080). La pagina modificata è ancora presente perché è stata salvata nel volume, non soltanto nel filesystem effimero del container.

## 6. Ispezionare il volume

```bash
docker volume ls
docker volume inspect lezione2-dati
```

Il campo `Mountpoint` mostra dove Docker conserva fisicamente il volume. Con Docker Desktop il percorso appartiene normalmente alla macchina virtuale interna di Docker e non è una directory direttamente navigabile dal computer host.

## 7. Pulizia finale

Rimuovere il container e, quando non serve più, anche il volume:

```bash
docker stop lezione2-web
docker rm lezione2-web
docker volume rm lezione2-dati
```

Attenzione: rimuovere il volume elimina definitivamente i dati contenuti al suo interno.

## Concetto chiave

Il container è effimero: può essere fermato e ricreato. Il volume è invece un archivio gestito da Docker, separato dal ciclo di vita del container, e conserva i dati finché non viene rimosso esplicitamente.

## Approfondimento

Come esercizio successivo si può sostituire il volume nominato con un bind mount, collegando una directory del computer alla directory `/usr/share/nginx/html` del container.
