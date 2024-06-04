<?php

namespace App\Http\Requests;

use App\DTO\RoleDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateRoleRequest extends FormRequest
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
            'name' => 'required|unique:Roles',
            'encryption' => 'required|unique:Roles',
        ];
    }

    public function createDTO():RoleDTO {
        return new RoleDTO(
            $this->input('name'),
            $this->input('discription'),
            $this->input('encryption'),
            $this->input('created_by'),
            $this->input('deleted_by'),
        );
    }
}
