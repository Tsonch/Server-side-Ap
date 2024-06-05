<?php 
    namespace app\DTO;

    use app\DTO\RolesAndPermissionsDTO;

    class RolesAndPermissionsCollectionDTO {
        
        public function __construct($rolesAndPermissions)
        {
            $this->rolesAndPermissions = $rolesAndPermissions->map(function ($rAp) {
                return new RolesAndPermissionsDTO(
                    $rAp->role_id,
                    $rAp->permission_id,
                    $rAp->created_by,
                    $rAp->deleted_by
                );
            });
        }
    }