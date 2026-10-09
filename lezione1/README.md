# Lezione 1 — Una pagina web con Docker e Nginx

In questa esercitazione eseguiamo una pagina HTML dentro un container Docker
basato sull'immagine ufficiale di **Nginx**.

## Obiettivi

- distinguere immagine, container e porta pubblicata;
- creare un'immagine con un `Dockerfile`;
- avviare, osservare e fermare un container;
- accedere alla shell di un container in esecuzione.

## Prerequisiti

- Docker Desktop oppure Docker Engine avviato;
- un terminale aperto nella directory `lezione1`.

Verificare l'installazione:

```bash
docker --version
docker run --rm hello-world
```

## 1. Esaminare i file dell'esempio

```text
lezione1/
├── .dockerignore
├── Dockerfile
├── compose.yaml
└── index.html
```

`index.html` è la pagina da pubblicare. Il `Dockerfile` parte da Nginx e copia
la pagina nella directory servita dal web server.

## 2. Costruire l'immagine

```bash
docker build -t lezione1-nginx:1.0 .
```

Controllare che l'immagine sia presente:

```bash
docker image ls lezione1-nginx
```

## 3. Avviare il container

```bash
docker run --name lezione1-web -d -p 8080:80 lezione1-nginx:1.0
```

La sintassi della pubblicazione è `porta-del-computer:porta-del-container`.
In questo caso la porta `8080` del computer viene collegata alla porta `80`
di Nginx.

Aprire [http://localhost:8080](http://localhost:8080) nel browser. Se si usa
una macchina virtuale o un ambiente remoto, usare l'indirizzo IP dell'host e
la porta `8080` (ad esempio `http://IP_HOST:8080`), verificando anche le regole
del firewall.

## 4. Osservare il container

```bash
docker ps
docker logs lezione1-web
docker port lezione1-web
```

Per provare la pagina dal terminale:

```bash
curl http://localhost:8080
```

## 5. Accedere alla shell

```bash
docker exec -it lezione1-web sh
```

Dentro al container:

```sh
ls -la /usr/share/nginx/html
cat /usr/share/nginx/html/index.html
exit
```

`docker exec` apre un nuovo processo nel container già in esecuzione; `exit`
chiude la shell, ma non ferma il container.

## 6. Fermare e rimuovere il container

```bash
docker stop lezione1-web
docker rm lezione1-web
```

L'immagine resta disponibile e può essere riutilizzata. Per rimuoverla:

```bash
docker rmi lezione1-nginx:1.0
```

## Variante con Docker Compose

```bash
docker compose up --build -d
docker compose ps
docker compose logs
```

La pagina è disponibile su [http://localhost:8080](http://localhost:8080).
Terminata l'esercitazione:

```bash
docker compose down
```

## Esercizi

1. Modificare `index.html`, ricostruire l'immagine e verificare il risultato.
2. Pubblicare il servizio sulla porta `9090` usando `-p 9090:80`.
3. Avviare un secondo container con un nome diverso e osservare cosa succede
   se si prova a usare la stessa porta dell'host.
4. Confrontare `docker ps` e `docker ps -a` dopo aver fermato il container.

## Risoluzione dei problemi

- **La porta è già occupata:** usare una porta diversa, per esempio
  `-p 8081:80`.
- **Il browser non raggiunge la pagina:** verificare che il container sia in
  esecuzione con `docker ps` e che l'URL includa la porta corretta.
- **La modifica dell'HTML non appare:** dopo aver modificato il file eseguire
  di nuovo `docker build` e ricreare il container.
