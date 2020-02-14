FROM ubuntu:16.04

# First update Ubuntu
RUN apt-get update \

# Install System-Packages
&& apt-get install -y composer zip sudo \

# Install PHP with necessary packages for Opus4
&& apt-get install -y php\
    php-cli\
    php-dev\
    php-xdebug
