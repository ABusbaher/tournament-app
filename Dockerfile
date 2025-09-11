# Use the official PHP image as a base
FROM php:8.1-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    bash \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath \
    && docker-php-ext-enable pdo_mysql

RUN docker-php-ext-configure gd --with-freetype --with-jpeg && docker-php-ext-install gd

# Install Composer
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

# -------------------------------
# Install NVM + Node.js 18.17.1
# -------------------------------
ENV NVM_DIR=/root/.nvm
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash \
    && . "$NVM_DIR/nvm.sh" \
    && nvm install 18.17.1 \
    && nvm alias default 18.17.1 \
    && nvm use default

# Make Node & npm available globally in PATH
ENV PATH=$NVM_DIR/versions/node/v18.17.1/bin:$PATH

COPY composer.json composer.lock ./

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy existing application directory contents
COPY . .

# Set file permissions
RUN chown -R www-data:www-data /var/www

# Expose port 9000 and start PHP-FPM server
EXPOSE 9000
CMD ["php-fpm"]
