<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SearchQueriesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $queries = collect($this->input('queries', []))
            ->map(fn ($query) => is_string($query) ? trim($query) : $query)
            ->filter(fn ($query) => filled($query))
            ->values()
            ->all();

        $this->merge(['queries' => $queries]);
    }

    public function rules(): array
    {
        return [
            'queries' => ['required', 'array', 'min:1', 'max:5'],
            'queries.*' => ['required', 'string', 'min:2', 'max:200', 'regex:/^[\pL\pN\s\-\+\&\.\"\'\,\!\?\(\):;#@\/]+$/u'],
        ];
    }

    public function messages(): array
    {
        return [
            'queries.required' => 'Please enter at least one search query.',
            'queries.min' => 'Please enter at least one search query.',
            'queries.max' => 'You can search a maximum of 5 queries at a time.',
            'queries.*.required' => 'Each search query must not be empty.',
            'queries.*.min' => 'Each search query must be at least 2 characters.',
            'queries.*.max' => 'Each search query may not be greater than 200 characters.',
            'queries.*.regex' => 'One or more queries contain invalid characters.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $queries = $this->input('queries', []);

            if (count($queries) !== count(array_unique(array_map('mb_strtolower', $queries)))) {
                $validator->errors()->add('queries', 'Duplicate search queries are not allowed.');
            }
        });
    }
}
