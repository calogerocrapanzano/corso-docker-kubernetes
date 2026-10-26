# Lezione 1

## Contesto corrente

Visualizzare il contesto corrente:

    kubectl config current-context

## Elenco dei contesti

Elencare tutti i contesti:

    kubectl config get-contexts

## Cambiare contesto

Utilizzare un contesto:

    kubectl config use-context [contextName]

## Informazioni sui nodi

Visualizzare l’elenco di tutti i nodi installati. Con Docker Desktop dovrebbe essercene soltanto uno.

    kubectl get nodes

Visualizzare alcune informazioni sul nodo:

    kubectl describe node
