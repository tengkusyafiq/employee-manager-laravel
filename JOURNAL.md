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
MUST register and login one user first.
Now the Login and register scaffold is done.

## Make a role table to assign users
Make Role model and migration with `php artisan make:model Role -m`

Now let's edit our migration file for create_roles_table to add some columns:
```php
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
```

`php artisan migrate`


Open up User.php to add:
```php
    public function roles()
    {
        return $this->belongsToMany('App\Role');
    }
```
which means user can have many roles.

Do the same thing in Role.php indicates a role can have many users:
```php
    public function users()
    {
        return $this->belongsToMany('App\User');
    }
```

Now we need a table in our db to link role and user table together.
`php artisan make:migration create_role_user_table`

Edit the table column in the migration file:
```php
        Schema::create('        Schema::create('role_user', function (Blueprint $table) {
', function (Blueprint $table) {
            $table->id();
            $table->integer('role_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->timestamps();
        });
```
`->unsigned()` means it matches between the tables, you can call it primary key.

Now create the tables with `php artisan migrate`.
Proceed if all is okay.

## Seed new data to the tables.
First let's seed roles type in our role table.
`php artisan make:seed RolesTableSeeder`
Open the seeder file.
Import on top `use App\Role;`

In `run` function:
```php
    Role::truncate(); //to avoid duplicates if run more than one.
    Role::create(['name'=>'boss']);
    Role::create(['name'=>'manager']);
    Role::create(['name'=>'employee']);
```

Then in `DatabaseSeeder.php`, add `$this->call(RolesTableSeeder::class);` so that you can run the seed.
`php artisan db:seed`

Now see your `roles` table in your db, the roles should exists now.

Now let's seed new fake users.
`php artisan make:seed UsersTableSeeder`
Open the seeder file.
Import on top:
```php
use App\Role;
use App\User;
``` 
because we want to use both, we want to assign the role at the same time.
Put code below in run function:
```php
        User::truncate(); //to avoid duplicates if run more than one.
        // get the roles type in our roles table
        $bossRole = Role::where('name', 'boss')->first();
        $managerRole = Role::where('name', 'manager')->first();
        $employeeRole = Role::where('name', 'employee')->first();

        // create users and later we will attach the roles
        $boss = User::create([
            'name' => 'phillip',
            'email' => 'phillip@vimigo.my',
            'password' => bcrypt('password'),
        ]);
        $manager = User::create([
            'name' => 'jess',
            'email' => 'jess@vimigo.my',
            'password' => bcrypt('password'),
        ]);
        $employee = User::create([
            'name' => 'syafiq',
            'email' => 'syafiq@vimigo.my',
            'password' => bcrypt('password'),
        ]);

        // attach users above to a role
        $boss->roles()->attach($bossRole);
        $manager->roles()->attach($managerRole);
        $employee->roles()->attach($employeeRole);
```

Then in `DatabaseSeeder.php`, add `$this->call(UsersTableSeeder::class);` so that you can run the seed.
`php artisan db:seed`

Now see your `users` table in your db, the roles should exists now.

Now, if you see the `role_user` table, you can see that user id 1(Phillip) has role id 1(boss) and so on.

## User role helper
We create this to help use get user's roles soon.
In User.php, make a function like below:
```php
    public function hasAnyRoles($roles) // for user that has multiple roles
    {
        return null !== $this->roles()->whereIn('name', $roles)->first();
    }

    public function hasAnyRole($role) // for user that has only one role
    {
        return null !== $this->roles()->where('name', $role)->first();
    }
```
how it works:
first function, we gonna pass in an array or roles,and get current user model, and we gonna see if any of our roles ar in name column. If doesn't have any, we return null.

second function, also works the same way, except it only check if user has one role or not.

## Protecting Routes by using roles, so only boss/admin can view all employees.
We will use middleware to check wether the user is a boss or not to access a route.
Make one:
`php artisan make:middleware AccessBoss`

Inside `handle` method, we gonna use helper method we made before, `hasAnyRole`, to see if the user has a role of `boss`.
```php
        if (Auth::user()->hasAnyRole('boss')) { // if the user is boss, return to next middleware.
            return $next($request);
        }

        return redirect('home'); // if not boss, just redirect to home
```
Register this middleware in `Kernel.php` in routeMiddleware.
```php
    protected $routeMiddleware = [
        ...
        'auth.boss' => \App\Http\Middleware\AccessBoss::class,
    ];
```
We call the middleware `auth.boss`.

Okay to use this middleware in route, let say only boss can access `/boss`. Add the route in web.php:
```php
Route::get('/boss', function () {
    return 'you are the boss';
})->middleware(['auth', 'auth.boss'])->name('boss');
```
`auth` middleware makes the route only can be accessed by logged in user.
`auth.boss` middleware makes the route only can be accessed by logged in boss.

