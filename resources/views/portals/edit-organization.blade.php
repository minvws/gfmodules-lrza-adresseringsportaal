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
            <h1>Edit organization</h1>

            <form method="POST" action="{{ route('portal.update-organization') }}">
                @csrf
                <input type="hidden" name="id" value="{{ $organization->getId() }}">

                <div class="form-group">
                    <label for="ura_identifier">URA Identifier (max 8 digits)</label>
                    <input type="text" id="ura_identifier" name="ura_identifier" value="{{ old('ura_identifier', $organization->getUraIdentifier()) }}" maxlength="8" pattern="[0-9]{1,8}" required placeholder="e.g. 12345678">
                </div>

                <div class="form-group">
                    <label for="org_name">Organization Name</label>
                    <input type="text" id="org_name" name="org_name" value="{{ old('org_name', $organization->getName()) }}" required>
                </div>

                <x-contact-point 
                    name="telecom" 
                    label="Organization Contact Information"
                    :system="$organization->getTelecom()?->getSystem()?->value"
                    :value="$organization->getTelecom()?->getValue()"
                    :use="$organization->getTelecom()?->getUse()?->value"
                    :rank="$organization->getTelecom()?->getRank()"
                    :period-start="$organization->getTelecom()?->getPeriod()?->getStart()?->format('c')"
                    :period-end="$organization->getTelecom()?->getPeriod()?->getEnd()?->format('c')"
                    :system-options="\App\Models\ContactPointSystem::getAllAsArray()" 
                    :use-options="\App\Models\ContactPointUse::getAllAsArray()"
                />

                <button type="submit">Update Organization</button>
            </form>
        </div>
    </section>
@endsection
