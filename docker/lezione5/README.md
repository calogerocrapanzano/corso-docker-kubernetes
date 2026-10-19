# Lezione 5 — Persistenza del database con un volume Docker

In questa esercitazione riprendiamo l'esempio della lezione 4 e aggiungiamo
la persistenza dei dati. MySQL salva i propri file in un volume Docker
nominato. I dati restano quindi disponibili anche quando i container vengono
rimossi e ricreati.

## Obiettivi

- usare un volume nominato in Docker Compose;
- montare il volume nella directory dati di MySQL;
- distinguere container, rete e volume;
- verificare che i dati sopravvivano alla ricreazione dei container;
- capire la differenza tra `docker compose down` e `docker compose down -v`.

## Prerequisiti

- Docker Desktop oppure Docker Engine avviato;
- un browser web;
- un terminale aperto nella directory `lezione5`.

Verificare l'installazione:

```bash
docker --version
docker compose version
```

## 1. Esaminare la configurazione

La directory contiene:

```text
lezione5/
├── compose.yaml
└── README.md
```

Il file `compose.yaml` definisce due servizi:

- `db`, basato sull'immagine `mysql:8.0`;
- `adminer`, basato sull'immagine `adminer`.

Per il servizio `db` è definito anche il volume nominato
`lezione5-mysql-data`, montato nella directory `/var/lib/mysql`. È la
directory in cui MySQL memorizza i dati del database.

Controllare la configurazione senza avviare i servizi:

```bash
docker compose config
```

## 2. Avviare i servizi

```bash
docker compose up -d
```

Compose crea automaticamente la rete del progetto, il volume nominato e i due
container. Controllare lo stato:

```bash
docker compose ps
```

MySQL potrebbe impiegare alcuni secondi per completare l'avvio. Controllare i
log con:

```bash
docker compose logs -f db
```

Quando compare un messaggio simile a `ready for connections`, premere
`Ctrl+C`. I servizi continueranno a funzionare.

## 3. Collegarsi ad Adminer

Aprire [http://localhost:8080](http://localhost:8080) e usare questi valori:

```text
Sistema: MySQL
Server: db
Utente: root
Password: rootpass
Database: scuola
```

Il server è `db`, cioè il nome del servizio MySQL nella rete Docker Compose.
Non usare `localhost`: dentro Adminer indicherebbe il container Adminer stesso.

## 4. Creare una tabella e inserire i dati

In Adminer scegliere **SQL command** ed eseguire:

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

Verificare che vengano visualizzati i due studenti.

## 5. Osservare il volume

Elencare i volumi Docker:

```bash
docker volume ls
```

Tra i risultati deve comparire `lezione5-mysql-data`. Per visualizzarne i
dettagli:

```bash
docker volume inspect lezione5-mysql-data
```

Verificare anche il montaggio dal punto di vista di Compose:

```bash
docker compose config
```

Nella configurazione deve comparire il collegamento tra
`mysql-data` e `/var/lib/mysql`.

## 6. Verificare la persistenza

Con i dati ancora presenti nel database, rimuovere i container e la rete:

```bash
docker compose down
```

Il volume non viene rimosso. Controllare che esista ancora:

```bash
docker volume ls
```

Ricreare i servizi:

```bash
docker compose up -d
docker compose ps
```

Attendere che MySQL sia pronto, accedere nuovamente ad Adminer e verificare
che la tabella `studenti` contenga ancora i due record. I container sono nuovi,
ma MySQL riutilizza i dati salvati nel volume.

## 7. Eliminare anche i dati

Per fermare e rimuovere i container, la rete e il volume usare:

```bash
docker compose down -v
```

L'opzione `-v` rimuove il volume nominato. Al successivo avvio MySQL creerà un
database vuoto:

```bash
docker compose up -d
```

La tabella `studenti` non sarà più presente.

Usare `docker compose down -v` solo quando si desidera eliminare
esplicitamente i dati.

## Confronto con la lezione 4

Nella lezione 4 MySQL non usava un volume. Di conseguenza, dopo
`docker compose down`, la rimozione del container eliminava anche i dati.

Nella lezione 5:

- `docker compose down` rimuove i container e la rete, ma conserva il volume;
- `docker compose up -d` ricrea i container usando i dati già salvati;
- `docker compose down -v` rimuove anche il volume e i dati del database.

## Riepilogo

- un container è temporaneo, mentre un volume può conservare i dati;
- il volume viene montato in `/var/lib/mysql`;
- il nome del volume è `lezione5-mysql-data`;
- `docker compose down` conserva il volume;
- `docker compose down -v` elimina il volume e i dati;
- Adminer continua a raggiungere MySQL usando il nome del servizio `db`.
