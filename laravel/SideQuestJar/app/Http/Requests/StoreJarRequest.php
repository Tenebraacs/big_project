<?php

namespace App\Http\Requests;

use App\Models\Jar;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;


class StoreJarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        
        if(Auth::check() && Auth::user()->can('create', Jar::class)){
            return true;
        }
        else{
            return false;
        }
        

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
            //TODO: 14-19 videok //Puszi a Pocidra Voros

            "name" => ["required", "string"],
            "description" => ["nullable", "string"],
            "picture" => ["string", "nullable"],
            "type" => ["nullable",
                "in:public_jar,private_jar,friends_only_jar,invite_only_jar,official_jar"],

        ];
    }
}
