<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganizationRequest extends FormRequest
{
    private const PHONE_FORMAT_ERROR = 'The contact detail format is invalid.
        It should comply with E.123 (inter)national notation.';
    private const PHONE_FORMAT_REGEX = '/^(?:\+(?=(?:.*\d){2,15}$)(?:\([0-9]{1,3}\)|[0-9]{1,3})' .
        '(?: [0-9]{1,12})+|(?=(?:.*\d){2,15}$)(?:\([0-9]{1,6}\)|[0-9]{1,6})(?: [0-9]{1,12})+)$/';
    private const DATETIME_REGEX = '/^([0-9]([0-9]([0-9][1-9]|[1-9]0)|[1-9]00)|[1-9]000)(-(0[1-9]|1[0-2])' .
     '(-(0[1-9]|[1-2][0-9]|3[0-1])(T([01][0-9]|2[0-3]):[0-5][0-9]:([0-5][0-9]|60) ' .
     '(\.[0-9]+)?(Z|(\+|-)((0[0-9]|1[0-3]):[0-5][0-9]|14:00)))?)?)?$/';
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
            'ura_identifier' => [
                'required',
                'digits_between:1,8',
                'regex:/^[0-9]+$/',
            ],
            'telecom.system' => 'nullable|string|in:phone,fax,email,pager,url,sms,other',
            'telecom.value' => 'nullable|string|max:255',
            'telecom.use' => 'nullable|string|in:work,temp,old,mobile',
            'telecom.rank' => 'nullable|integer|min:1|max:100',
            'telecom.period.start' => 'nullable|date',
            'telecom.period.end' => 'nullable|date|after:telecom.period.start',
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

            $regexRules = [
                'phone' => self::PHONE_FORMAT_REGEX,
                'fax'   => self::PHONE_FORMAT_REGEX,
                'pager' => self::PHONE_FORMAT_REGEX,
                'sms'   => self::PHONE_FORMAT_REGEX,
                'url'   => '/^((https?|ftp):\/\/)?(([\w-]+\.)+[a-z]{2,})(:\d{1,5})?(\/[^\s]*)?$/',
                'email' => '/^[\w\.-]+@[\w\.-]+\.\w{2,}$/',
            ];

            $addError = fn($field, $message) => $validator->errors()->add($field, $message);

            // Required value checks
            if ((isset($telecomData['system']) || isset($telecomData['use'])) && empty($telecomData['value'])) {
                $addError('telecom.value', 'The contact details are required when system or purpose is provided.');
            }

            // Regex validation for specific systems
            if (
                !empty($telecomData['system'])
                && !empty($telecomData['value'])
                && isset($regexRules[$telecomData['system']])
            ) {
                if (!preg_match($regexRules[$telecomData['system']], $telecomData['value'])) {
                    $messages = [
                        'phone' => self::PHONE_FORMAT_ERROR,
                        'fax'   => self::PHONE_FORMAT_ERROR,
                        'pager' => self::PHONE_FORMAT_ERROR,
                        'sms'   => self::PHONE_FORMAT_ERROR,
                        'url'   => 'The URL format is invalid.',
                        'email' => 'The email format is invalid.',
                    ];
                    $addError('telecom.value', $messages[$telecomData['system']]);
                }
            }

            // FHIR-specific rules
            if (!empty($telecomData['value']) && empty($telecomData['system'])) {
                $addError('telecom.system', 'A system is required if a value is provided.');
            }

            if (
                (!empty($telecomData['period']['start']) ||
                !empty($telecomData['period']['end'])) && empty($telecomData['value'])
            ) {
                $addError('telecom.value', 'A value is required if a period is provided.');
            }

            foreach (['start', 'end'] as $field) {
                if (!empty($telecomData['period'][$field])) {
                    if (!preg_match(self::DATETIME_REGEX, $telecomData['period'][$field])) {
                        $addError("telecom.period.{$field}", "The {$field} date format is invalid.");
                    }
                }
            }
        });
    }
}
