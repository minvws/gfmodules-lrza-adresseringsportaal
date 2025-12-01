# GFModules Portal Register

This app is the Portal Register and is part of the 'Generieke Functies, lokalisatie en addressering' project of the Ministry of Health, Welfare and Sport of the Dutch government. The purpose of this application is for Healthcare Organizations to get published mCSD Directories from this Portal Register which they can use this to contact these directories directly or to build their own composed mCSD Directory with the use of the [update-client](https://github.com/minvws/gfmodules-mcsd-update-client). HealthCare organizations can publish their [mCSD Directory](https://profiles.ihe.net/ITI/mCSD/CapabilityStatement-IHE.mCSD.Directory.html) with addressing information at this register.

## Disclaimer

This project and all associated code serve solely as documentation
and demonstration purposes to illustrate potential system
communication patterns and architectures.

This codebase:

- Is NOT intended for production use
- Does NOT represent a final specification
- Should NOT be considered feature-complete or secure
- May contain errors, omissions, or oversimplified implementations
- Has NOT been tested or hardened for real-world scenarios

The code examples are only meant to help understand concepts and demonstrate possibilities.

By using or referencing this code, you acknowledge that you do so at your own
risk and that the authors assume no liability for any consequences of its use.

## Development

This project can be setup and tested either as a Laravel Sail application or in the provided docker environment. 

> **Quickstart**
> 
> The easiest way is to start the docker-compose project by running:
> 
> ```bash
> docker compose up
> ```
> This will start the project on 'http://localhost:8512'
>


### Laravel Sail

Requirements:

- [php(>=8.3.0)](https://www.php.net/manual/en/install.general.php)
- [composer(>=2.2)](https://getcomposer.org/download/)
- npm(>=10.8.2) + [node(>=20)](https://nodejs.org/en/download)

Run the following commands to run this application in docker using ```sail```.

```bash
cp .env.example .env
composer install
npm install
npm run build
vendor/bin/sail up -d
vendor/bin/sail artisan key:generate
```

This application requires an instance of a [HAPI](https://github.com/hapifhir/hapi-fhir) server running with it. 
Make sure that the correct URL is set for in the env file. See [env.example](https://github.com/minvws/gfmodules-lrza-adresseringsportaal/blob/ba9ae5748468da5758734ebeafb2f24b7dd24389/.env.example#L30)

### Docker compose

The easiest way to run this application is by using the docker-compose project in this repository. 

```bash
docker compose up
```

This will start the project on 'http://localhost:8512'

### Docker standalone

It's possible to do a standalone run of the application using docker. This docker container will have the laravel application running on an nginx webserver running on port 80.
Note that you would either set environment variables (see `.env.example`), or mount your `.env` during docker run.

Make sure you build the frontend assets locally first:

```bash
    # Build assets
    npm run build
    
    # Build docker image
    make container-build
    
    # Run container
    docker run -ti --rm -p 8512:80 --mount type=bind,source=./.env,target=/var/www/html/.env gfmodules-lrza-adresseringsportaal:latest
```

The application will be available on port 8512.
