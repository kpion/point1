# Single entry point (front controller) in PHP

In progress.

# I. Put this into your apache's vhost configuration or .htaccess file:
<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteCond %{SCRIPT_FILENAME} !-d
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteRule ^(.*)$ index.php/$1 [QSA,L]
	#a trick to make it simple to the requested elements *after* index.php:
	SetEnv USE_URI_PROTOCOL PATH_INFO
</IfModule>

In progress.