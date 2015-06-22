<?php
namespace CRM\Model;

/**
 * Represents an individual user
 */
class User
{
    public $username;
    public $password;
    
    /**
     * Populate the user object based on a provided array
     * 
     * @param array $array
     */
    public function exchangeArray($array)
    {
        $this->username = (empty($array['username']) ? null : $array['username']);
        $this->password = (empty($array['password']) ? null : $array['password']);
    }
}