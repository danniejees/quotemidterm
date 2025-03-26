# Use an official PHP runtime as a parent image
FROM php:7.4-apache

# Set the working directory inside the container
WORKDIR /var/www/html

# Copy the local files to the container
COPY . .

# Install PostgreSQL client (needed for connecting to PostgreSQL)
RUN apt-get update && apt-get install -y libpq-dev

# Enable Apache mod_rewrite for URL rewriting (if needed for your PHP app)
RUN a2enmod rewrite

# Expose port 80
EXPOSE 80

# Start Apache service
CMD ["apache2-foreground"]
