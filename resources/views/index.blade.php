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

            <a href="{{ route('portals.index') }}">To the portal registration &raquo;</a>
        </div>
    </section>
@endsection
