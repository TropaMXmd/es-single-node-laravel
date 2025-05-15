# Use an official PHP image with required extensions
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    git \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy existing application directory
COPY . /var/www

# Set permissions (optional: depends on your setup)
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

# Expose port (not strictly needed unless you're running directly from the container)
EXPOSE 9000

CMD ["php-fpm"]
