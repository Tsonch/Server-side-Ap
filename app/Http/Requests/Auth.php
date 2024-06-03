<?php

namespace App\Http\Requests;

use App\DTO\AuthDTO;
use Illuminate\Foundation\Http\FormRequest;

class Auth extends FormRequest
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
            'username' => 'required|string|alpha|regex:/^[A-Z]/|min:7',
            'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
        ];
    }

    public function createDTO() : AuthDTO {
        return new AuthDTO(
            $this->input('username'),
            $this->input('password')
        );
    }
}
