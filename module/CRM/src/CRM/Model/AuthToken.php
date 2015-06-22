<?php
namespace CRM\Model;

/**
 * Represents an individual authentication token
 */
class AuthToken
{
    public $id;
    public $username;
    public $createdAt;
    public $token;
    
    /**
     * Load data into the object from an array
     * 
     * @param array $array
     */
    public function exchangeArray($array)
    {
        $this->id = (empty($array['id']) ? null : $array['id']);
        $this->username = (empty($array['username']) ? null : $array['username']);
        $this->createdAt = (empty($array['created_at']) ? null : new \DateTime($array['created_at']));
        $this->token = (empty($array['token']) ? null : $array['token']);
    }
}