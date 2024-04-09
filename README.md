# Lumen Passport

> Support Lumen 10.x and Laravel Passport 11.x

Source : [Lumen Passport](https://github.com/dusterio/lumen-passport)

## Edit bootstap / app.php

```php

// uncomment
 $app->withFacades();
 $app->withEloquent();

// add config
 $app->configure('auth');
 $app->configure('passport');

// uncomment
 $app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
 ]);

 /*
  |--------------------------------------------------------------------------
  | Disable Passport Routes
  |--------------------------------------------------------------------------
  |
  | Here we will disable the default routes provided by Passport.
  |
  */
  // add this
  \Laravel\Passport\Passport::$registersRoutes = false;

  // add register service provider
  $app->register(Laravel\Passport\PassportServiceProvider::class);
  $app->register(\Ekopras18\LumenPassport\PassportServiceProvider::class);

  /*
  |--------------------------------------------------------------------------
  | Load The Application Routes and Passport Routes
  |--------------------------------------------------------------------------
  |
  | Next we will include the routes file so that they can all be added to
  |
  */
  // add this
  \Ekopras18\LumenPassport\LumenPassport::routes($app, ['prefix' => 'v1/oauth']);

```

## Make directory config and file auth.php, passport.php
> vendor/ekopras18/lumen-passport/src/config/
  - auth.php
    ```php
    <?php

    return [

        /*
        |--------------------------------------------------------------------------
        | Authentication Defaults
        |--------------------------------------------------------------------------
        |
        | This option controls the default authentication "guard" and password
        | reset options for your application. You may change these defaults
        | as required, but they're a perfect start for most applications.
        |
        */

        'defaults' => [
            'guard' => 'api',
            'passwords' => 'users',
        ],

        /*
        |--------------------------------------------------------------------------
        | Authentication Guards
        |--------------------------------------------------------------------------
        |
        | Next, you may define every authentication guard for your application.
        | Of course, a great default configuration has been defined for you
        | here which uses session storage and the Eloquent user provider.
        |
        | All authentication drivers have a user provider. This defines how the
        | users are actually retrieved out of your database or other storage
        | mechanisms used by this application to persist your user's data.
        |
        | Supported: "token"
        |
        */

        'guards' => [
            'api' => [
                'driver' => 'passport',
                'provider' => 'users',
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | User Providers
        |--------------------------------------------------------------------------
        |
        | All authentication drivers have a user provider. This defines how the
        | users are actually retrieved out of your database or other storage
        | mechanisms used by this application to persist your user's data.
        |
        | If you have multiple user tables or models you may configure multiple
        | sources which represent each model / table. These sources may then
        | be assigned to any extra authentication guards you have defined.
        |
        | Supported: "database", "eloquent"
        |
        */

        'providers' => [
            'users' => [
                'driver' => 'eloquent',
                'model' => \App\Models\User::class
            ]
        ],

        /*
        |--------------------------------------------------------------------------
        | Resetting Passwords
        |--------------------------------------------------------------------------
        |
        | Here you may set the options for resetting passwords including the view
        | that is your password reset e-mail. You may also set the name of the
        | table that maintains all of the reset tokens for your application.
        |
        | You may specify multiple password reset configurations if you have more
        | than one user table or model in the application and you want to have
        | separate password reset settings based on the specific user types.
        |
        | The expire time is the number of minutes that the reset token should be
        | considered valid. This security feature keeps tokens short-lived so
        | they have less time to be guessed. You may change this as needed.
        |
        */

        'passwords' => [
            //
        ],

    ];
    ```
  - passport.php
    ```php
    <?php

    return [

        /*
        |--------------------------------------------------------------------------
        | Passport Guard
        |--------------------------------------------------------------------------
        |
        | Here you may specify which authentication guard Passport will use when
        | authenticating users. This value should correspond with one of your
        | guards that is already present in your "auth" configuration file.
        |
        */

        'guard' => 'api',

        /*
        |--------------------------------------------------------------------------
        | Encryption Keys
        |--------------------------------------------------------------------------
        |
        | Passport uses encryption keys while generating secure access tokens for
        | your application. By default, the keys are stored as local files but
        | can be set via environment variables when that is more convenient.
        |
        */

        'private_key' => env('PASSPORT_PRIVATE_KEY'),

        'public_key' => env('PASSPORT_PUBLIC_KEY'),

        /*
        |--------------------------------------------------------------------------
        | Client UUIDs
        |--------------------------------------------------------------------------
        |
        | By default, Passport uses auto-incrementing primary keys when assigning
        | IDs to clients. However, if Passport is installed using the provided
        | --uuids switch, this will be set to "true" and UUIDs will be used.
        |
        */

        'client_uuids' => true,

        /*
        |--------------------------------------------------------------------------
        | Personal Access Client
        |--------------------------------------------------------------------------
        |
        | If you enable client hashing, you should set the personal access client
        | ID and unhashed secret within your environment file. The values will
        | get used while issuing fresh personal access tokens to your users.
        |
        */

        'personal_access_client' => [
            'id' => env('PASSPORT_PERSONAL_ACCESS_CLIENT_ID'),
            'secret' => env('PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET'),
        ],

    ];

    ```

## User models

  ```php
  /** @file app/Models/User.php */

  use Laravel\Passport\HasApiTokens;

  class User extends Model implements AuthenticatableContract, AuthorizableContract
  {
      use HasApiTokens, Authenticatable, Authorizable, HasFactory;

      /* rest of the model */
  }
  ```

## For migration, copy in 

> vendor/ekopras18/lumen-passport/src/database/migrations/

## .env File add this

```env
PASSPORT_PERSONAL_ACCESS_CLIENT_ID=
PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=
```


## Install passport

```bash
# Create new tables for Passport
php artisan migrate

# Install encryption keys and other stuff for Passport
php artisan passport:install
```