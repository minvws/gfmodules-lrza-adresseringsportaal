<nav
    data-open-label="Menu"
    data-close-label="Sluit menu"
    data-media="(min-width: 42rem)"
    aria-label="{{ __('Main navigation') }}"
    class="collapsible">
    <div class="collapsing-element">
        <ul>
            <li>
                <a href="{{ route('index') }}" @if(\Illuminate\Support\Facades\Route::currentRouteName() === 'index') aria-current="page" @endif><span class="icon icon-home">Home-icoon</span>@lang('Home')</a>
                @auth
                    <a href="{{ route('portal.index') }}" @if(\Illuminate\Support\Facades\Route::currentRouteName() === 'portal.index') aria-current="page" @endif>@lang('Portal Registration')</a>
                @endauth
            </li>
        </ul>
        @auth
        <ul>
            <li>
                <span>
                @auth
                    Organization: {{ Auth::user()->getOrganization()->getName() }}
                @endauth
                </span>

                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit">@lang('Logout')</button>
                </form>
            </li>
        </ul>
        @endauth
    </div>
</nav>
