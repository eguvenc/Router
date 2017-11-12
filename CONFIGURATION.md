

### Konfigürasyon

Bu dosya router paketini yerel sunucunuzda nasıl kuracağınıza ilişkin bilgileri içerir.

#### Apache Web Server Konfigurasyonu

Apache konfigürasyon dosyanızda <kbd>DocumentRoot</kbd> konfigürasyonu <kbd>projeniz/public/</kbd> klasörüne ayarlanmış olmalıdır.

```
<VirtualHost *:80>

        ServerAdmin webmaster@localhost
        DocumentRoot /var/www/Router/public/

        ServerName router
        DirectoryIndex index.php

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined

</VirtualHost>
```

DirectoryIndex değerini <kbd>index.php</kbd> değerine ayarlamanız önerilir. ServerName bu örnekte <kbd>router</kbd> olarak ayarlanmıştır.


#### .htaccess

Konfigurasyon aşağıdaki gibi <kbd>.htaccess</kbd> dosyasını gerektirir. Bu dosya <kbd>/public</kbd> klasöründe mevcuttur.

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

#### Host Dosyası

Bu örnekte <kbd>/etc/hosts</kbd> dosyasına proje router olarak tanımlanmıştır.

```
127.0.1.1       router
```

Bu tanımlamadan sonra tarayıcınızdan aşağıdaki gibi proje ismi ile örnek dosyalara ulaşabilirsiniz.

```
http://router/
```