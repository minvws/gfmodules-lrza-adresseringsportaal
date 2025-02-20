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

    @session('success')
    <div class="confirmation" role="group" aria-label="Confirmation">
        <p>{{ $value }}</p>
    </div>
    @endsession

    <section class="layout-form">
        <div>
            <h1>KVK PORTAL PAGE</h1>

            @foreach ($errors->all() as $message)
                <div class="error" role="alert">
                    <p>{{ $message }}</p>
                </div>
            @endforeach

            <form action="{{route('portal.kvk.index')}}" method="post">
                @csrf

                <label for="endpoint">Supplier endpoint</label>
                <div>
                    <span class="nota-bene" id="endpoint-explanation">
                        Enter the endpoint (starting with https://) of the supplier you want to use.
                    </span>
                    <input
                        id="endpoint"
                        name="endpoint"
                        type="text"
                        value="{{ old('endpoint') ?? $kvk_user->suppliers[0]->endpoint ?? '' }}"
                        aria-describedby="endpoint-explanation"
                    />
                </div>

                <button type="submit">Verzend</button>
            </form>
        </div>
    </section>
@endsection
