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
            <h1>Edit endpoint</h1>

            <form method="POST" action="{{ route('portal.update-endpoint') }}">
                @csrf
                <div class="form-group">
                    <h4>Address</h4>
                    <input type="text" id="address" name="address" value="{{ old('address', isset($endpoint) ? $endpoint->getAddress() : '') }}" required>
                    <h4>Status</h4>
                    <select id="status" name="status" required>
                        @foreach($statusOptions as $status)
                            <option value="{{ $status->value }}" {{ old('status', isset($endpoint) ? $endpoint->getStatus()->value : '') == $status->value ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('-', ' ', $status->value)) }}
                            </option>
                        @endforeach
                    </select>
                    <h4>Period</h4>
                    <div class="column-2">
                        <div>
                            <label for="period-start">Active from:</label>
                            <input type="text" id="period-start" name="period-start" 
                                   placeholder="2025-06-24T10:30:00+02:00"
                                   value="{{ old('period-start', isset($endpoint) && $endpoint->getPeriod() && $endpoint->getPeriod()->getStart() ? $endpoint->getPeriod()->getStart()->format('c') : '') }}" />
                            <small>Format: YYYY-MM-DDTHH:MM:SS+TZ (e.g., 2025-06-24T10:30:00+02:00)</small>
                        </div>
                        <div>
                            <label for="period-end">Active till:</label>
                            <input type="text" id="period-end" name="period-end" 
                                   placeholder="2025-06-24T15:30:00+02:00"
                                   value="{{ old('period-end', isset($endpoint) && $endpoint->getPeriod() && $endpoint->getPeriod()->getEnd() ? $endpoint->getPeriod()->getEnd()->format('c') : '') }}" />
                            <small>Format: YYYY-MM-DDTHH:MM:SS+TZ (e.g., 2025-06-24T15:30:00+02:00)</small>
                        </div>
                    </div>
                @php
                 $connectionType = isset($endpoint) ? $endpoint->getConnectionType() : null;
                 $selectedConnectionType = old('connectionType', $connectionType ? $connectionType->getCode() : '');
                @endphp
                <h4>Connection Type</h4>
                <div class="column-3"  id="connectionType-div">

                    <select id="connectionType" name="connectionType" required>
                        <option value="">Select a connection type</option>
                        @foreach($connectionTypeOptions as $code => $display)
                            <option value="{{ $code }}" {{ old('connectionType', $selectedConnectionType) == $code ? 'selected' : '' }}>
                                {{ $display }}
                            </option>
                        @endforeach
                    </select>
                </div>
                </div>

                <button type="submit">Update Endpoint</button>
            </form>
        </div>
    </section>
@endsection
