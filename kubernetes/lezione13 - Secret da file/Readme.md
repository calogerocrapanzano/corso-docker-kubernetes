# Secret: montare un file creato da un file esistente

Una Secret può essere creata direttamente da un file già presente sul computer
con `kubectl create secret generic --from-file`. In questa lezione il file
`app-secret.properties` viene trasformato nella Secret `app-secret` e montato
nel Pod come `/etc/app/app-secret.properties`.

Il file incluso contiene solo credenziali di esempio. In un progetto reale non
inserire credenziali vere nel repository e proteggere il file locale con i
permessi appropriati.

## Creare la Secret dal file

    kubectl create secret generic app-secret \
      --from-file=app-secret.properties

Il nome del file diventa la chiave della Secret. È possibile visualizzare solo
i metadati della Secret senza stampare il contenuto:

    kubectl get secret app-secret
    kubectl describe secret app-secret

## Creare il Pod

    kubectl apply -f pod.yaml

## Verificare il file montato

    kubectl get pod secret-box
    kubectl exec secret-box -it -- /bin/sh

Nel container il file è disponibile nella directory `/etc/app`:

    ls -l /etc/app
    cat /etc/app/app-secret.properties
    exit

I file montati da una Secret sono leggibili dal processo del container. Per
questo motivo è importante limitare gli accessi e montare solo le chiavi
necessarie.

## Pulizia

    kubectl delete -f pod.yaml --grace-period=0 --force
    kubectl delete secret app-secret
