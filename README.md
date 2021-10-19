# Zend Framework test-assignment
- PHP ^7.3 with pdo_mysql module
- MySQL
- Doctrine
- Vue.js 

### Setup (non-docker)

##### * Install the framework and all the dependencies
composer install

##### * Use your own database credentials
mysql -uadmin -e "CREATE DATABASE test_ek_2021;"

##### * Edit config/autoload/local.php (remove the .dist filename extension if it doesnt exist) 
edit database login credentials in config/autoload/local.php under "connection"

##### * Generate DB schema from entities (annotation metadata)
php vendor/bin/doctrine orm:schema-tool:create

##### * Use php built in web server. This shortcut is defined in composer.json
composer serve
