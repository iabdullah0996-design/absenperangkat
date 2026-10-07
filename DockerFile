FROM php:8.4-fpm

# Install dependensi sistem, Node.js, npm, SQLite, & ekstensi PHP
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    sqlite3 \
    libsqlite3-dev \
    zip \
    unzip \
    git \
    curl \
    nodejs \
    npm

# Install ekstensi PHP (termasuk pdo_sqlite untuk database SQLite)
RUN docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd

# Set working directory
WORKDIR /var/www

# Copy kodingan proyek
COPY . /var/www

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Buat file database.sqlite jika belum ada & beri izin akses folder
RUN touch database/database.sqlite
RUN chmod -R 777 storage bootstrap/cache database

EXPOSE 8000

# Jalankan auto-migrate database lalu jalankan server
CMD ["sh", "-c", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000"]