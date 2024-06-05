<?php 
    namespace app\DTO;

    use app\DTO\UsersAndRolesDTO;

    class UsersAndRolesCollectionDTO {
        public function __construct($usersAndRoles)
        {
            $this->usersAndRoles = $usersAndRoles->map(function ($uAr) {
                return new UsersAndRolesDTO(
                    $uAr->user_id,
                    $uAr->role_id,
                    $uAr->created_by,
                    $uAr->deleted_by
                );
            });
        }
    }