FROM php:7.4-apache

# Installiere benötigte PHP-Erweiterungen und GD-Abhängigkeiten
RUN apt-get update && \
    apt-get install -y \
    libzip-dev \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install mysqli zip gd && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html/

# Copy application files
COPY . /var/www/html/

# Permissions (vorsichtig mit 777 – lieber gezielter setzen)
RUN chmod -R 777 /var/www/html/

# Set environment variables
ENV FQDN=localhost
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
ENV APACHE_RUN_USER=www-data

# Expose Apache port
EXPOSE 80

# Start Apache server and set hostname
CMD ["/bin/sh", "-c", "echo \"$FQDN\" > /etc/hostname && echo \"127.0.0.1 $FQDN\" >> /etc/hosts && apache2-foreground"]