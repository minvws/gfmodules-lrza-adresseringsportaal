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
