<?php
    namespace app\DTO;

    class UserDTO {

        public $id;
        public $username;
        public $email;
        public $dateOfBirth;
        public $created_at;
        public $roles;
        public $permissions;

        public function __construct($id, $username, $email, $dateOfBirth, $created_at) 
        {
            $this->id = $id;
            $this->username = $username;
            $this->email = $email;
            $this->dateOfBirth = $dateOfBirth;
            $this->created_at = $created_at;
        }
    }