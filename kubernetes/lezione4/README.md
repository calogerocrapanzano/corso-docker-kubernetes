# Deployment

Utilizziamo ora il modello Deployment invece del modello Pod.

## Creare il Deployment

    kubectl apply -f deploy-example.yaml

## Visualizzare l’elenco dei Pod

    kubectl get pods -o wide
    
## Visualizzare i dettagli del Pod

    kubectl describe pod deploy-example

## Visualizzare le informazioni sul Deployment

    kubectl get deploy
    kubectl describe deploy deploy-example

## Visualizzare il nome del ReplicaSet

    kubectl get rs

## Visualizzare i dettagli del ReplicaSet

    kubectl describe rs

## Pulizia

    kubectl delete -f deploy-example.yaml
