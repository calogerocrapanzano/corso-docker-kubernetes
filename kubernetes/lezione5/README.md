## Distribuire l’applicazione

    kubectl apply -f myapp.yaml

## Distribuire il Service

    kubectl apply -f myservice.yaml

## Il Service è collegato al Pod?

In tal caso, l’endpoint punterà all’indirizzo IP del Pod. Visualizzare l’indirizzo IP del Pod:

    kubectl get po -o wide

Visualizzare quindi l’endpoint del Service. L’indirizzo IP dovrebbe corrispondere.

    kubectl get ep myservice

## Eseguire il port-forward verso il Service

    kubectl port-forward service/myservice 8080:80

Aprire un browser e visitare <http://localhost:8080>.

Interrompere il port-forward premendo **Ctrl-C**.

## Modificare il file YAML dell’applicazione

Modificare l’etichetta **app** impostandola a `myapp2`, quindi salvare il file.

Applicare la modifica:

    kubectl apply -f myapp.yaml

## Controllare nuovamente l’endpoint

    kubectl get ep myservice

## Eseguire nuovamente il port-forward verso il Service

    kubectl port-forward service/myservice 8080:80

Aprire un browser e visitare <http://localhost:8080>.

Interrompere il port-forward premendo **Ctrl-C**.

## Pulizia

    kubectl delete -f myservice.yaml
    kubectl delete -f myapp.yaml
