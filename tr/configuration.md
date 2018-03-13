
## Konfigürasyon

> Bu dosya router paketini yerel sunucunuzda nasıl kuracağınıza ilişkin bilgileri içerir.

### Apache web server

Apache konfigürasyon dosyanızda `DocumentRoot` konfigürasyonu `projeniz/demo/public/` klasörüne ayarlanmış olmalıdır.

```
<VirtualHost *:80>

        ServerAdmin webmaster@localhost
        DocumentRoot /var/www/Router/demo/public/

        ServerName router
        DirectoryIndex index.php

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined

</VirtualHost>
```

DirectoryIndex değerini `index.php` değerine ayarlamanız önerilir. ServerName bu örnekte `router` olarak ayarlanmıştır.


### .htaccess

Konfigurasyon aşağıdaki gibi `.htaccess` dosyasını gerektirir. Bu dosya `/public` klasöründe mevcuttur.

```
# Disable directory indexing

Options -Indexes
Options +FollowSymLinks
Options -MultiViews

## Windows xampp fix

DirectoryIndex index.php

RewriteEngine on
RewriteBase /

# Disables all access to files and directories, sends all request to index.php

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

RewriteRule ^(.*)$ index.php/$1 [L]
```

### Host dosyası

Bu örnekte `/etc/hosts` dosyasına proje router olarak tanımlanmıştır.

```
127.0.1.1       router
```

Bu tanımlamadan sonra tarayıcınızdan aşağıdaki gibi proje ismi ile örnek dosyalara ulaşabilirsiniz.

```
http://router/
```