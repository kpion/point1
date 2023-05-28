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

