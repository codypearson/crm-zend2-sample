<?php
namespace CRM\Model;

use Zend\Db\TableGateway\TableGateway;

/**
 * Provide access to the user table
 */
class UserTable
{
    protected $tableGateway;
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    /**
     * Get a user based on his/her username
     * 
     * @param string $username
     * @return \CRM\Model\User
     * @throws \Exception If the user is not found
     */
    public function getUser($username)
    {
        $rowset = $this->tableGateway->select(array('username' => $username));
        $row = $rowset->current();
        if (!($row instanceof User))
        {
            throw new \Exception("Could not find user $username!");
        }
        return $row;
    }
    
    /**
     * Check a username and password for accuracy
     * 
     * @param string $username
     * @param string $password
     * @return \CRM\Model\User
     * @throws \Exception If the user does not exist, or the password doesn't match
     */
    public function loginUser($username, $password)
    {
        // Passwords are hashed in the DB using the SHA2-256 algorithm
        $passwordhash = hash('sha256', $password);
        $rowset = $this->tableGateway->select(array('username' => $username, 'password' => $passwordhash));
        $row = $rowset->current();
        if (!($row instanceof User))
        {
            throw new \Exception("Invalid username and/or password.");
        }
        
        return $row;
    }
}