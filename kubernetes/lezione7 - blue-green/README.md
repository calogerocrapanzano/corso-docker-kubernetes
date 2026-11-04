# Deployment blue-green

**NOTA: se esegui questa esercitazione su un laptop/PC ARM64, modifica il file YAML e cambia il nome dell'immagine da "hello-app" a "hello-arm", mantenendo invariato il tag.**

## Creare un Deployment V1

    kubectl create -f hello-dep-v1.yaml

## Creare il servizio ClusterIP

    kubectl create -f clusterip.yaml

## Visualizzare l'elenco dei pod

    kubectl get pods -o wide

## Visualizzare l'applicazione in un browser

Per prima cosa, esegui il port forwarding verso il ClusterIP:

    kubectl port-forward service/svc-front 8080:8080

Apri un browser e vai all'indirizzo http://localhost:8080

La versione dell'applicazione sarà V1.

---

## Creare un Deployment V2

    kubectl create -f hello-dep-v2.yaml

## Visualizzare l'elenco dei pod

    kubectl get pods -o wide

## Modificare il manifest del ClusterIP

Modifica il file clusterip.yaml e cambia l'ultima riga in modo che il servizio punti al Deployment V2.

    app: hello-v2

## Aggiornare il servizio ClusterIP

    kubectl apply -f clusterip.yaml

## Visualizzare l'applicazione in un browser

Per prima cosa, esegui il port forwarding verso il ClusterIP:

    kubectl port-forward service/svc-front 8080:8080

Apri un browser e vai all'indirizzo http://localhost:8080

La versione dell'applicazione sarà V2.

## Pulizia

    kubectl delete -f hello-dep-v1.yaml
    kubectl delete -f hello-dep-v2.yaml
    kubectl delete -f clusterip.yaml
