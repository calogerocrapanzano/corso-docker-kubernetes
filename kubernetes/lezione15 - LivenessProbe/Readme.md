# Probe di vitalità (Liveness Probe)

## Creazione del pod

    kubectl apply -f pod.yaml

## Visualizzare gli eventi del pod

    kubectl describe pod liveness-example

## Pulizia

    kubectl delete -f pod.yaml --force --grace-period=0
