## Startup guide

## Requirements `*`
- [Laravel herd](https://herd.laravel.com)
- [php version 8.4.x](https://php.net/downloads) `**`
- [composer](https://getcomposer.org/download)
- [node.js](https://nodejs.org/en/download)

---
#### `*` Some of these can be downloaded and used directly in Laravel Herd. Please pick in  the settings what code editor you like!
##
#### `**` These extentions need to be enabled in your `php.ini` file
---

* Ctype
* cURL 
* DOM 
* Fileinfo 
* Filter 
* Hash 
* Mbstring 
* OpenSSL 
* PCRE 
* PDO 
* Session 
* Tokenizer 
* XML 
* sqlite
---
### *DISCLAIMER!* I use git bash in vscode as terminal, your terminal might behave differently!
---
># First time run
* Unpack the `.zip` file in a folder of your choice
* Open Laravel herd and go to the Dashboard page\
    Click on 'Open Sites'\
    Click on 'Add' in the top left corner and press 'Link existing project'\
    Select the unzipped folder, and choose php version 8.4.*
* When on the overview page of your website, click on open to the right of your chosen code editor
* In your code editor, open the terminal and
* run `cp .env.example .env`
* run `composer install`
* run `php artisan key:generate`
* run `php artisan migrate --seed` (yes to all)
* run `npm install`
* run `npm run build`
* run `php artisan optimize`
* run `php artisan filament:optimize`
* Go back to your laravel herd overview page of the website, and click on the URL or run `php artisan serve` and click on the link
---

> ## Normal run
* Go to your laravel herd overview page of the website, and click on the URL or run `php artisan serve` in the project root and click on the link
---
Login information `*`
|email|password|
|--|--|
|test@test.test|ADMIN|
|john@doe.com|Password|

---
>###### *Project made in 11-2024 - 01-2025*
>### Created by Jasper van den Heuij