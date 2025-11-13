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

            <h1>PORTAL PAGE</h1>
            <h3>Available organization: {{ $organization->getName() }}</h3>

            <div class="column-2">
                <div>
                <a class="button" href="{{ route('portal.edit-organization') }}">
                    Set up Organization
                </a>
                <button href="{{ route('portal.delete-organization') }}"
                    class="destructive"
                    onclick="event.preventDefault(); if(confirm('Are you sure you want to delete the organization? This will also delete the associated endpoint and cannot be undone.')) { document.getElementById('delete-organization-form').submit(); }">
                     Delete Organization
                </button>
                <form id="delete-organization-form" method="POST" action="{{ route('portal.delete-organization') }}" style="display: none;">
                     @csrf
                     @method('DELETE')
                </form>
                </div>
                <div>
                <a class="button" href="{{ route('portal.edit-endpoint') }}">
                    Set up Endpoint
                </a>
                @if($organization->getEndpoint())

                <button href="{{ route('portal.delete-endpoint') }}"
                    class="destructive"
                    onclick="event.preventDefault(); if(confirm('Are you sure you want to delete the endpoint? This action cannot be undone.')) { document.getElementById('delete-endpoint-form').submit(); }">
                     Delete Endpoint
                </button>
                <form id="delete-endpoint-form" method="POST" action="{{ route('portal.delete-endpoint') }}" style="display: none;">
                     @csrf
                     @method('DELETE')
                </form>
                @endif
            </div>
        </div>
</div>
</section>
@endsection
