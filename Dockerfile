FROM php:8.4-fpm

# Install Nginx dan dependensi yang dibutuhkan
RUN apt-get update && apt-get install -y \
    nginx \
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

# Build aset CSS/JS & Filament
RUN npm install && npm run build
RUN php artisan filament:assets || true

# Buat file database.sqlite & symlink storage
RUN touch database/database.sqlite
RUN php artisan storage:link || true
RUN chmod -R 777 storage bootstrap/cache database public

# Konfigurasi Nginx ringkas
RUN echo 'server { \
    listen 8000; \
    index index.php index.html; \
    root /var/www/public; \
    location / { \
        try_files $uri $uri/ /index.php?$query_string; \
    } \
    location ~ \.php$ { \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_index index.php; \
        include fastcgi_params; \
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
    } \
}' > /etc/nginx/sites-available/default

EXPOSE 8000

# Jalankan PHP-FPM dan Nginx secara bersamaan
CMD ["sh", "-c", "php artisan migrate --force && php artisan optimize:clear && php-fpm -D && nginx -g 'daemon off;'"]