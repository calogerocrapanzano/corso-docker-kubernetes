# Lezione 2

## Creare un Pod in modo dichiarativo

Creiamo ora un Pod utilizzando un file YAML.

    kubectl apply -f myapp.yaml

## Ottenere informazioni

    kubectl get pods -o wide
    kubectl describe pod myapp-pod

## Collegare il terminale

    kubectl exec -it myapp-pod -- bash

Stampare la variabile d’ambiente `DBCON` impostata nel file YAML.

    echo $DBCON

## Uscire dall’istanza

    exit

## Pulizia

    kubectl delete -f myapp.yaml
