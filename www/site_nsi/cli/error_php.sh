#! /bin/bash

if [ "$UID" -ne "0" ]
then
  echo "Il faut etre root pour executer ce script. ==> sudo "
  exit
fi

if [ "$1" != "on" ] && [ "$1" != "off" ] ; then
  echo "usage $0 on|off"
  echo "pour afficher ou pas les erreurs php"
  exit
fi
php_ver='7.4'
php_ini="/etc/php/${php_ver}/fpm/php.ini"
if [ "$1" == "on" ] ; then
    sed -i -e "s/error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_STRICT$/error_reporting = E_ALL/" "$php_ini"
    sed -i -e "s/display_errors = Off/display_errors = On/" "$php_ini"
    sed -i -e "s/\/\/\$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION)/\$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION)/" /var/www/site_nsi/src/models/Database.php
else
    sed -i -e "s/error_reporting = E_ALL$/error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_STRICT/" "$php_ini"
    sed -i -e "s/display_errors = On/display_errors = Off/" "$php_ini"
    sed -i -e "s/\$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION)/\/\/\$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION)/" /var/www/site_nsi/src/models/Database.php
fi
systemctl restart php${php_ver}-fpm.service
echo "affichage des erreurs php $1"
