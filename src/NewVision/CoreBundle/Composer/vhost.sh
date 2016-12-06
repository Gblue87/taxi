#!/bin/bash

# This script creates virtual hosts

# Set the path to your localhost
www="/home/ntumbalev/Projects/"
read -p "Please enter your www dir (default=$www): " input
www="${input:-$www}"

echo "Enter directory name under $www"
while [ -z "$sn" ]
do
    read sn
done

# Create the file with VirtualHost configuration in /etc/apache2/site-available/
echo "<VirtualHost *:80>
        ServerName $sn.localhost
        DocumentRoot $www$sn/web/
        <Directory $www$sn/web/>
                Options Indexes FollowSymLinks Includes ExecCGI
                AllowOverride All
                Order allow,deny
                allow from all
                Require all granted
        </Directory>
</VirtualHost>" > /etc/apache2/sites-available/$sn.conf

# Add the host to the hosts file
echo 127.0.0.1 $sn.localhost >> /etc/hosts

# Enable the site
a2ensite $sn

# Reload Apache2
/etc/init.d/apache2 restart

echo "Your new site is available at http://$sn.localhost"
