#!/bin/bash

chmod 777 app/cache -R
chmod 777 app/logs -R
php app/console assets:install
chmod 777 app/cache -R
chmod 777 app/logs -R
