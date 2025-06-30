<?php

declare(strict_types=1);

namespace App\Models;

enum EndpointStatus: string
{
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case ERROR = 'error';
    case OFF = 'off';
    case ENTERED_IN_ERROR = 'entered-in-error';
    case TEST = 'test';
}
