# ZF2 tryout application
Small web-apllication to test ZF2. Task in file [TASK.md](https://github.com/Avatar4eg/zend-tryout/blob/master/TASK.md) (russian).
### Configuration:
- Main config in **video_service** part of [config.php](https://github.com/Avatar4eg/zend-tryout/blob/master/app/config/config.php) file.
- Also DB migration required.
### Result:
1. Web-app with sortable recordings list:
```bash
http://yourserver.com/recordings
```
Preview:  
![Imgur](https://i.imgur.com/y05JvPp.png)  
2. CLI-app with infinite loop on selected stream:
```bash
php run -watch
```
Generating *.mp4 files in **storage/video** public folder and writing DB records for it.
### Dev tools:
1. PHPStorm 2017.1
2. XDebug 2.5.1
3. XAMPP 7.0.9
4. Phalcon Dev-Tools 3.1.1
### Tested on environment:
1. Apache 2.4.23
2. PHP 7.0.9
3. MariaDB 10.1.16
4. Phalcon 3.1.1