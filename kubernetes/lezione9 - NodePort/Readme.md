# NodePort

Esponiamo un Deployment utilizzando un servizio NodePort.

## Creare l'applicazione

    kubectl apply -f deploy-app.yaml

## Creare il servizio NodePort

    kubectl apply -f nodeport.yaml

## Visualizzare l'elenco dei pod

    kubectl get pods -o wide

## Utilizzare il NodePort

Poiché utilizziamo Docker Desktop e il nodo Docker Desktop è associato a localhost, per raggiungere il servizio devi utilizzare **localhost** + il **NodePort**.

Quando utilizzi un provider cloud, devi ottenere l'indirizzo IP di un nodo invece di localhost.

Ottenere l'indirizzo IP pubblico del nodo

    kubectl get nodes -o wide

## Pulizia

    kubectl delete -f nodeport.yaml
    kubectl delete -f deploy-app.yaml
