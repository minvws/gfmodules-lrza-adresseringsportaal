#!/bin/bash -xe

composer install --ignore-platform-reqs
npm install
npm run build

if [ ! -f /opt/app/.env ]; then
    cp .env.example .env
fi

grep ^APP_KEY=$ .env && php artisan key:generate

exit 0
