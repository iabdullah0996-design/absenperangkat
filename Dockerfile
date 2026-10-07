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

# Install ekstensi PHP
RUN docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd

# Set working directory
WORKDIR /var/www

# Copy kodingan proyek
COPY . /var/www

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Build aset CSS/JS frontend & publish aset Filament
RUN npm install && npm run build
RUN php artisan filament:assets || true

# Buat file database.sqlite & symlink storage
RUN touch database/database.sqlite
RUN php artisan storage:link || true
RUN chmod -R 777 storage bootstrap/cache database public

EXPOSE 8000

# Jalankan auto-migrate database & optimize lalu start server
CMD ["sh", "-c", "php artisan migrate --force && php artisan optimize:clear && php artisan serve --host=0.0.0.0 --port=8000"]