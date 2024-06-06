<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Roles extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'encryption',
        'created_by',
        'deleted_by',
        'deleted_at'
    ];

    public function permissions() {
        $role_id = $this->id;

        $permissions_id = RolesAndPermissions::select('permission_id')->where('role_id', $role_id)->get();

        $permissions = $permissions_id->map(function ($id) {
            return Permissions::where('id', $id->permission_id)->first();
        });

        return $permissions;
    }
}
