# Lezione 3 — Un server comunica con un database

In questa esercitazione avviamo due container: Adminer, che fornisce una semplice interfaccia web, e MySQL, che contiene il database. I due container comunicano attraverso una rete Docker privata.

## Obiettivi

- creare una rete Docker;
- avviare due container sulla stessa rete;
- collegare Adminer al database MySQL usando il nome del container;
- creare una tabella e inserire alcuni dati;
- capire che i container possono comunicare tra loro senza usare `localhost`.

## Prerequisiti

- Docker Desktop oppure Docker Engine avviato;
- un browser web;
- un terminale aperto nella directory `lezione3`.

Verificare l'installazione:

```bash
docker --version
```

## 1. Creare la rete Docker

Creiamo una rete privata chiamata `lezione3-rete`:

```bash
docker network create lezione3-rete
```

I container collegati a questa rete potranno comunicare usando il nome degli altri container.

## 2. Avviare il database MySQL

```bash
docker run -d \
  --name lezione3-db \
  --network lezione3-rete \
  -e MYSQL_ROOT_PASSWORD=rootpass \
  -e MYSQL_DATABASE=scuola \
  mysql:8.0
```

MySQL potrebbe impiegare alcuni secondi per completare l'avvio. Controllare i log:

```bash
docker logs -f lezione3-db
```

Quando nei log compare un messaggio simile a `ready for connections`, premere `Ctrl+C` per tornare al terminale. Il container continuerà a funzionare.

## 3. Avviare Adminer

```bash
docker run -d \
  --name lezione3-adminer \
  --network lezione3-rete \
  -p 8080:8080 \
  adminer
```

Controllare che i due container siano in esecuzione:

```bash
docker ps
```

## 4. Collegarsi al database dal browser

Aprire:

<http://localhost:8080>

Compilare il modulo di Adminer con questi valori:

```text
Sistema: MySQL
Server: lezione3-db
Utente: root
Password: rootpass
Database: scuola
```

Il campo **Server** deve contenere `lezione3-db`, cioè il nome del container MySQL. Non bisogna usare `localhost`: dentro Adminer, `localhost` indicherebbe il container Adminer stesso.

## 5. Creare una tabella

Dopo il login, scegliere **SQL command** e inserire:

```sql
CREATE TABLE studenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    corso VARCHAR(100) NOT NULL
);

INSERT INTO studenti (nome, corso) VALUES
    ('Anna Rossi', 'Docker'),
    ('Luca Bianchi', 'Kubernetes');

SELECT * FROM studenti;
```

Eseguire il comando e verificare che vengano visualizzati i due studenti.

## 6. Osservare la rete

```bash
docker network inspect lezione3-rete
```

Nella sezione `Containers` devono comparire sia `lezione3-db` sia `lezione3-adminer`.

Il concetto importante è:

> Adminer è il server che presenta la pagina web. MySQL è il database. Adminer raggiunge MySQL usando il nome `lezione3-db`, perché i due container appartengono alla stessa rete Docker.

La porta `8080` è pubblicata sul computer solo per permettere al browser di raggiungere Adminer. La comunicazione tra Adminer e MySQL avviene invece direttamente sulla rete Docker.

## 7. Pulizia finale

Questo esempio non usa un volume: rimuovendo il container MySQL si eliminano anche i dati creati durante l'esercitazione.

```bash
docker rm -f lezione3-adminer lezione3-db
docker network rm lezione3-rete
```

## Riepilogo

- una rete Docker permette ai container di comunicare;
- i container sulla stessa rete possono raggiungersi usando il loro nome;
- `localhost` indica sempre il container o il computer dal quale viene usato;
- la porta pubblicata serve per collegare il browser al container Adminer;
- in questa esercitazione il database è temporaneo e non persistente.
