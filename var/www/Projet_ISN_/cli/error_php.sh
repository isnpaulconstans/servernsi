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
php_ini="/etc/php/7.2/fpm/php.ini"
if [ "$1" == "on" ] ; then
    sed -i -e "s/error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_STRICT$/error_reporting = E_ALL/" "$php_ini"
    sed -i -e "s/display_errors = Off/display_errors = On/" "$php_ini"
else
    sed -i -e "s/error_reporting = E_ALL$/error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_STRICT/" "$php_ini"
    sed -i -e "s/display_errors = On/display_errors = Off/" "$php_ini"
fi
systemctl restart php7.2-fpm.service
echo "affichage des erreurs php $1"

