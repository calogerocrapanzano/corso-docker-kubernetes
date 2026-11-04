# Volumi persistenti

## Creare il PersistentVolume

    kubectl apply -f pv.yaml

## Visualizzare il PersistentVolume

    kubectl get pv

## Creare la PersistentVolumeClaim

    kubectl apply -f pvc.yaml

## Visualizzare la PersistentVolumeClaim

    kubectl get pvc

## Creare il Pod

    kubectl apply -f pod.yaml

## Connettersi al container BusyBox

    kubectl exec mybox -it -- /bin/sh

## Creare un file

    cd demo
    cat > hello.txt
    Hello World
    Invio e Ctrl-D
    ls
    exit

## Eliminare il Pod

Eliminiamo il Pod e creiamolo nuovamente per verificare che il file sia stato
conservato nel volume persistente.

    kubectl delete -f pod.yaml --force --grace-period=0

## Creare nuovamente il Pod

    kubectl apply -f pod.yaml

## Connettersi nuovamente al container BusyBox

    kubectl exec mybox -it -- /bin/sh
    cd demo
    ls
    cat hello.txt
    exit

## Pulizia

    kubectl delete -f pod.yaml  --force --grace-period=0
    kubectl delete -f pvc.yaml
    kubectl delete -f pv.yaml
