<?php
namespace CRM\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Http\Request;
use Zend\Json\Json;
use CRM\Model\Customer;
use CRM\Model\CustomerTable;
use CRM\Model\AuthTokenTable;

/**
 * Handle all CRUD options for customers.
 */
class CustomersController extends AbstractRestfulController
{
    protected $customerTable;
    protected $authTokenTable;
    
    /**
     * Not sure if it was a GET or a POST request?
     * This will provide the parameters in either case.
     * 
     * @return \Zend\Stdlib\ParametersInterface
     */
    protected function getParams()
    {
        if ($this->getRequest()->isPost())
        {
            return $this->getRequest()->getPost();
        } elseif ($this->getRequest()->isGet())
        {
            return $this->getRequest()->getQuery();
        }
    }
    
    /**
     * Grab an instance of the CustomerTable object
     * 
     * @return CustomerTable
     */
    protected function getCustomerTable()
    {
        if (!($this->customerTable instanceof CustomerTable))
        {
            $this->customerTable = $this->getServiceLocator()->get('CRM\Model\Customer');
        }
        
        return $this->customerTable;
    }
    
    /**
     * Grab an instance of the AuthTokenTable object
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
     * Check to see if a valid authentication token was provided.
     * 
     * @return mixed The logged-in username if the token is valid, otherwise false
     */
    protected function checkAuthToken()
    {
        try
        {
            $tokenobject = $this->getAuthTokenTable()->getTokenByToken($this->getParams()->token);
            return $tokenobject->username;
        } catch (\Exception $e)
        {
            return false;
        }
    }
    
    /**
     * Handles create, read, and update actions for customers.
     * 
     * Expected params for all cases:
     * token
     * 
     * When using a GET request, retrieves existing customers.
     * With no additional params: Get all users.
     * With the 'id' param: Get the identified user.
     * With the 'email' param: Get any users with the specified email.
     * With the 'lastname' param: Get all users whose last name contains the specified string.
     * 
     * When using a POST request, creates or updates customers.
     * Param: 'customer' containing a JSON object as follows:
     * {
     *  id: '',
     *  last_name: '',
     *  first_name: '',
     *  email: '',
     *  street_address: '',
     *  city: '',
     *  state: '',
     *  postal_code: '',
     *  company_name: '',
     *  phone: ''
     * }
     * 
     * If the ID is set, attempt to update the existing customer with the specified ID.
     * Otherwise, create a new customer.
     * 
     * Response JSON:
     * {
     *  customers: Array of customers (see above format)
     * }
     * 
     * @return \Zend\View\Model\JsonModel
     */
    public function indexAction()
    {
        $viewmodel = new JsonModel();
        $username = $this->checkAuthToken();
        if ($username)
        {
            $request = $this->getRequest();
            $httpmethod = $request->getMethod();
            switch ($httpmethod)
            {
                case Request::METHOD_GET:
                    // GET requests are for retrieving customers
                    $httpquery = $request->getQuery();
                    if ($httpquery->id > 0)
                    {
                        // ID provided? Get the customer with that ID
                        $this->loadCustomerById($viewmodel, $httpquery->id);
                    } elseif ($httpquery->email)
                    {
                        // E-mail address provided? List customers with that e-mail (exact match)
                        $this->loadCustomersByEmail($viewmodel, $httpquery->email);
                    } elseif ($httpquery->lastname)
                    {
                        // Last name provided? List customers whose last names contain the provided string
                        $this->searchCustomersByLastName($viewmodel, $httpquery->lastname);
                    } else
                    {
                        // Nothing provided? List all customers.
                        $this->loadAllCustomers($viewmodel);
                    }
                break;
                case Request::METHOD_POST:
                    // A POST request means we want to save a customer.
                    $this->saveCustomer($viewmodel, $username);
                break;
            }
        } else
        {
            $viewmodel->error = "You must supply a valid authentication token.";
        }
        return $viewmodel;
    }
    
    /**
     * Delete a customer by ID.
     * 
     * Expected params:
     * token
     * id
     * 
     * Response JSON:
     * {
     *  success: true|false,
     *  error: ''
     * }
     * 
     * @return \Zend\View\Model\JsonModel
     */
    public function deleteAction()
    {
        $viewmodel = new JsonModel();
        if ($this->checkAuthToken())
        {
            $this->getCustomerTable()->deleteCustomerById($this->getRequest()->getPost()->id);
            $viewmodel->success = true;
        } else
        {
            $viewmodel->success = false;
            $viewmodel->error = "You must supply a valid authentication token.";
        }
        
        return $viewmodel;
    }
    
    /**
     * Load all customers in the database into the JSON response.
     * 
     * @param \Zend\View\Model\ViewModel $viewmodel
     */
    protected function loadAllCustomers(ViewModel &$viewmodel)
    {
        $customertable = $this->getCustomerTable();
        $viewmodel->customers = $customertable->getAllCustomers()->toArray();
    }
    
    /**
     * Load the customer with the specified ID into the JSON response.
     * 
     * @param \Zend\View\Model\ViewModel $viewmodel
     * @param int $id
     */
    protected function loadCustomerById(ViewModel &$viewmodel, $id)
    {
        try
        {
            $customerobject = $this->getCustomerTable()->getCustomerById($id);
            $viewmodel->customers = array($customerobject->toArray());
        } catch (\Exception $e) {
            $viewmodel->customers = array();
        }
    }
    
    /**
     * Load customers with the specified e-mail address into the JSON response.
     * 
     * @param \Zend\View\Model\ViewModel $viewmodel
     * @param string $email
     */
    protected function loadCustomersByEmail(ViewModel &$viewmodel, $email)
    {
        $viewmodel->customers = $this->getCustomerTable()->getCustomersByEmail($email)->toArray();
    }
    
    /**
     * Load all customers whose last names contain the specified string into the JSON response.
     * 
     * @param \Zend\View\Model\ViewModel $viewmodel
     * @param string $lastname
     */
    protected function searchCustomersByLastName(ViewModel &$viewmodel, $lastname)
    {
        $viewmodel->customers = $this->getCustomerTable()->searchCustomersByLastName($lastname)->toArray();
    }
    
    /**
     * Grab the JSON object from the POST params and save the resulting customer.
     * 
     * @param \Zend\View\Model\ViewModel $viewmodel
     * @param string $username The user who created the customer
     */
    protected function saveCustomer(ViewModel &$viewmodel, $username)
    {
        $customer = new Customer();
        $customerarray = Json::decode($this->getRequest()->getPost()->customer, Json::TYPE_ARRAY);
        // Populate the customer object with the data provided by the user
        $customer->exchangeArray($customerarray);
        $customer->createdBy = $username;
        try
        {
            $this->getCustomerTable()->saveCustomer($customer);
            $viewmodel->customers = array($customer->toArray());
            $viewmodel->success = true;
        } catch (\Exception $e)
        {
            $viewmodel->success = false;
            $viewmodel->error = $e->getMessage();
        }
    }
}