# Journal
In this file, I record what I've done, so I can replicate the same things in the future.

## Project setup
Install xampp and start apache and mysql.
Go to `localhost/phpmyadmin`, username `root`, password is blank.
Proceed if all is okay.
`laravel new employee-manager` to create a project.

Setup a db in your phpmyadmin, called `employee-manager`.
Set your db in `.env` in your project folder.
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=employee-manager
DB_USERNAME=root
DB_PASSWORD=
```

## Login and register using make:auth (not passport yet)
For laravel >=6, latest:
`composer require laravel/ui`
`php artisan ui vue --auth`
install node: https://nodejs.org/en/download/
Must restart vs code.
`npm install`
`npm run dev`
`php artisan migrate`
Now the Login and register scaffold is done.

## Make a role table to assign users
Make Role model and migration with `php artisan make:model Role -m`