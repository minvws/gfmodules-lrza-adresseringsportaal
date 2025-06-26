<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganizationRequest extends FormRequest
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
            'org_name.required' => 'Organization name is required',
            'org_name.min' => 'Organization name must be at least :min character',
            'org_name.max' => 'Organization name may not be greater than :max characters',
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
            'telecom.system' => 'nullable|string|in:phone,fax,email,pager,url,sms,other',
            'telecom.value' => 'nullable|string|max:255',
            'telecom.use' => 'nullable|string|in:work,temp,old,mobile',
            'telecom.rank' => 'nullable|integer|min:1',
            'telecom.period.start' => 'nullable|date',
            'telecom.period.end' => 'nullable|date|after_or_equal:telecom.period.start',
        ];
    }

    /**
     * Configure the validator instance.
     * @param \Illuminate\Contracts\Validation\Validator $validator
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $telecomData = $this->input('telecom', []);

            // FHIR Rule: A system is required if a value is provided
            if (!empty($telecomData['value']) && empty($telecomData['system'])) {
                $validator->errors()->add('telecom.system', 'A system is required if a value is provided.');
            }
        });
    }
}
