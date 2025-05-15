#!/bin/bash
echo "Populating HAPI FHIR DB with test data..."

curl -v -s -X POST http://portalhapi:8080/fhir \
    -H "Content-Type: application/fhir+json" \
    -H "Accept: application/fhir+json" \
    --json @seed/seed.json

echo "Running supervisord..."
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
