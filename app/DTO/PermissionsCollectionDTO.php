<?php

    namespace app\DTO;

    use app\DTO\PermissionDTO;

    class PermissionsCollectionDTO
    {
        public function __construct($permissions)
        {
            $this->permissions = $permissions->map(function ($permission) {
                return new PermissionDTO(
                    $permission->name,
                    $permission->description,
                    $permission->encryption,
                    $permission->created_by,
                    $permission->deleted_by
                );
            });
        }
    }
