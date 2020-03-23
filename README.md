# Employee manager Laravel project
## How to run the project

1. Clone the repo.

2. Install xampp(or any related programs) and start Apache and MySQL server.

3. Open phpmyadmin and create a database named `employee-manager`

4. Duplicate `.env.example`, name it as `.env` and edit some lines as below:
	```
	DB_CONNECTION=mysql
	DB_HOST=127.0.0.1
	DB_PORT=3306
    DB_DATABASE=employee-manager
	DB_USERNAME=root
	DB_PASSWORD=
	```

5. Install composer.

6. Run commands below:

	```
	php artisan serve
	```