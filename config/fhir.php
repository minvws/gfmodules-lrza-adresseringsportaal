<?php

declare(strict_types=1);

return [
    'endpoint' => env('FHIR_ENDPOINT', 'http://localhost:8000/fhir'),
    'default_endpoint_payloadtype_code' =>
        env('FHIR_DEFAULT_ENDPOINT_PAYLOADTYPE_CODE', 'mcsd-update-client-directory'),
    'default_endpoint_payloadtype_display' =>
        env('FHIR_DEFAULT_ENDPOINT_PAYLOADTYPE_DISPLAY', 'mCSD update client directory'),
    'default_endpoint_payloadtype_text' =>
        env('FHIR_DEFAULT_ENDPOINT_PAYLOADTYPE_TEXT', 'mCSD update client directory'),
];
