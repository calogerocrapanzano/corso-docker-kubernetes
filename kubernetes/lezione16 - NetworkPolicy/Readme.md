# NetworkPolicy

## Prerequisito

Le NetworkPolicy vengono applicate dal plugin CNI del cluster. Verificare che
il plugin utilizzato supporti NetworkPolicy; in caso contrario le risorse
possono essere accettate dall'API Server, ma non avere effetto sul traffico.

**NON SU DOCKER DESKTOP**

## Creazione delle risorse

Creiamo il namespace dedicato e applichiamo tutte le risorse al suo interno:

    kubectl apply -f namespace.yaml
    kubectl apply -f server.yaml
    kubectl apply -f clients.yaml

Attendere che i Pod siano pronti:

    kubectl get pods -n test -o wide
    kubectl get service networkpolicy-server -n test

## Applicazione delle NetworkPolicy

    kubectl apply -f networkpolicy.yaml
    kubectl get networkpolicy -n test
    kubectl describe networkpolicy networkpolicy-server-allow-client -n test

La prima policy seleziona il server e abilita l'isolamento dell'ingresso. Le
altre policy aggiungono soltanto i flussi autorizzati: il client con
`access=allowed` può raggiungere il server sulla porta TCP 80, mentre il Pod con
`access=denied` non è autorizzato.

## Test dal client autorizzato

Il client autorizzato raggiunge il server

    kubectl exec -n test networkpolicy-client -- wget -qO- "http://networkpolicy-server"

Il comando deve stampare la pagina iniziale di Nginx.

## Test dal client non autorizzato

    kubectl exec -n test networkpolicy-attacker -- wget -T 3 -qO- http://networkpolicy-server

La richiesta deve fallire o andare in timeout: il client non possiede la label
`access=allowed` richiesta dalla policy di ingresso.


## Test dell'egress

Il terzo Pod ha una policy di default deny sull'egress. Può raggiungere il
server, ma non può effettuare richieste verso Internet:

    SERVICE_IP=$(kubectl get service networkpolicy-server -n test -o jsonpath='{.spec.clusterIP}')
    kubectl exec -n test networkpolicy-egress-client -- curl -sS "http://${SERVICE_IP}"
    kubectl exec -n test networkpolicy-egress-client -- curl -I --connect-timeout 3 https://www.google.com
    kubectl exec -n test networkpolicy-egress-client -- curl -I --connect-timeout 3 https://147.163.149.59 (unipa.it)

La prima richiesta deve riuscire; la seconda deve fallire o andare in timeout.


## Verifica delle label

    kubectl get pods -n test --show-labels

Le label sono il criterio con cui le NetworkPolicy selezionano i Pod autorizzati.

## Pulizia

    kubectl delete -f networkpolicy.yaml
    kubectl delete -f clients.yaml
    kubectl delete -f server.yaml

