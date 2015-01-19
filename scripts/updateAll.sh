#!/bin/bash
if [ -f "composer.json" ];
then
	sudo sh scripts/clean.sh;
	sh scripts/updateAssets.sh;
	sudo chmod 777 /var/www/trazeo-web/app/cache/dev/jms_diextra/metadata;
else
	echo "Error. Sitúese en el directorio raíz de Symfony";
fi
