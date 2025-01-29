# Shortener

Just tiny URL shortener service

# System requirements

  * Apache 1.3/2.2/2.4 web server + mod_rewrite
  * PHP 5.3-8.3
    
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

# Usage
Saving URL: 

```
https://yourhost.com/s/?shorten=https://piclod.com/
```

If the save is successful, a unique 12-digit link identifier with HTTP code 200 will be returned. 
In case any error ocurred you just get empty result with HTTP 400 error code.


Using received URL Id:
```
https://yourhost.com/s/nt3a6x9hpbhj
```

or 

```
https://yourhost.com/s/?go=nt3a6x9hpbhj
```

