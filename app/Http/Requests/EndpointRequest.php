<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use App\Models\EndpointStatus;
use App\Models\EndpointConnectionTypes;

class EndpointRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'address.required' => 'Address is required',
            'address.url' => 'Address must be a valid URL',
            'address.max' => 'Address may not be greater than :max characters',
            'status.required' => 'Status is required',
            'status.in' => 'Status must be one of: :values',
            'period-start.date_format' => 'Period start must be in ISO 8601 
            format with timezone (e.g., 2025-06-24T10:30:00+02:00)',
            'period-end.date_format' => 'Period end must be in ISO 8601
            format with timezone (e.g., 2025-06-24T10:30:00+02:00)',
            'period-end.after_or_equal' => 'Period end must be after or equal to period start',
            'connectionType.required' => 'Connection type is required',
            'connectionType.in' => 'Connection type must be one of the valid types',
        ];
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'address' => [
                'required',
                'url',
                'max:1024',
                function (string $attribute, mixed $value, Closure $fail) {
                    $prefixes = ["https://"];
                    if (config('app.allow_insecure_endpoints') === true) {
                        $prefixes[] = "http://";
                    }
                    if (!Str::startsWith(strtolower($value), $prefixes)) {
                        $fail($attribute . ' must start with ' . implode(' or ', $prefixes));
                    }
                },
            ],
            'status' => [
                'required',
                'string',
                'in:' . implode(',', array_column(EndpointStatus::cases(), 'value')),
            ],
            'period-start' => [
                'nullable',
                'date_format:Y-m-d\TH:i:sP', // ISO 8601 format with timezone
            ],
            'period-end' => [
                'nullable',
                'date_format:Y-m-d\TH:i:sP', // ISO 8601 format with timezone
                'after_or_equal:period-start',
            ],
            'connectionType' => [
                'required',
                'string',
                'in:' . implode(',', EndpointConnectionTypes::getCodes()),
            ],
        ];
    }
}
