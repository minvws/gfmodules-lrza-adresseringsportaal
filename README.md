Auto-generated README for gfmodules-portal-register

## Development setup

Requirements:
- php
- composer
- npm

Run the following commands to run this application in docker using ```sail```.



```bash
composer install
npm run build
vendor/bin/sail up -d
vendor/bin/sail artisan key:generate
vendor/bin/sail artisan migrate
vendor/bin/sail artisan db:seed
```



## Run on docker
It's possible to do a standalone run of the application using docker. This docker container will have the laravel application running on an nginx webserver running on port 80.
Note that you would either set environment variables (see `.env.example`), or mount your `.env` during docker run.

Make sure you build the frontend assets locally first:

```bash
    # Build assets
    npm run build
    
    # Build docker image
    make container-build
    
    # Run container
    docker run -ti --rm -p 8201:80 --mount type=bind,source=./.env,target=/var/www/html/.env gfmodules-portal-register:latest
```
