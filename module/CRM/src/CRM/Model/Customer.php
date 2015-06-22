<?php
namespace CRM\Model;

/**
 * Represents an individual customer
 */
class Customer
{
    public $id;
    public $lastName;
    public $firstName;
    public $companyName;
    public $email;
    public $streetAddress;
    public $city;
    public $state;
    public $postalCode;
    public $phone;
    public $createdBy;
    public $createdAt;
    public $lastModified;
    
    /**
     * Populate the object based on a provided array
     * 
     * @param array $array
     */
    public function exchangeArray($array)
    {
        $this->id = (empty($array['id']) ? null : $array['id']);
        $this->lastName = (empty($array['last_name']) ? null : $array['last_name']);
        $this->firstName = (empty($array['first_name']) ? null : $array['first_name']);
        $this->companyName = (empty($array['company_name']) ? null : $array['company_name']);
        $this->email = (empty($array['email']) ? null : $array['email']);
        $this->streetAddress = (empty($array['street_address']) ? null : $array['street_address']);
        $this->city = (empty($array['city']) ? null : $array['city']);
        $this->state = (empty($array['state']) ? null : $array['state']);
        $this->postalCode = (empty($array['postal_code']) ? null : $array['postal_code']);
        $this->phone = (empty($array['phone']) ? null : $array['phone']);
        $this->createdBy = (empty($array['created_by']) ? null : $array['created_by']);
        $this->createdAt = (empty($array['created_at']) ? null : new \DateTime($array['created_at']));
        $this->lastModified = (empty($array['last_modified']) ? null : new \DateTime($array['last_modified']));
    }
    
    /**
     * Convert the object into an array
     * 
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->id,
            'last_name' => $this->lastName,
            'first_name' => $this->firstName,
            'company_name' => $this->companyName,
            'email' => $this->email,
            'street_address' => $this->streetAddress,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postalCode,
            'phone' => $this->phone,
            'created_by' => $this->createdBy,
            'created_at' => ($this->createdAt instanceof \DateTime ? $this->createdAt->format('Y-m-d H:i:s') : null),
            'last_modified' => ($this->lastModified instanceof \DateTime ? $this->lastModified->format('Y-m-d H:i:s') : null)
        );
    }
}
