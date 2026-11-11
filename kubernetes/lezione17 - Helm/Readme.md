# Esercizio Helm: chart `hello-world`

## Obiettivo

Installare e personalizzare il chart di esempio ufficiale
[`hello-world`](https://github.com/helm/examples/tree/main/charts/hello-world)
di Helm usando un file di valori locale.

Il chart crea un Deployment Nginx, un Service ClusterIP, un ServiceAccount e le
probe HTTP di liveness e readiness.

## Prerequisiti

- Helm installato e disponibile nel `PATH`;
- `kubectl` configurato su un cluster Kubernetes;
- accesso a Internet per aggiungere il repository Helm.

Verificare gli strumenti:

    helm version
    kubectl cluster-info

## Aggiungere il repository Helm

Il progetto GitHub pubblica il chart anche come repository Helm. Lo aggiungiamo
con `helm repo add` e scarichiamo l'indice dei chart:

    helm repo add examples https://helm.github.io/examples
    helm repo update
    helm search repo examples/hello-world

## Esaminare e validare il chart

    helm show chart examples/hello-world
    helm show values examples/hello-world

Visualizzare i manifest generati senza installarli:

    helm template hello-world examples/hello-world \
      --namespace helm-demo \
      --create-namespace \
      --values values.yaml

## Installare il chart

    helm install hello-world examples/hello-world \
      --namespace helm-demo \
      --create-namespace \
      --values values.yaml

Controllare release e risorse create:

    helm list --namespace helm-demo
    kubectl get all --namespace helm-demo
    kubectl get pods --namespace helm-demo

Il Deployment deve avere due repliche, come specificato in `values.yaml`.

## Provare l'applicazione

Il Service è di tipo ClusterIP, quindi lo rendiamo temporaneamente accessibile
dal computer con il port-forward:

    kubectl port-forward --namespace helm-demo \
      service/hello-world 8080:80

In un secondo terminale:

    curl http://localhost:8080

Interrompere il port-forward con `Ctrl-C`.

## Upgrade del release

Cambiare `replicaCount` da `2` a `3` in `values.yaml`, poi eseguire:

    helm upgrade hello-world examples/hello-world \
      --namespace helm-demo \
      --values values.yaml

Verificare il nuovo numero di repliche:

    kubectl get deployment --namespace helm-demo
    helm get values hello-world --namespace helm-demo

## Ispezionare il release

    helm status hello-world --namespace helm-demo
    helm get manifest hello-world --namespace helm-demo
    helm history hello-world --namespace helm-demo

## Rollback

Visualizzare le revisioni disponibili:

    helm history hello-world --namespace helm-demo

Eseguire il rollback alla revisione precedente, sostituendo `REVISIONE` con il
numero mostrato da `helm history`:

    helm rollback hello-world REVISIONE --namespace helm-demo

## Pulizia

    helm uninstall hello-world --namespace helm-demo
    kubectl delete namespace helm-demo

## Esercizio finale

Modificare `values.yaml` per ottenere contemporaneamente:

1. quattro repliche Nginx;
2. il tag dell'immagine `1.27-alpine`;
3. un Service di tipo `NodePort`;
4. una nuova revisione tramite `helm upgrade`.

Verificare il risultato con:

    helm list --namespace helm-demo
    kubectl get deployment,service --namespace helm-demo
    helm history hello-world --namespace helm-demo
