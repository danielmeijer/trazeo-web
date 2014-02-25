cd /var/www/dev/suspasitos2
git pull
rsync -a * root@trazeo.es:/var/www/vhosts/ns3000631.ovh.net/dev-suspasitos.es --exclude 'app/cache' --exclude 'app/config/parameters.yml' --exclude 'vendor' --exclude 'app/logs'
