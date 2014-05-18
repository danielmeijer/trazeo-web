#!/bin/bash
if [ -f "composer.json" ];
then
	php app/console doctrine:generate:entities TrazeoBaseBundle
	php app/console doctrine:schema:update --force
	php app/console doctrine:fixtures:load --fixtures=src/Trazeo/BaseBundle/DataFixtures/ORM/
	php app/console geonames:load:countries
	php app/console geonames:load:timezones
	#php app/console geonames:load:localities ES
	php app/console fos:user:create hidabe fhidalgo@sopinet.com hidabe2013 --super-admin
sh scripts/clean.sh	
else
	echo "Error. Sitúese en el directorio raíz de Symfony";
fi
