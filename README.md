# Shortener

Just tiny URL shortener service

# System requirements

  * Apache 1.3/2.2/2.4 web server + mod_rewrite
  * PHP 5.3-8.4
    
# FreeBSD quick setup
```   
# cd /usr/local/www/apache24/data/
# fetch https://github.com/nightflyza/shortener/archive/refs/heads/main.zip
# unzip main.zip
# mv shortener-main s
# rm -fr main.zip
# chmod -R 777 s/data
```

# Linux quick setup
```
# cd /var/www/html/
# wget https://github.com/nightflyza/shortener/archive/refs/heads/main.zip
# unzip main.zip
# mv shortener-main s
# rm -fr main.zip
# chmod -R 777 s/data
```

# Apache configuration
Make sure that Apache mod_rewrite is enabled on your server in /usr/local/etc/apache24/httpd.conf

```
LoadModule rewrite_module libexec/apache24/mod_rewrite.so
```
if this line is commented, just uncomment it and restart your web-server

```
apachectl restart
```

also check for AllowOverride option in your Apache config

```
<Directory />
    AllowOverride All
    Order deny,allow
</Directory>
```

# Usage
Saving URL: 

```
https://yourhost.com/s/?shorten=https://piclod.com/
```

If the save is successful, a unique 12-characters link identifier with HTTP code 200 will be returned. 
In case any error occurred you just get empty result with HTTP 400 error code.


Using received URL Id:
```
https://yourhost.com/s/nt3a6x9hpbhj
```

or 

```
https://yourhost.com/s/?go=nt3a6x9hpbhj
```

