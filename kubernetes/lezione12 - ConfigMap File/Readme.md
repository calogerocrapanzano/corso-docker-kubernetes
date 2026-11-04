# ConfigMap: montare un file dentro un Pod

Una ConfigMap permette di separare la configurazione dall'immagine del
container. In questa lezione la chiave `app.properties` della ConfigMap viene
montata nel Pod come file `/etc/app/app.properties`.

## Creare la ConfigMap

    kubectl apply -f configmap.yaml

## Visualizzare la ConfigMap

    kubectl get configmap
    kubectl describe configmap app-config

## Creare il Pod

    kubectl apply -f pod.yaml

## Verificare il file montato

    kubectl get pod configmap-box
    kubectl exec configmap-box -it -- /bin/sh

All'interno del container, visualizzare il contenuto del file:

    ls -l /etc/app
    cat /etc/app/app.properties
    exit

Il nome della chiave della ConfigMap diventa il nome del file. La proprietà
`readOnly: true` impedisce al container di modificare la configurazione montata.

## Pulizia

    kubectl delete -f configmap.yaml
    kubectl delete -f pod.yaml --grace-period=0 --force
