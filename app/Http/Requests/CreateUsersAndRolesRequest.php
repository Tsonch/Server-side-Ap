<?php

namespace App\Http\Requests;

use App\DTO\UsersAndRolesDTO;
use Illuminate\Foundation\Http\FormRequest;

class CreateUsersAndRolesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if(Auth::check()) {
            return true;
        }
        return false;
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
        ];
    }

    public function createDTO() : UsersAndRolesDTO {
        return new UsersAndRolesDTO(
            $this->input('role_id'), 
            $this->input('user_id'),
            $this->input('created_by'),
            $this->input('deleted_by')
        );
    }
}
