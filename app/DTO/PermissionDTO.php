<?php
    namespace app\DTO;

    class PermissionDTO {
        public $name;
        public $description;
        public $encryption;
        public $created_by;
        public $deleted_by;

        public function __construct($name, $description, $encryption, $created_by, $deleted_by)
        {
            $this->name = $name;
            $this->description = $description;
            $this->encryption = $encryption;
            $this->created_by = $created_by;
            $this->deleted_by = $deleted_by;
        }
    }
