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
                @auth("web_ura")
                    <a href="{{ route('portal.ura.index') }}" @if(\Illuminate\Support\Facades\Route::currentRouteName() === 'portal.ura.index') aria-current="page" @endif>@lang('URA Portal Registration')</a>
                @endauth
                @auth("web_kvk")
                    <a href="{{ route('portal.kvk.index') }}" @if(\Illuminate\Support\Facades\Route::currentRouteName() === 'portal.kvk.index') aria-current="page" @endif>@lang('KVK Portal Registration')</a>
                    @endauth
            </li>
        </ul>
        @auth
        <ul>
            <li>
                <span>
                @auth("web_kvk")
                    KVK User: {{ Auth::user()->kvk_number }}
                @endauth
                @auth("web_ura")
                    URA User: {{ Auth::user()->ura_number }}
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
