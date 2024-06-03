<?php
    namespace app\DTO;

    class RegistrationDTO {

        public $username;
        public $password;
        public $email;
        public $birthday;

        public function __construct($username, $password, $email, $birthday) 
        {
            $this->username = $username;
            $this->password = $password;
            $this->email = $email;
            $this->birthday = $birthday;
        }
    }