trazeo
======

Web + Mobile application to recover the way to school on foote

TRAZEO
======
Development funded initially by the CrowdFunding campaign of the Trazeo project

Requeriments
============
- php 5.4 or more
- php-mbstring extension: ```sudo apt-get install php5.6-mbstring```

Turn on RabbitMQ (to proccess messages in Background)
=====================================================

rabbitmq-server 

./app/console rabbitmq:consumer send_trazeo

INSTALATION
===========

1. Download source code

2. Update composer and clear cache, configure

3. Installation of system libraries: intl, curl, rabbit-mq-server

4. Run message script

./app/console rabbitmq:consumer send_trazeo

phpStorm
========

Thanks!

![Image of PHPStorm](phpstorm_logo_png.png)
