# ZF2 tryout application
Small web-apllication to test ZF2. Task in file [TASK.md](https://github.com/Avatar4eg/zend-tryout/blob/master/TASK.md) (russian).
### Installation & Configuration:
- clone project  
- run `composer update`  
- configure [doctrine.global.php](https://github.com/Avatar4eg/zend-tryout/blob/master/config/autoload/doctrine.global.php), [mail.global.php](https://github.com/Avatar4eg/zend-tryout/blob/master/config/autoload/mail.global.php) and create **scn-social-auth.local.php** with social services client & secrets (template can be found in **/vendor/socalnick/scn-social-auth/config**)  
- run `doctrine-module orm:schema-tool:update` to add DB schema  
- run `doctrine-module migrations:migrate` to add basic roles and user  
- `gulp watch` can be used to serve js and css changes
### Result:
1. Login and register:  
```bash
http://yourserver.com/user/login
http://yourserver.com/user/register
```  
Preview:  
![Imgur](https://i.imgur.com/i7wq1zb.png)  
2. User profile:  
```bash
http://yourserver.com/user
```  
![Imgur](https://i.imgur.com/qOq5TG3.png)  
3. Email conformation...:  
![Imgur](https://i.imgur.com/7A0sqmn.png)  
...and activation:
```bash
http://yourserver.com/user/activate?t=%token
```  
![Imgur](https://i.imgur.com/2gckRhg.png)  
4. Also:  
- dependency based (composer)  
- front builded and minified (gulp/bower)  
- main header metadata  
### Dev tools:
1. PHPStorm 2017.1
2. XDebug 2.5.3
3. XAMPP 5.6.28
### Tested on environment:
1. Apache 2.4.23
2. PHP 5.6.28
3. MariaDB 10.1.19