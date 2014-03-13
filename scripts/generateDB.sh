#!/bin/bash
if [ -f "composer.json" ];
then
	php app/console doctrine:generate:entities TrazeoBaseBundle
	php app/console doctrine:schema:update --force
	php app/console doctrine:fixtures:load
	php app/console geonames:load:countries
	php app/console geonames:load:timezones
	#php app/console geonames:load:localities ES
	sh scripts/clean.sh
	php app/console fos:user:create hidabe fhidalgo@sopinet.com hidabe2013 --super-admin
	else
	echo "Error. Sitúese en el directorio raíz de Symfony";
fi
