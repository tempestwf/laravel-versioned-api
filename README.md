# Tempest Tools Skeleton

This is a Skeleton that I am crafting that will contain all the commonly required features of web applications, and a set of tools for building APIs faster, more securely, and more extensively than any other project I have encountered.

Over the nearly 15 years I have spent making web apps, I have faced many poorly written projects. I spent this time collecting notes on what I think an ideal project would look like and thinking about the best way to create a tool set to write API's that meet the following requirements: 
1. Are as fast as possible to set up and configure
2. Secure
3. Flexible and extensible enough to allow the core code of the API to be easily adapted to any scenario
4. Consistent to use and understand.

I also have always wondered why it is that there’s a set of features which 99% of web apps require, and yet there is no way to start a project with all those features already authored and working together. 

My goal is to have Tempest Tools, and this Skeleton, be my answer to those needs; an ideal starting place for new Laravel projects. The Skeleton I am crafting will contain all the commonly required features of web applications, and a set of tools for building APIs faster, more securely, and more extensively than any other project.

As new tools are added to the Skeleton, I will wire them together. Therefore, the Skeleton will serve as a working example of all the tools included, and as the perfect starting place for any new Laravel project.

This Skeleton also houses the test cases for other Tempest Tools packages.

Tempest Tools Skeleton is a work in progress, so if you encounter a problem please report it, and/or fire off a pull request to fix the issue.

Also note that the packages folder holds the 1st party components.

Tempest Tools Skeleton is a fork of Laravel versioned API. Please see: https://github.com/mitchdav/laravel-versioned-api

## What’s currently included in v2.0.4 (1st Party)
1. https://github.com/tempestwf/tempest-tools-common
 - Contains common libraries used by other Tempest Tools packages.
2. https://github.com/tempestwf/tempest-tools-moat
 - Contains easily configurable middleware that can be applied to any route restrict access to it based on a templating system that allows developers to easily define which permissions (stored in the database VIA;  https://packagist.org/packages/laravel-doctrine/acl) are required to access it.
3. https://github.com/tempestwf/tempest-tools-raven
 - Contains tools to easily configure event-based messaging and real-time alerts.
4. Sign Up / User email verification.
5. Oauth sign in from 3rd party services (google, facebook, twitter).
6. Improved localization features.
7. Tempest Tools CRUD
 - The fastest, most secure, and extendable way to write RESTful APIs
8. User management system
 - A user management system based on Tempest Tools CRUD

## What’s currently included in v2.0.4 (3rd Party)
1. Everything that laravel-versioned-api ships with
2. https://packagist.org/packages/laravel-doctrine/extensions
3. https://packagist.org/packages/gedmo/doctrine-extensions
4. https://packagist.org/packages/beberlei/DoctrineExtensions
5. https://packagist.org/packages/laravel-doctrine/migrations
6. https://packagist.org/packages/laravel-doctrine/acl

## What’s currently in development
1. Tempest Tools Cache Tagging System
 - Tag cache items based on the templating system from Tempest Tools Common ArrayHelper. Apply tags to cache in distributed cache environments that don’t commonly support it.

## What’s planned:
1. Improved error handling and logging.
2. Support for code modularization.
3. Better implementation of Gedmo extensions (applying the extensions to provided entities).
4. Full Text Search.
5. Result Caching.
6. Database sharding for large datasets.

## Requirements
- PHP >= 7.1.0
- [Composer](https://getcomposer.org/)
- laravel/framework 5.3.*
- laravel-doctrine/orm 1.2.*,
- Cache system (Redis, Memcached, File cache)
- A database (MySQL, or any others supported by Laravel and Doctrine)

## Installation
You may fork the repo first and then update the git clone command below. Or you may optionally save the cloned repo into your own repository.

Note: Make sure to turn off xdebug before running composer install as it can cause it to hang indefinitely (setting xdebug: "xdebug.remote_autostart= 0" should fix the problem).
Clone the repo manually by running the following commands in the terminal. We do a manual clone so we can run the sub module commands:

    git clone https://github.com/tempestwf/tempest-tools-skeleton {name of directory to clone into}
    cd {name of directory to clone into}
    git submodule init
    git submodule update
    composer install
    php -r "copy('.env.example', '.env');"
    php artisan key:generate
    php artisan jwt:generate

If you want a vanilla implementation that does not include the albums and artists and tests related to them you may switch to the vanilla branch.

    git checkout 2.0.2-vanilla

Give your API a name in the .env

    API_NAME="{API Name}"

Set your additional information in the .env file. Remember to also set up the host on your system before testing as well.

    APP_URL=http://{site url}
    API_DOMAIN={domain}

Also in the .env file you must set up a base user. This user will be added to the DB, and the test cases that require a user will run using this users

    BASE_USER_NAME="{name}"
    BASE_USER_EMAIL={email}
    BASE_USER_PASSWORD={password}

This will setup the project's dependencies, however you will still need to setup the database. You must first create a MySQL database, and then store its details in the .env file like so:

    DB_DATABASE={database}
    DB_USERNAME={username}
    DB_PASSWORD={password}

If you have cache drivers then add the following lines.

    CACHE_DRIVER={cache driver name}
    SESSION_DRIVER={cache driver name}
    QUEUE_DRIVER={cache driver name}

If the cache driver is redis you should specify the setting like the lines bellow.

    REDIS_HOST={host}
    REDIS_CLIENT={client}
    REDIS_PASSWORD={password}
    REDIS_PORT={port}

As of now we have Facebook, Twitter and Google supported on the Socialite plugin. You can specify the account by adding the lines bellow.

    FACEBOOK_KEY={key}
    FACEBOOK_SECRET={secret}
    FACEBOOK_REDIRECT_URI=http://api-dev.aki.com/auth/authenticate/callback/facebook
    
    TWITTER_KEY={key}
    TWITTER_SECRET={secret}
    TWITTER_REDIRECT_URI=http://api-dev.aki.com/auth/authenticate/callback/twitter
    
    GOOGLE_KEY={key}
    GOOGLE_SECRET={secret}
    GOOGLE_REDIRECT_URI=http://api-dev.aki.com/auth/authenticate/callback/google

If at this point you would like to save your modified repo into it's own repository you may do so with the following command.

    git push https://{username}:{password}@github.com/{your github user}/{your repo} +{branch of the skeleton you want to copy}:master

Set the origin of your repo to the new location you pushed it too

     git remote set-url origin https://{username}:{password}@github.com/{your github user}/{your repo}

To then setup the database we use Doctrine's helper command to build our schema and proxies. Migration files are located at ```./database/migrations``` which is currently blank right now. Do ```migrations:diff``` to generated the first migration file that will be derived from the entities. Then ```migrations:migrate``` to put it up to your database. As well as ```generate:proxies``` to create proxies for each entity available.

    php artisan doctrine:migrations:diff
    php artisan doctrine:migrations:migrate
    php artisan doctrine:generate:proxies

At this point your database is still empty. Please run the seeders to populate permissions and authentication essentials, add in your base user and some sample records. Seeder files are located at ```./database/seeds```.

    php artisan db:seed
    php artisan db:seed --class=SampleRecordsSeeder

## Logging In
Make a POST request to ```/auth/authenticate``` with ```Content-Type``` set to ```application/json```. The JSON structure should look like the following:
	
	{
	    "email": "{email address from env}",
	    "password": "{email address from env}"
	}

If the response is successful, you will receive a token which you can use to make subsequent requests to the server, while remaining authenticated as the given user. To send through the token value send it in the ```Authorization``` header as follows:

	Authorization: Bearer {token}
	
To retrieve the user details make a GET request to ```/auth/me``` and you will receive a request similar to the following:

    {
        "id": 1,
        "email": "theodora39@example.net",
        "name": "Dillon Effertz",
        "job": "Roofer"
    }

For further information about making requests to the server check the [Dingo API Wiki](https://github.com/dingo/api/wiki).
	
## Developing The Server
As you make changes to the entities, you need to generate proxies for your entities, so that the system can load quickly for each request. To do this, run the following command in the terminal:

	php artisan doctrine:generate:proxies

This will not be necessary unless you modify the entities as their proxies are already generated and committed.

If your changes to an entity modify its database structure, you can create a migration based on change by running the following command:

	php artisan doctrine:migrations:diff
	php artisan doctrine:migrations:migrate

## Routes
Routes for your versions are available by looking at the [routes file].

See: tempest-tools-moat (https://github.com/tempestwf/tempest-tools-moat) for information on adding acl middleware to your routes.

## Adding New Versions
The important files are all contained in the ```app/API``` folder. The project has 3 different versions of the same API, which is kept very simple to be as extendable as possible.

When you would like to add a new version, you will need to follow this process:

1. Copy the whole previous version's folder into the ```app/API``` folder and give it a suitable name (for example, copy the ```app/API/V3``` folder to ```app/API/V4```)
2. Do a search and replace operation to update the folder's references from the previous version to the new version (for example, update all instances of ```V3``` to ```V4``` within the ```V4``` folder)
3. Copy any existing routes from the previous version to the new version in the routes file
4. Update the ```config/api.php``` file to include the new version in the ```auth``` section
5. Update the ```.env``` file's ```API_VERSION``` variable to the new version
6. Make your changes to the API's new version (for example, add a new entity, or a field to an existing entity)
7. Run the following commands to update the database schema and proxies

        php artisan doctrine:migrations:diff
        php artisan doctrine:migrations:migrate
        php artisan doctrine:generate:proxies

8. Add routes to any new endpoints in the routes file
9. Just in case that APP_URL is missing you can run the line bellow

        php artisan config:clear


## Removing Versions
To remove a version follow this process:
1. Remove the version's folder inside the ```app/API``` folder
2. Remove the routes for the removed version from the routes file
3. Remove the ```auth``` entry in the ```config/api.php``` file
4. Check that the ```.env``` file's ```API_VERSION``` variable is not set to the removed version


## Localization
As default we are using ageras-com/laravel-onesky as the translation wrapper. This is a seamless integration of OneSky to the system. It runs with artisan and has pull and push functionality. Meaning we do not need to manually upload and download translations.
1. Add in your ONESKY_API_KEY and ONESKY_SECRET to your .env file
2. Create the base locale of your system. As of default we are with 'en' as default. The translation files are in the resources/lang folder and is separated by each local folder.
3. When you are ready to translate your language files, use this simple artisan command to upload them to your OneSky account:
```
        php artisan onesky:push        
```
4. When your language files have been translated, use this command to download them directly into your project:
   
```
        php artisan onesky:pull
```
5. If you only want certain languages to be pulled, you can use the --lang= flag:
```
        php artisan onesky:pull --lang=en,da,no
```
6. If you have multiple projects, you can use the --project= flag to specify the id:
```
        php artisan onesky:push --project=1337
```

Note: You can find the default locale fallback at the app.php config file. To translate you can use trans() to translate (example: echo trans('auth_failed');). For reference you can check https://packagist.org/packages/ageras/laravel-onesky    


## Known irregularities 
When adding libraries on the composer.json or adding values to .env file sometimes it does not reflect as it goes. If you experience this you might need to run the following:
```
    composer dump-autoload -o
    php artisan config:clear
    php artisan cache:clear   
```

