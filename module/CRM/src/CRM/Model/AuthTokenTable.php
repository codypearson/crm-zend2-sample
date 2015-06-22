<?php
namespace CRM\Model;

use Zend\Db\TableGateway\TableGateway;

/**
 * Provides basic access to the auth_token table
 */
class AuthTokenTable
{
    protected $tableGateway;
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    /**
     * Get the token object for a provided token string
     * 
     * @param string $token
     * @return \CRM\Model\AuthToken
     * @throws \Exception
     */
    public function getTokenByToken($token)
    {
        $resultset = $this->tableGateway->select(array('token' => $token));
        $tokenobject = $resultset->current();
        if (!($tokenobject instanceof AuthToken))
        {
            throw new \Exception("Token $token not found.");
        }
        
        return $tokenobject;
    }
    
    /**
     * Get a token object by ID
     * 
     * @param int $id
     * @return \CRM\Model\AuthToken
     * @throws \Exception
     */
    public function getTokenById($id)
    {
        $resultset = $this->tableGateway->select(array('id' => (int) $id));
        $tokenobject = $resultset->current();
        if (!($tokenobject instanceof AuthToken))
        {
            throw new \Exception("Token ID $id not found.");
        }
        return $tokenobject;
    }
    
    /**
     * Save an AuthToken object to the DB.
     * 
     * @param \CRM\Model\AuthToken $tokenobject
     * @return int The ID of the saved token
     */
    public function saveToken(AuthToken $tokenobject)
    {
        $dataarray = array(
            'id' => $tokenobject->id,
            'username' => $tokenobject->username,
            'created_at' => ($tokenobject->createdAt instanceof \DateTime ? $tokenobject->createdAt->format('Y-m-d H:i:s') : null),
            'token' => $tokenobject->token
        );
        
        if ($tokenobject->id > 0)
        {
            // ID set? We're updating.
            $this->tableGateway->update($dataarray, array('id' => $tokenobject->id));
            $id = $tokenobject->id;
        } else 
        {
            // ID not set? Create a new token.
            $this->tableGateway->insert($dataarray);
            $id = $this->tableGateway->getLastInsertValue();
        }
        
        return $id;
    }
    
    /**
     * Generates a new, random token for the provided user.
     * 
     * @param \CRM\Model\User $user
     * @return AuthToken
     */
    public function createNewTokenForUser(User $user)
    {
        $tokenobject = new AuthToken();
        // The token string is a string randomly generated using OpenSSL
        $tokenobject->token = bin2hex(openssl_random_pseudo_bytes(64));
        $tokenobject->username = $user->username;
        $tokenobject->createdAt = new \DateTime();
        $tokenid = $this->saveToken($tokenobject);
        return $this->getTokenById($tokenid);
    }
}