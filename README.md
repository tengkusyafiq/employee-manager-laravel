# Employee manager - a Laravel project

## TO DO
1. ~~Migrations and seeding users.~~

2. ~~User role relationship.~~

3. ~~Boss route to do CRUD on users.~~

4. API CRUD operation on `/boss` route, use `resource`. Remember, boss can create user!
_See employeedata project_

5. Pagination on index method in user controller. 
_See employeedata project_

6. Use transformer to only return name and email on index method.
_See incremental API course._

7. Input validation file. 
_See Laravel 5 from scratch notes_

8. Move from built in auth to Passport. 
_No exact references yet. But can try since the docs is clear enough._

9. CUD users with excel/csv files. 
_No references yet!!!_

10. Filter index.
_Just found references._


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