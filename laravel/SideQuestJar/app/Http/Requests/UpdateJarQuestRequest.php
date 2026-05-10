<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJarQuestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //

            "title" => ["required", "string", "max:60"],
            "description" => ["nullable", "string", "max:1500"],
            'picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'], //Majd kitalalom mi legyen vele => kitalaltam
        ];
    }
}
