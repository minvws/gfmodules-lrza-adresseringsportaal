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
            <h1>PORTAL AUTH PAGE</h1>

            @foreach ($errors->all() as $message)
                <div class="error" role="alert">
                    <p>{{ $message }}</p>
                </div>
            @endforeach

            <table>
                <tr><td>Logged in URA</td><td>{{ Auth::user()->ura_number }}</td></tr>
            </table>

            <form action="{{route('portals.index')}}" method="post">
                @csrf
                <label for="form-example-base">Supplier endpoint</label>

                <div>
                    <span class="nota-bene" id="form-example-base-explanation">
                        Enter the endpoint (starting with https://) of the supplier you want to use.
                    </span>
                    <input
                        id="endpoint"
                        name="endpoint"
                        type="text"
                        value="{{ old('endpoint') ?? $ura->suppliers[0]->endpoint ?? '' }}"
                        aria-describedby="endpoint"
                    />
                </div>

                <button type="submit">Verzend</button>
            </form>
        </div>
    </section>
@endsection
