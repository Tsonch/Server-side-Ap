<?php 
    namespace app\DTO;

    class RoleDTO {
        public $name;
        public $discription;
        public $encryption;
        public $created_by;
        public $deleted_by;

        public function __construct($name, $discription, $encryption, $created_by, $deleted_by)
        {
            $this->name = $name;
            $this->discription = $discription;
            $this->encryption = $encryption;
            $this->created_by = $created_by;
            $this->deleted_by = $deleted_by;
        }
    }