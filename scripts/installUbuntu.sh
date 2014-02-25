# Installing imagick: http://devilsworkshop.org/tutorial/php54-imagemagick-pecl-installation/61444/
apt-get install php-pear php5-dev imagemagick php5-imagick
apt-get install libmagickwand-dev # libmagick9-dev
pecl install imagick # pear config-set preferred_state beta
# Add to PHP.ini: extension=imagick.so
