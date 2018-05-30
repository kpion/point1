
I. Put this into your hvost configuration or .htaccess file:
<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteRule ^(.*)$ index.php/$1 [QSA,L]
</IfModule> 

Details: