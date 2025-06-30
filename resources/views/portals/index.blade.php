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

    @foreach ($errors->all() as $message)
        <div class="error" role="alert">
            <p>{{ $message }}</p>
        </div>
    @endforeach

    @session('success')
        <div class="confirmation" role="group" aria-label="Confirmation">
            <p>{{ $value }}</p>
        </div>
    @endsession

    <section class="layout-form">
        <div>
            <h1>PORTAL PAGE</h1>
            <h3>Available organization: {{ $organization->getName() }}</h3>

            <div class="column-2">
                <a href="{{ route('portal.edit-organization') }}">
                    <button type="button">Set up Organization</button>
                </a>
                <a href="{{ route('portal.edit-endpoint') }}">
                    <button type="button">Set up Endpoint</button>
                </a>
            </div>

            <div class="column-2">
                <form method="POST" action="{{ route('portal.delete-organization') }}" onsubmit="return confirm('Are you sure you want to delete the organization? This will also delete the associated endpoint and cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background-color: #dc3545; color: white;">Delete Organization</button>
                </form>
                @if($organization->getEndpoint())
                <form method="POST" action="{{ route('portal.delete-endpoint') }}" onsubmit="return confirm('Are you sure you want to delete the endpoint? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background-color: #dc3545; color: white;">Delete Endpoint</button>
                </form>
                @endif
            </div>
        </div>
    </section>
@endsection
