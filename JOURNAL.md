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

## CRUD operations on users as a boss
Make a controller for that:
`php artisan make:controller Boss\\UserController -r`
to make a resource(-r) controller in Boss folder.

Open up web.php to create the routes.
```php
Route::resource('/boss/users', 'Boss\UserController', [
    'except' => ['show', 'create', 'store'],
]);
```
But we want to add a prefix `boss` in front of the routes name and uri. We do that by:
```php
Route::namespace('Boss')->prefix('boss')->name('boss.')->group(function(){

    Route::resource('/users', 'UserController', [
    'except' => ['show', 'create', 'store'],
    ]);

});
```
If you notice, we don't put `boss` in `resource` anymore since it's also been taken care of.

Now, we protect the resources so only boss can access. Just like we did before.
```php
Route::namespace('Boss')->prefix('boss')->middleware(['auth', 'auth.boss'])->name('boss.')->group(function(){

    Route::resource('/users', 'UserController', [
    'except' => ['show', 'create', 'store'],
    ]);

});

```
Now let's start viewing the employee(user) list. On app.blade.php,
```php
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-items">
                            <a href="{{ route('boss.users.index') }}">
                                Manage Employees
                            </a>
                        </li>
                    </ul>
```
For the index method in usercontroller:
```php
use App\User;

    public function index(){
        return view('boss.users.index')->with('users', User::all());
    }
```
use with method to get all the users(User::all()) and send it in `users` variable.

Now let's make view 'boss.users.index'. Make a `index.blade.php` in `views/boss`, and copy code from `home.blade.php` to start editing from it.

Get table template from https://getbootstrap.com/docs/4.4/content/tables/ and put it in the blade view.
```php
                <div class="card-body">
                <table class="table">
                    <thead class="thead-dark">
                        <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Roles</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- loop through users here -->
                        @foreach($users as $user)
                            <tr>
                                <th>{{ $user->name }}</th>
                                <th>{{ $user->email }}</th>
                                <th>{{ implode(', ', $user->roles()->get()->pluck('name')->toArray()) }}</th>

                            </tr>
                        @endforeach
                    </tbody>
                </table>

                </div>
```
See for loop section. $user is passed from the controller, then each loop, we print name email and roles.
Since a user might have multiple roles, we use:
```php
<th>{{ implode(', ', $user->roles()->get()->pluck('name')->toArray()) }}</th>
```

So if as a boss, he only can see this table.

## Edit users.
Let's make boss can edit users' roles.
Add edit button on new column in view.
```php
<th>
    <!-- so that every button is each id -->
    <a href="{{ route('boss.users$user->id) }}">
        <button type="button" clabtn-primary btn-sm">
            Edit
        </button>
    </a>
</th>
```
Make a `boss/users/edit.blade.php`. The content, just copy from index blade and remove table.

Leave it from now, and let's edit the edit controller.
```php
    public function edit($id)
    {
        // user user click on edit on their own id, redirect back to index page.
        if (Auth::user()->id == $id) {
            return redirect()->route('boss.users.index');
        } // user can't edit themselves

        // if not, go to the edit page.
        return view('boss.users.edit')->with(['user' => User::find($id), 'roles' => Role::all()]);
    }
```

Now, if you logged in as phillip, you cant edit phillip.

Let's edit the view.
Simple header.
```php
<div class="card-header">Manage {{ $user->name }}</div>
```

Then we will use form to do update method, like we learn from laravel5fromscratch.
```php
                <div class="card-body">
                    <form action="{{ route('boss.users.update', ['user'=>$user->id]) }}" method="POST">
                        @csrf
                        <!-- update method use PUT request -->
                        {{ method_field('PUT') }}
                        
                        <!-- make a checkbox to select roles -->
                        @foreach($roles as $role)
                            <div class="form-check">
                                <!-- check the box if user already has the role -->
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                    {{ $user->hasAnyRole($role->name)? 'checked':'' }}>
                                <label>{{ $role->name }}</label>
                                </input>
                            </div>
                        @endforeach
                        <!-- Button to submit data to update method -->
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
```

After clicking the button, the input will be sent to update method. Let's make it.
```php
    public function update(Request $request, $id)
    {
        // user user click on edit on their own id, redirect back to index page.
        if (Auth::user()->id == $id) {
            return redirect()->route('boss.users.index');
        } // user can't edit themselves

        $user = User::find($id); // find users id
        // since in view we take an array (roles[]), we can use sync()
        $user->roles()->sync($request->roles);

        return redirect()->route('boss.users.index');
    }
```

## Alerts
The problem with our view is, there's no alert warning when user want to edit on his info. So he might be confused why he's being redirected back to the index. So we need to warn users.
Make `views/partials/alerts.blade.php` and use this:
```php
@if(session('success'))
    <div class="alert alert-success" role="alert">
        {{ session('success') }}
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning" role="alert">
        {{ session('warning') }}
    </div>
@endif

```

Call this partials in our `layouts/app` to be used in all of our views.
```php
        <main class="py-4 container">
            @include('partials.alerts')
            @yield('content')
        </main>
```

Now to activate the alerts, edit our controller.
```php
            return redirect()->route('boss.users.index')->with('warning', 'You cannot edit yourself.');
```
Same way for `success` alert.

## Delete user
In index view, let's put delete button to delete the user like so.
```php
<!-- delete button submit to destroy method -->
<form action="{{ route('boss.users.destroy', $use" method="POST" class="float-left">
    {{ method_field('DELETE') }}
    @csrf
    <button type="submit" class="btn btn-danger btn-sm">
        Delete
    </button>
</form>
```

Now let's edit the destroy method.
```php
    public function destroy($id)
    {
        // user user click on delete on their own id, redirect back to index page.
        if (Auth::user()->id == $id) {
            return redirect()->route('boss.users.index')->with('warning', 'You cannot delete yourself.');
        } // user can't delete themselves

        User::destroy($id);

        return redirect()->route('boss.users.index')->with('success', 'Employee has been deleted.');
    }
}
```
Test it, should be okay.

## Filter/search:
https://www.youtube.com/watch?v=3PeF9UvoSsk
https://www.youtube.com/results?search_query=Laravel+api+Pagination+with+Filters
https://www.youtube.com/watch?v=KWnmOBkHzUo