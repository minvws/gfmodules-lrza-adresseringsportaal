<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UraNoUraNumberException extends Exception
{
    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report(): ?bool
    {
        return true;
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  Request $request
     * @return Response
     */
    public function render(Request $request)
    {
        return response()
            ->view('errors.no-ura-number', [], 403);
    }
}
