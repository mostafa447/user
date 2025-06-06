FROM php:8.1-apache

# انسخ ملفات موقعك إلى مجلد الاستضافة داخل الحاوية
COPY . /var/www/html/

# أعط التصاريح اللازمة
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80
