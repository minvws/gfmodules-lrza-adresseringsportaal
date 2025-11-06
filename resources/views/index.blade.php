@extends('layouts.guest')

@section('content')
<section>
    <div>
        @if (session()->has('error'))
            <section role="alert" class="error no-print" aria-label="{{ __('error') }}">
                <div>
                    <h4>{{ session('error') }}</h4>
                    <p>{{ session('error_description') }}</p>
                </div>
            </section>
        @endif

        @session('success')
            <div class="confirmation" role="group" aria-label="Confirmation">
                <p>{{ $value }}</p>
            </div>
        @endsession
        <h1>Portal Register</h1>

        @foreach ($errors->all() as $message)
        <div class="error" role="alert">
            <p>{{ $message }}</p>
        </div>
        @endforeach

        @guest
        <p>You are not logged in.</p>
        <ul class="external-login">
            <li>
                <form action="{{route('login')}}" method="post">
                    @csrf
                    <input type="text" name="kvk" value="{{ config('app.default_kvk_number') }}" />
                    <button>Login as an KVK user</button>
                </form>
            </li>
            @if (config('auth.oidc_enabled'))
            <li>
                <a href="{{ route('oidc.login') }}" class="button">Login with OIDC</a>
            </li>
            @endif

            @endguest
            @auth
            <p>You are logged in.</p>
            <span>
                Organization: {{ Auth::user()->getOrganization()->getName() }}
            </span>
            <a href="{{ route('portal.index') }}">Go to Portal</a>
            @endauth
    </div>
</section>
@endsection