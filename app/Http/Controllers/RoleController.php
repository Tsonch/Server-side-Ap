<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use Illuminate\Http\Request;
use App\DTO\RolesCollectionDTO;

class RoleController extends Controller
{
    public function getRoles(Request $request) {
        $roles = new RolesCollectionDTO(Roles::all());
        return response()->json($roles->roles);
    }

    public function getCurrentUserRole(Request $request) {
        $role = ;
        return reaponse()->json($role)
    }
}
