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
            <h1>PORTAL PAGE</h1>
            <h2>Organization: {{ $organization->getName() }}</h2>

            @foreach ($errors->all() as $message)
                <div class="error" role="alert">
                    <p>{{ $message }}</p>
                </div>
            @endforeach

            <form action="{{route('portal.index')}}" method="post">
                @csrf

                <label for="org_name">Organization</label>
                <div>
                    <span class="nota-bene" id="org_name-explanation">
                        Organization name
                    </span>
                    <input
                        id="org_name"
                        name="org_name"
                        type="text"
                        value="{{ old('org_name') ?? $organization->getName() }}"
                        aria-describedby="org_name"
                    />
                </div>

                <label for="endpoint">Supplier endpoint</label>
                <div>
                    <span class="nota-bene" id="endpoint-explanation">
                        Enter the endpoint (starting with https://) of the supplier you want to use.
                    </span>
                    <input type="hidden" name="id" value="{{ count($organization->getEndpoints()) > 0 ? $organization->getEndpoints()[0]->getId() : '' }}"/>
                    <input
                        id="endpoint"
                        name="endpoint"
                        type="text"
                        value="{{ old('endpoint') ?? (count($organization->getEndpoints()) > 0 ? $organization->getEndpoints()[0]->getAddress() : '') }}"
                        aria-describedby="endpoint"
                    />
                </div>

                <button type="submit">Verzend</button>
            </form>
        </div>
    </section>
@endsection
