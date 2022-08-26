lftp sftp://opnza00001@uatpartner.avios.com/ -e "mirror -R /var/www/html/open-web.loc/lftp/inbound/ /inbound; bye";
php /var/www/html/open-web.loc/index.php crons synclog