<?php

declare(strict_types=1);

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class GuestLayout extends Component
{
    /**
     * Get the view / view contents that represent the component.
     *
     * @return View|Htmlable|Closure|string
     */
    public function render()
    {
        return view('layouts.guest');
    }
}
