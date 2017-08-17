trazeo
======

Web + Mobile application to recover the way to school on foote

TRAZEO
======
Development funded initially by the CrowdFunding campaign of the Trazeo project

Requeriments
============
- php 5.4 or more
- mysql 5.5 (error in 5.6 or superior)
- php-mbstring extension: ```sudo apt-get install php5.6-mbstring```
- php-intl extension: ```sudo apt-get install php5.6-intl```
- php-amqplib: ```sudo apt-get install php-amqplib```

- Supervisor
```
apt-get install supervisor
app/console rabbitmq-supervisor:rebuild
```

Turn on RabbitMQ (to proccess messages in Background)
=====================================================

rabbitmq-server 

./app/console rabbitmq:consumer send_trazeo

INSTALLATION
============

1. Download source code

2. Update composer and clear cache, configure

3. Installation of system libraries: intl, curl, rabbit-mq-server

4. Run message script

./app/console rabbitmq:consumer send_trazeo

phpStorm
========

Thanks!

![Image of PHPStorm](phpstorm_logo_png.png)
