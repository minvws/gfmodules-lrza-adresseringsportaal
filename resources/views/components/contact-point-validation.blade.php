@props([
    'contactPoint',
    'fieldName' => 'contact_point'
])

@if($contactPoint && !$contactPoint->isValid())
    <div class="error" role="alert">
        <p><strong>ContactPoint validation errors:</strong></p>
        <ul>
            @foreach($contactPoint->getValidationErrors() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if($errors->has($fieldName))
    <div class="error" role="alert">
        @foreach($errors->get($fieldName) as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif
