#!/bin/bash
if [ -f "composer.json" ];
then
	php app/console doctrine:generate:entities TrazeoBaseBundle
	php app/console doctrine:generate:entities SopinetGamificationBundle
	php app/console doctrine:generate:entities SopinetChatBundle
	php app/console doctrine:generate:entities SopinetGCMBundle
	php app/console doctrine:schema:update --force
	php app/console doctrine:schema:update --force --env=prod
else
	echo "Error. Sitúese en el directorio raíz de Symfony";
fi
