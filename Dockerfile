FROM php:8.2-fpm

# تثبيت التبعيات المطلوبة
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim unzip git curl \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# نسخ ملفات المشروع
WORKDIR /var/www
COPY . .

# إعداد Laravel
RUN composer install
RUN php artisan config:cache
RUN php artisan route:cache

# إعداد صلاحيات المجلدات
RUN chmod -R 775 storage bootstrap/cache

# فتح البورت المناسب لـ Render
EXPOSE 8080

# أمر التشغيل
CMD php artisan serve --host=0.0.0.0 --port=8080
