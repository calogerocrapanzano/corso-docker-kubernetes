# ClusterIP

## Creare il servizio

    kubectl apply -f clusterip.yaml

## Creare l'applicazione

    kubectl apply -f deploy-app.yaml

## Creare BusyBox

    kubectl apply -f pod.yaml

## Visualizzare l'elenco dei pod

    kubectl get pods -o wide

## Connettersi al container BusyBox

    kubectl exec mybox -it -- /bin/sh

## BusyBox e comandi principali

BusyBox è un'immagine Linux minimale che raccoglie in un unico eseguibile molti
comandi tipici dei sistemi Unix. È utile in Kubernetes per creare un pod leggero
da usare come ambiente di diagnosi della rete e dei servizi: non è pensato per
eseguire applicazioni complete, ma per effettuare rapidamente verifiche
all'interno del cluster.

Una volta aperta la shell con `kubectl exec`, questi sono alcuni comandi utili:

* `ls`, `pwd`, `cd`: esplorare il filesystem del container;
* `cat`, `head`, `tail`: leggere file e configurazioni;
* `env` e `hostname`: visualizzare le variabili d'ambiente e il nome del pod;
* `ps`: elencare i processi in esecuzione;
* `ip addr` e `ip route`: controllare interfacce, indirizzi e routing di rete;
* `nslookup svc-example`: verificare la risoluzione DNS del servizio ClusterIP;
* `wget -qO- http://svc-example:8080`: effettuare una richiesta HTTP al servizio;
* `ping -c 3 <indirizzo-o-nome>`: verificare la raggiungibilità, quando ICMP è
  consentito;
* `date` e `free`: controllare ora di sistema e memoria disponibile;
* `exit`: uscire dalla shell del container.

Per esempio, la sequenza seguente verifica prima il DNS e poi la risposta HTTP
del servizio:

    nslookup svc-example
    wget -qO- http://svc-example:8080

## Ottenere la pagina iniziale di Nginx tramite il servizio ClusterIP

    wget -qO- http://svc-example:8080
    exit

## Pulizia

    kubectl delete -f clusterip.yaml
    kubectl delete -f deploy-app.yaml
    kubectl delete -f pod.yaml --grace-period=0 --force
