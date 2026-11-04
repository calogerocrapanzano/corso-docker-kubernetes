# Secret

Per codificare/decodificare rapidamente stringhe in base64

    https://www.base64encode.org/
    https://www.base64decode.org/

oppure, su Windows, installa base64

    choco install base64

su Linux/Mac

    echo [string] | base64
    echo [encodedString] | base64 -d

## Creare i Secret

    kubectl apply -f secrets.yaml

## Visualizzare i Secret

    kubectl get secret
    kubectl describe secret secrets
    kubectl get secret secrets -o YAML

## Creare il pod

    kubectl apply -f pod.yaml

## Connettersi a BusyBox

    kubectl exec mybox -it -- /bin/sh

## Visualizzare le variabili d'ambiente USERNAME e PASSWORD

    echo $USERNAME
    echo $PASSWORD
    exit

## Pulizia

    kubectl delete -f secrets.yaml
    kubectl delete -f pod.yaml --force --grace-period=0
