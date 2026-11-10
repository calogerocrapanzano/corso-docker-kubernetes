# Immagine di base con PHP CLI, sufficiente per eseguire il server di sviluppo
# integrato in Laravel (php artisan serve).
FROM php:8.4-cli

# Installa le dipendenze di sistema e le estensioni PHP necessarie a Laravel:
# - pdo_mysql: connessione a MariaDB/MySQL
# - mbstring, bcmath e intl: funzioni usate da Laravel e dalle sue dipendenze
# - libzip/unzip/git: supporto all'installazione dei pacchetti Composer
# Infine elimina la cache di apt per ridurre la dimensione dell'immagine.
RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip libicu-dev libzip-dev libonig-dev \
    && docker-php-ext-install pdo_mysql mbstring bcmath intl \
    && rm -rf /var/lib/apt/lists/*

# Recupera Composer dall'immagine ufficiale e lo rende disponibile nel PATH.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Imposta la directory di lavoro dell'applicazione Laravel.
WORKDIR /var/www/html

# Crea in anticipo alcune directory che possono essere vuote nel progetto.
RUN mkdir -p database/seeders database/factories

# Copia prima i file delle dipendenze: Docker può riutilizzare la cache di questo
# layer quando cambia solo il codice dell'applicazione.
COPY composer.json composer.lock* ./

# Installa le dipendenze PHP senza interazione e senza eseguire gli script Laravel
# prima che tutto il codice dell'app sia stato copiato.
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Copia nell'immagine il resto del progetto Laravel.
COPY . .

# Prepara Laravel all'avvio:
# - crea le directory usate da cache, sessioni, viste e bootstrap
# - assegna i permessi necessari all'utente del processo
# - crea temporaneamente il file .env e genera APP_KEY
# - ottimizza l'autoloader di Composer
# - rimuove .env per evitare di incorporare configurazioni/segreti nell'immagine
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache \
    && cp .env.example .env \
    && php artisan key:generate --force \
    && composer dump-autoload --optimize \
    && rm -f .env

# Documenta la porta usata dal server Laravel (la pubblicazione vera e propria
# viene configurata nel Deployment/Service Kubernetes).
EXPOSE 8000

# Avvia il server di sviluppo Laravel ascoltando su tutte le interfacce del pod.
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
