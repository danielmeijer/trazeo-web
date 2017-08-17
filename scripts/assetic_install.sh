#!/bin/bash

chmod 777 app/cache -R
chmod 777 app/logs -R
php app/console assets:install
php app/console assetic:dump
chmod 777 app/cache -R
chmod 777 app/logs -R
