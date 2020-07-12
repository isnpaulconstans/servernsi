#! /usr/bin/bash
# wget -O gitea https://dl.gitea.io/gitea/1.12.1/gitea-1.12.1-linux-amd64
# wget -O gitea.asc https://dl.gitea.io/gitea/1.12.1/gitea-1.12.1-linux-amd64.asc
# gpg --keyserver keys.openpgp.org --recv 7C9E68152594688862D62AF62D9AE806EC1592E2
# gpg --verify gitea.asc gitea
# rm gitea.asc

adduser \
   --system \
   --shell /bin/bash \
   --gecos 'Git Version Control' \
   --group \
   --disabled-password \
   --home /home/gitea \
   gitea

cp -r home/* /home/gitea/
#mkdir -p /var/lib/gitea/{custom,data,log}
cp -r lib /var/lib/gitea

chown -R gitea:gitea /var/lib/gitea/
chmod -R 750 /var/lib/gitea/
mkdir /etc/gitea
chown root:gitea /etc/gitea
chmod 770 /etc/gitea

chmod +x gitea
mv gitea /usr/local/bin/gitea

cp app.ini /etc/gitea/
cp gitea.service /etc/systemd/system/
sudo systemctl enable gitea
sudo systemctl start gitea

