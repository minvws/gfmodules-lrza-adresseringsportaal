<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class EndpointRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'org_name.required' => 'Organization name is required',
            'org_name.min' => 'Organization name must be at least :min character',
            'org_name.max' => 'Organization name may not be greater than :max characters',
            'endpoint.required' => 'Endpoint is required',
            'endpoint.url' => 'Endpoint must be a valid URL',
            'endpoint.max' => 'Endpoint may not be greater than :max characters',
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
            'org_name' => [
                'required',
                'string',
                'min:1',
                'max:255',
            ],
            'id' => [
                'string',
                'nullable',
                'min:0',
                'max:255',
                'regex:/^[A-Za-z0-9\-\.]{1,64}$/',
            ],
            'endpoint' => [
                'required',
                'url',
                'max:1024',
                function ($attribute, $value, $fail) {
                    $prefixes = ["https://"];
                    if (config('app.allow_insecure_endpoints') === true) {
                        $prefixes[] = "http://";
                    }
                    if (!Str::startsWith(strtolower($value), $prefixes)) {
                        $fail($attribute . ' must start with https://');
                    }
                },
            ]
        ];
    }
}
