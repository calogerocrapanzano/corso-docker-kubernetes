# ReplicaSet

Utilizziamo ora il modello ReplicaSet invece del modello Pod.

## Creare il ReplicaSet

    kubectl apply -f rs-example.yaml

## Visualizzare l’elenco dei Pod

    kubectl get pods -o wide

## Visualizzare il nome del ReplicaSet

    kubectl get rs

## Visualizzare i dettagli del ReplicaSet

    kubectl describe rs rs-example

## Pulizia

    kubectl delete -f rs-example.yaml
