# ConfigMap

## Creare la ConfigMap

    kubectl apply -f cm.yaml

## Visualizzare le informazioni della ConfigMap

    kubectl get cm
    kubectl describe configmap cm-example

Visualizziamo le stesse informazioni in formato YAML

    kubectl get configmap cm-example -o YAML

## Creare il pod

    kubectl apply -f pod.yaml

## Connettersi a BusyBox

    kubectl exec mybox -it -- /bin/sh

## Visualizzare la variabile d'ambiente CITY

    echo $CITY
    exit

## Pulizia

    kubectl delete -f cm.yaml
    kubectl delete -f pod.yaml --grace-period=0 --force
