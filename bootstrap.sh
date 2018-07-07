debconf-set-selections <<< 'mysql-server mysql-server/root_password password root'
debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password root'

#install necessary software
sudo apt-get update
sudo apt-get install -y apache2 php libapache2-mod-php mysql-server php-pdo-mysql phpunit unzip composer
sudo apt-get upgrade -y

sudo a2enmod rewrite

# update MySQL to not escape slashes
sudo cat > /etc/mysql/conf.d/mysqld_no_backslash_escape.cnf<<EOD
[mysqld]
sql_mode='NO_BACKSLASH_ESCAPE'
EOD
#remove bind address
sudo sed -i '/bind-address/d' /etc/mysql/mysql.conf.d/mysqld.cnf
# restart MySQL
service mysql restart
# And we're done!

#set up test pholdbox database
mysql --user=root --password=root < /vagrant/schema.sql

#set up permissions
mysql --user=root --password=root -e "grant all PRIVILEGES on *.* to 'root'@'%' IDENTIFIED BY 'root' WITH GRANT OPTION;"

#set up apache config
sudo echo '<VirtualHost *:80>' > /etc/apache2/sites-available/pholdbox.local.dev.conf
sudo echo '       ServerAdmin webmaster@localhost' >> /etc/apache2/sites-available/pholdbox.local.dev.conf
sudo echo '       DocumentRoot /vagrant/webroot' >> /etc/apache2/sites-available/pholdbox.local.dev.conf
sudo echo '       <Directory "/vagrant/webroot">' >> /etc/apache2/sites-available/pholdbox.local.dev.conf
sudo echo '               Options All Indexes' >> /etc/apache2/sites-available/pholdbox.local.dev.conf
sudo echo '               Order Allow,Deny' >> /etc/apache2/sites-available/pholdbox.local.dev.conf
sudo echo '               Allow From All' >> /etc/apache2/sites-available/pholdbox.local.dev.conf
sudo echo '               Require all granted' >> /etc/apache2/sites-available/pholdbox.local.dev.conf
sudo echo '               AllowOverride All' >> /etc/apache2/sites-available/pholdbox.local.dev.conf
sudo echo '       </Directory>' >> /etc/apache2/sites-available/pholdbox.local.dev.conf
sudo echo '       ErrorLog ${APACHE_LOG_DIR}/pholdbox.error.log' >> /etc/apache2/sites-available/pholdbox.local.dev.conf
sudo echo '       CustomLog ${APACHE_LOG_DIR}/pholdbox.access.log combined' >> /etc/apache2/sites-available/pholdbox.local.dev.conf
sudo echo '</VirtualHost>' >> /etc/apache2/sites-available/pholdbox.local.dev.conf

sudo ln -s /etc/apache2/sites-available/pholdbox.local.dev.conf /etc/apache2/sites-enabled/pholdbox.local.dev.conf
sudo rm /etc/apache2/sites-enabled/000-default.conf
sudo touch /var/www/html/php_errors.log
sudo chown vagrant:vagrant /var/www/html/php_errors.log
sudo chmod 777 /var/www/html/php_errors.log
sudo sed -i 's/;error_log = php_errors\.log/error_log = \/var\/www\/html\/php_errors.log/' /etc/php/7.0/apache2/php.ini
sudo service apache2 restart
