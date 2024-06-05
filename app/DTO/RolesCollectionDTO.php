<?php

namespace app\DTO;

use app\DTO\RoleDTO;

class RolesCollectionDTO
{
    public function __construct($roles)
    {
        $this->roles = $roles->map(function ($role) {
            return new RoleDTO(
                $role->name,
                $role->description,
                $role->encryption,
                $role->created_by,
                $role->deleted_by
            );
        });
    }
}
