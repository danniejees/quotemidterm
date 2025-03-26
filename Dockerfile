# Start with a PHP image
FROM php:8.1-apache

# Install dependencies for PostgreSQL PDO
RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer (optional if you are using it for dependencies)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Expose the default port for Apache
EXPOSE 80
