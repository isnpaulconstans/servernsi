#! /bin/bash

if [ "$UID" -ne "0" ]
then
  echo "Il faut etre root pour executer ce script. ==> sudo "
  exit
fi

read -p "Installation de nginx "
apt-get install nginx php-fpm php-sqlite3 php-zip
php_ini="/etc/php/7.4/fpm/php.ini"
sed -i -e "s/upload_max_filesize = .*$/upload_max_filesize = 1G/" "$php_ini"
sed -i -e "s/post_max_size = .*$/post_max_size = 1G/" "$php_ini"
sed -i -e "s/memory_limit = .*$/memory_limit = 1G/" "$php_ini"
sed -i -e "s/;date.timezone =.*$/date.timezone = Europe\/Paris/" "$php_ini"
systemctl restart php7.4-fpm.service

read -p "config du site NSI "
rm -rf /var/www
ln -s /home/profnsi/install/www /var
cp nsi.conf /etc/nginx/sites-available/
cp gitea.conf /etc/nginx/sites-available/
ln -s /etc/ngnix/sites-available/nsi.conf /etc/nginx/sites-enabled/
ln -s /etc/ngnix/sites-available/gitea.conf /etc/nginx/sites-enabled/

read -p "config https"
apt-get install certbot python3-certbot-nginx
certbot --nginx

read -p "config de gitea"
cd gitea
install.sh
cd ..

read -p "config de Jirafeau"
mkdir -p /home/Jirafeau/var-lNFvvoew5am7mWa/{files,links,async}
chown -R www-data:www-data /home/Jirafeau/

read -p "config de Jupyter"
sed -i -e "s/#NAME_REGEX=.*/NAME_REGEX=\"^[a-z][-a-z0-9_.]*[a-z0-9_]$\"/" "/etc/adduser.conf"
.
./setup_nbgrader.py install
./setup_nbgrader.py add course 1nsi
./setup_nbgrader.py add course tnsi
./setup_nbgrader.py add teacher delay.e 1nsi --password 'manu!42'
./setup_nbgrader.py add teacher delay.e tnsi --password 'manu!42'
# TODO ajouter les élèves 

