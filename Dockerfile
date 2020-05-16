FROM php:7.1-apache

FROM composer:1.10

RUN docker-php-ext-install mysqli pdo pdo_mysql

EXPOSE 3306 8000

CMD bash
