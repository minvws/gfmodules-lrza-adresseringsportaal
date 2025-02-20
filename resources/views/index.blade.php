@extends('layouts.guest')

@section('content')
    @if (session()->has('error'))
        <section role="alert" class="error no-print" aria-label="{{ __('error') }}">
            <div>
                <h4>{{ session('error') }}</h4>
                <p>{{ session('error_description') }}</p>
            </div>
        </section>
    @endif

    <section>
        <div>
            <h1>Portal Register</h1>

            <p>Here be registrations of portals. Please note that the cake is a lie.</p>

            @foreach ($errors->all() as $message)
                <div class="error" role="alert">
                    <p>{{ $message }}</p>
                </div>
            @endforeach

            <form action="{{route('login.ura')}}" method="post">
                @csrf
                <input type="text" name="ura" value="{{ config('app.default_ura_number') }}"/>
                <button>Login as an URA user</button>
            </form>

            <form action="{{route('login.kvk')}}" method="post">
                @csrf
                <input type="text" name="kvk" value="{{ config('app.default_kvk_number') }}"/>
                <button>Login as an KVK user</button>
            </form>
        </div>
    </section>
@endsection
