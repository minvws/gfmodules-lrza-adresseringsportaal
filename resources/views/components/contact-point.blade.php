@props([
    'name' => 'contact_point',
    'label' => 'Contact Point',
    'system' => null,
    'value' => null,
    'use' => null,
    'rank' => null,
    'periodStart' => null,
    'periodEnd' => null,
    'required' => false,
    'systemOptions' => [],
    'useOptions' => [],
])

<div class="form-group">
    <h4>{{ $label }}</h4>
    
    <div class="column-2">
        <div>
            <label for="{{ $name }}_system">System {{ $required ? '*' : '' }}</label>
            <select id="{{ $name }}_system" name="{{ $name }}[system]" {{ $required ? 'required' : '' }}>
                <option value="">Select contact method</option>
                @foreach($systemOptions as $code => $display)
                    <option value="{{ $code }}" {{ old($name.'.system', $system) == $code ? 'selected' : '' }}>
                        {{ $display }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label for="{{ $name }}_value">Contact Details</label>
            <input 
                type="text" 
                id="{{ $name }}_value" 
                name="{{ $name }}[value]" 
                value="{{ old($name.'.value', $value) }}"
                placeholder="Enter contact details"
            />
        </div>
    </div>

    <div class="column-3">
        <div>
            <label for="{{ $name }}_use">Purpose</label>
            <select id="{{ $name }}_use" name="{{ $name }}[use]">
                <option value="">Select purpose</option>
                @foreach($useOptions as $code => $display)
                    <option value="{{ $code }}" {{ old($name.'.use', $use) == $code ? 'selected' : '' }}>
                        {{ $display }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label for="{{ $name }}_rank">Preference Order</label>
            <input 
                type="number" 
                id="{{ $name }}_rank" 
                name="{{ $name }}[rank]" 
                value="{{ old($name.'.rank', $rank) }}"
                min="1"
                placeholder="1 = highest"
            />
            <small>1 = highest priority</small>
        </div>

        <div>
        </div>
    </div>

    <div class="column-2">
        <div>
            <label for="{{ $name }}_period_start">Active from</label>
            <input 
                type="text" 
                id="{{ $name }}_period_start" 
                name="{{ $name }}[period][start]" 
                placeholder="2025-06-24T10:30:00+02:00"
                value="{{ old($name.'.period.start', $periodStart) }}" 
            />
            <small>Format: YYYY-MM-DDTHH:MM:SS+TZ (e.g., 2025-06-24T10:30:00+02:00)</small>
        </div>
        
        <div>
            <label for="{{ $name }}_period_end">Active till</label>
            <input 
                type="text" 
                id="{{ $name }}_period_end" 
                name="{{ $name }}[period][end]" 
                placeholder="2025-06-24T15:30:00+02:00"
                value="{{ old($name.'.period.end', $periodEnd) }}" 
            />
            <small>Format: YYYY-MM-DDTHH:MM:SS+TZ (e.g., 2025-06-24T15:30:00+02:00)</small>
        </div>
    </div>
</div>
