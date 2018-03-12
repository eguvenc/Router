
## Configuration

This file contains information of how to set up the router package on your localhost.

### Apache web server

In your Apache configuration file, `DocumentRoot` should be set to directory `your-project/public/`.

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

DirectoryIndex value are suggested to be set to `index.php`. ServerName is set to `router` in this example.

### .htaccess

Configuration requires a `.htaccess` file like below. This file exists in `/public` folder.

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

### Host file

In this example, the project is set as router in `/etc/hosts` file.

```
127.0.1.1       router
```

After defining this, you can access the sample files through your browser with the project name as in the below example:

```
http://router/
```