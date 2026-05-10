<?php

namespace App\Http\Requests;

use App\Models\Jar;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;


class StoreJarRequest extends FormRequest
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
        $types = ['public_jar', 'private_jar', 'friends_only_jar', 'invite_only_jar'];
        if (auth()->check() && auth()->user()->is_admin) {
            $types[] = 'official_jar';
        }

        return [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('jars')->where(function ($query) {
                    return $query->where('created_by', auth()->id());
                }),
            ],
            'description' => ['nullable', 'string', 'max:1500'],
            'picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'type' => ['required', Rule::in($types)],
        ];
    }
}
