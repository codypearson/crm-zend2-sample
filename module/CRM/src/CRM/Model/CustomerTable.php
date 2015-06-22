<?php

namespace CRM\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Predicate\Like;

/**
 * Provides access to the customer table
 */
class CustomerTable
{
    protected $tableGateway;
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    /**
     * Get all customers in the database
     * 
     * @return ResultSet
     */
    public function getAllCustomers()
    {
        return $this->tableGateway->select();
    }
    
    /**
     * Get a customer by the ID
     * 
     * @param int $id
     * @return \CRM\Model\Customer
     * @throws \Exception
     */
    public function getCustomerById($id)
    {
        $resultset = $this->tableGateway->select(array('id' => (int) $id));
        $customer = $resultset->current();
        if (!($customer instanceof Customer)) {
            throw new \Exception("Customer ID $id not found.");
        }
        
        return $customer;
    }
    
    /**
     * Get all customers that have the provided e-mail address
     * 
     * @param string $email
     * @return ResultSet
     */
    public function getCustomersByEmail($email)
    {
        return $this->tableGateway->select(array('email' => $email));
    }
    
    /**
     * Return all customers whose last names contain the provided string
     * 
     * @param string $lastname
     * @return ResultSet
     */
    public function searchCustomersByLastName($lastname)
    {
        $likeclause = new Like('last_name', "%$lastname%");
        return $this->tableGateway->select($likeclause);
    }
    
    /**
     * 
     * @param int $id
     */
    public function deleteCustomerById($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
    
    /**
     * Update an existing customer, or create a new customer.
     * 
     * @param \CRM\Model\Customer $customer
     * @return \CRM\Model\Customer
     * @throws \Exception If a required field is missing
     */
    public function saveCustomer(Customer $customer)
    {
        $missingfields = array();
        // Update the last modified timestamp
        $customer->lastModified = new \DateTime();
        if (!$customer->email)
        {
            // Email is a required field
            $missingfields[] = 'email';
        }
        if (!$customer->lastName)
        {
            // So is last name
            $missingfields[] = 'last_name';
        }
        if (count($missingfields) > 0)
        {
            throw new \Exception('The following fields are required: '.implode(', ', $missingfields));
        }
        if ($customer->id > 0)
        {
            // The ID is set, go ahead and update
            $this->tableGateway->update($customer->toArray(), array('id' => $customer->id));
        } else
        {
            // No ID, create a new record.
            $customer->createdAt = new \DateTime();
            $this->tableGateway->insert($customer->toArray());
            $customer->id = $this->tableGateway->getLastInsertValue();
        }
        
        return $customer;
    }
}