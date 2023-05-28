# Simple Single Point of Entry (Front Controller) in PHP

This project solves only one thing (hence "Point **one**" :))  - single point of entry AKA front controller pattern.

Can be used as a boilerplate for very simple sites, where you have "common" things you want to share across and/or you want friendly URLs like example.com/about or example.com/action/param


`index.php` file is the front controller, all the other files are just for illustration purposes.

## Usage

After setting up you can just add pages (.php files in /pages directory), they will be become available from now under example.com/yourNewPage

The idea is to not use any routers here.

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

## Nginx vhost configuration

We only change a bit the `location /` part

```
...
     location / {
		# Front Controller -  https://github.com/kpion/point1. Every non matching request  goes to index.php
		try_files $uri $uri/ /index.php$is_args$args;
		# End of Single Point of Entry.
    }
...

```

## .htaccess file example

Here we pass the 'URI' part as query param (as point-url variable).

This differs from the typical way in that we also pass the 'index.php' itself.

Just to be consistent (i.e. always pass something as query param, even if it's only an empty string).

```
<IfModule mod_rewrite.c>
	RewriteEngine On

	# if it isn't a directory or file:
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteRule ^(.*)$ index.php?point-url=$1 [QSA,L]
	
	# we also want our index php to be passed to index.php (as ?point-url=...), because this way we'll know
	# we *did* go through htaccess rewriting process, which is important.
	RewriteRule ^(index.php)$ index.php?point-url=$1 [QSA,L]
	
</IfModule>
```

## Final notes

You might want to consider moving 'pages' directory outside the document root.

