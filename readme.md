# Simple Single Point of Entry (Front Controller) in PHP

This project solves only one thing (hence "Point **one**" :))  - single point of entry AKA front controller pattern.

Can be used as a boilerplate for very simple sites, where you have "common" things you want to share across and/or you want friendly URLs like example.com/about or example.com/action/param

For a more feature rich version see https://github.com/kpion/point2

`index.php` file is the front controller, all the other files are just for illustration purposes.

## Setup

Clone or download the project, or even just the single `index.php` file and setup your server.

The instructions below explain how to tell your web server to funnel all HTTP requests to our PHP front-controller file.

The project comes with an example .htaccess file, so it should work out of the box, although using virtual hosts is recommended.

Assuming the project is located in /var/www/html/point1 :

## Apache vhost configuration example

```
<VirtualHost *:80>
	ServerName point1
	DocumentRoot /var/www/html/point1
	
	ErrorLog ${APACHE_LOG_DIR}/error.log
	CustomLog ${APACHE_LOG_DIR}/access.log combined	
	
	<directory /var/www/html/point1>
	    <IfModule mod_rewrite.c>
	      RewriteEngine On
	      RewriteCond %{SCRIPT_FILENAME} !-d
	      RewriteCond %{REQUEST_FILENAME} !-f
	      RewriteRule ^.*$ index.php [QSA,L] 
	    </IfModule>  
	</directory>
</VirtualHost>
```

## Nginx vhost configuration example

```
  server {
    listen 80;
    server_name point1;
    index index.php;
    root /var/www/html/point1;
                       
    location / {
		try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~ \.php$ {
    
		# regex to split $uri to $fastcgi_script_name and $fastcgi_path
		fastcgi_split_path_info ^(.+\.php)(/.+)$;

		# Check that the PHP script exists before passing it
		try_files $fastcgi_script_name =404;

		# Bypass the fact that try_files resets $fastcgi_path_info
		# see: http://trac.nginx.org/nginx/ticket/321
		set $path_info $fastcgi_path_info;
		fastcgi_param PATH_INFO $path_info;

		fastcgi_index index.php;
		include fastcgi.conf;

		fastcgi_pass unix:/run/php/php7.2-fpm.sock;
    }


    # deny access to .htaccess files, if Apache's document root
    # concurs with nginx's one
    #
    location ~ /\.ht {
      deny all;
    }
	
  }

```

## .htaccess file example

Same thing as in apache/vhost, plus setting an environment variable 'USE_URI_PROTOCOL' to 'PATH_INFO',
the simplest way to tell the PHP script that .htaccess is in use and that it's therefore possible that `$_SERVER['REQUEST_URI']` contains a parent dir, in case we keep multiple projects under /var/www/html and /var/www/html is our root.

A bit tricky but does the job.

```
<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteCond %{SCRIPT_FILENAME} !-d
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteRule ^(.*)$ index.php/$1 [QSA,L]
	#a trick making it simpler to get the requested elements *after* index.php:
	SetEnv USE_URI_PROTOCOL PATH_INFO
</IfModule>
```
## Final notes

You might want to consider moving 'pages' directory outside the document root.

