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

## Ottenere la pagina iniziale di Nginx tramite il servizio ClusterIP

    wget -qO- http://svc-example:8080
    exit

## Pulizia

    kubectl delete -f clusterip.yaml
    kubectl delete -f deploy-app.yaml
    kubectl delete -f pod.yaml --grace-period=0 --force
