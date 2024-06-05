<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolesAndPermissions extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_id',
        'permission_id',
        'created_by',
        'deleted_by'
    ];
}
