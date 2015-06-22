<?php
namespace CRM\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use CRM\Model\UserTable;
use CRM\Model\AuthTokenTable;

/**
 * This controller manages authentication for the API.
 * It provides an authentication token in exchange for
 * a username and password.
 */
class AuthenticateController extends AbstractRestfulController
{
    protected $userTable;
    protected $authTokenTable;
    
    /**
     * Grabs an instance of the UserTable object
     * 
     * @return UserTable
     */
    protected function getUserTable()
    {
        if (!($this->userTable instanceof UserTable))
        {
            $this->userTable = $this->getServiceLocator()->get('CRM\Model\User');
        }
        
        return $this->userTable;
    }
    
    /**
     * Grabs an instance of the AuthTokenTable object
     * 
     * @return AuthTokenTable
     */
    protected function getAuthTokenTable()
    {
        if (!($this->authTokenTable instanceof AuthTokenTable))
        {
            $this->authTokenTable = $this->getServiceLocator()->get('CRM\Model\AuthToken');
        }
        
        return $this->authTokenTable;
    }
    
    /**
     * Logs in the user and provides him/her with
     * an auth token.
     * 
     * Must be executed as a POST request.
     * 
     * Expected params:
     * username
     * password
     * 
     * Response JSON format:
     * {
     *  success: true,
     *  token: 'token'
     * }
     * OR
     * {
     *  success: false,
     *  error: 'error'
     * }
     * 
     * @return \Zend\View\Model\JsonModel
     */
    public function indexAction()
    {
        $viewmodel = new JsonModel();
        $httpquery = $this->getRequest()->getPost();
        try
        {
            // Log in the user. An exception will be thrown if the login is not successful.
            $userobject = $this->getUserTable()->loginUser($httpquery->username, $httpquery->password);
            // Generate a new token for the user.
            $tokenobject = $this->getAuthTokenTable()->createNewTokenForUser($userobject);
            // Add the token to the response JSON object
            $viewmodel->token = $tokenobject->token;
            $viewmodel->success = true;
        } catch (\Exception $e)
        {
            // Catch the exception so the user knows it failed
            $viewmodel->success = false;
            $viewmodel->error = $e->getMessage();
        }
        
        return $viewmodel;
    }
}
