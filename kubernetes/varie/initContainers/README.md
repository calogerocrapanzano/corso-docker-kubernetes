# Init container

In questa esercitazione vediamo come utilizzare un **init container** in Kubernetes.

Un init container viene eseguito prima del container principale. In questo esempio scarica una pagina web e la salva in una directory condivisa. Quando l’init container termina, Nginx utilizza lo stesso file e lo pubblica.

## Obiettivi

- capire quando viene eseguito un init container;
- condividere dati tra un init container e il container principale tramite un volume `emptyDir`;
- verificare che Nginx serva il file preparato dall’init container;
- esporre temporaneamente il Pod nel browser usando `kubectl port-forward`.

## 1. Applicare il Pod

Applicare il manifest Kubernetes:

```bash
kubectl apply -f myapp.yaml
```

Il manifest crea un Pod chiamato `init-demo` con:

- un init container chiamato `install`, basato sull’immagine `busybox`;
- un container principale chiamato `nginx`;
- un volume temporaneo `emptyDir` chiamato `workdir`.

L’init container scarica la pagina da `http://info.cern.ch` e la salva in `/work-dir/index.html`. La stessa directory viene montata da Nginx come `/usr/share/nginx/html`.

## 2. Controllare l’avvio del Pod

Visualizzare lo stato del Pod:

```bash
kubectl get pods
```

Attendere che lo stato diventi `Running`. Per maggiori informazioni:

```bash
kubectl describe pod init-demo
```

Se il Pod rimane nello stato `Init:0/1`, l’init container non ha ancora terminato la sua attività.

## 3. Controllare i log dell’init container

Visualizzare i log del container `install`:

```bash
kubectl logs init-demo -c install
```

Il comando consente di verificare eventuali errori durante il download della pagina.

## 4. Controllare il file dal container Nginx

Accedere alla shell del container principale:

```bash
kubectl exec -it init-demo -c nginx -- sh
```

Dentro al container:

```sh
cat /usr/share/nginx/html/index.html
exit
```

Il contenuto visualizzato è stato preparato dall’init container e reso disponibile a Nginx attraverso il volume condiviso.

## 5. Esporre il servizio nel browser

Il manifest indica che Nginx usa la porta `80` del container, ma questa porta non viene pubblicata automaticamente sul computer.

Per effettuare una prova locale, eseguire:

```bash
kubectl port-forward pod/init-demo 8080:80
```

Lasciare il comando attivo e aprire nel browser:

<http://localhost:8080>

Verrà visualizzata la pagina scaricata dall’init container.

Per interrompere il collegamento, tornare al terminale e premere:

```text
Ctrl+C
```

`kubectl port-forward` crea un collegamento temporaneo tra la porta `8080` del computer e la porta `80` del Pod. Non modifica il manifest e non crea un oggetto `Service`.

### Differenza tra `containerPort`, `port-forward` e `Service`

- `containerPort: 80` documenta la porta usata dall’applicazione nel container;
- `kubectl port-forward` permette un accesso temporaneo e locale, utile per i test;
- un `Service` permette di esporre e raggiungere stabilmente uno o più Pod. Per un accesso dalla rete esterna si potrebbe usare, ad esempio, un Service di tipo `NodePort`.

## 6. Pulizia

Eliminare il Pod:

```bash
kubectl delete -f myapp.yaml
```

Il comando elimina il Pod e il volume temporaneo `emptyDir` associato. Di conseguenza, anche il file scaricato dall’init container viene eliminato.

## Concetto chiave

L’init container prepara l’ambiente prima che parta il container principale. In questo esempio scarica un file, lo salva nel volume condiviso e poi Nginx utilizza quel file per rispondere alle richieste web.
