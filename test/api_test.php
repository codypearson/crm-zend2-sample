<?php
const TEST_USERNAME = 'testuser';
const TEST_PASS = 't3stp455';
const AUTH_URL = 'http://crm.codypearson.com/crm/authenticate';
const CUSTOMER_URL = 'http://crm.codypearson.com/crm/customers';
const DELETE_CUSTOMER_URL = 'http://crm.codypearson.com/crm/customers/delete';

echo "Authenticating:\n";
$authSession = curl_init(AUTH_URL);
curl_setopt($authSession, CURLOPT_POST, true);
curl_setopt($authSession, CURLOPT_RETURNTRANSFER, true);
curl_setopt($authSession, CURLOPT_POSTFIELDS, array(
    'username' => TEST_USERNAME,
    'password' => TEST_PASS
));
$authResult = json_decode(curl_exec($authSession));
curl_close($authSession);
echo "Result:\n";
print_r($authResult);

if (!$authResult->token)
{
    echo "Error: Failed to obtain authentication token.\n";
    exit();
}

echo "Creating a new customer:\n";
$createCustomerSession = curl_init(CUSTOMER_URL);
curl_setopt($createCustomerSession, CURLOPT_POST, true);
curl_setopt($createCustomerSession, CURLOPT_RETURNTRANSFER, true);
curl_setopt($createCustomerSession, CURLOPT_POSTFIELDS, array(
    'token' => $authResult->token,
    'customer' => json_encode(array(
        'last_name' => 'Simpson',
        'first_name' => 'Homer',
        'email' => 'homer@thesimpsons.com',
        'street_address' => '742 Evergreen Terrace',
        'city' => 'Springfield',
        'state' => 'NT',
        'postal_code' => '49007',
        'company_name' => 'Springfield Nuclear Power Plant',
        'phone' => '(939) 555-0113'
    ))
));

$createResult = json_decode(curl_exec($createCustomerSession));
curl_close($createCustomerSession);
echo "Result:\n";
print_r($createResult);

$testUser = $createResult->customers[0];

echo "Listing all customers:\n";
$listAllSession = curl_init(CUSTOMER_URL . '?' . http_build_query(array(
    'token' => $authResult->token
)));
curl_setopt($listAllSession, CURLOPT_RETURNTRANSFER, true);
$listAllResult = json_decode(curl_exec($listAllSession));
curl_close($listAllSession);
echo "Result:\n";
print_r($listAllResult);

echo "Retrieving test user by ID:\n";
$retrieveIdSession = curl_init(CUSTOMER_URL . '?' . http_build_query(array(
    'token' => $authResult->token,
    'id' => $testUser->id
)));
curl_setopt($retrieveIdSession, CURLOPT_RETURNTRANSFER, true);
$retrieveIdResult = json_decode(curl_exec($retrieveIdSession));
curl_close($retrieveIdSession);
echo "Result:\n";
print_r($retrieveIdResult);

echo "Retrieving test user by e-mail:\n";
$retrieveEmailSession = curl_init(CUSTOMER_URL . '?' . http_build_query(array(
    'token' => $authResult->token,
    'email' => $testUser->email
)));
curl_setopt($retrieveEmailSession, CURLOPT_RETURNTRANSFER, true);
$retrieveEmailResult = json_decode(curl_exec($retrieveEmailSession));
curl_close($retrieveEmailSession);
echo "Result:\n";
print_r($retrieveEmailResult);

echo "Searching for test user by last name (partial):\n";
$searchNameSession = curl_init(CUSTOMER_URL . '?' . http_build_query(array(
    'token' => $authResult->token,
    'lastname' => 'imp'
)));
curl_setopt($searchNameSession, CURLOPT_RETURNTRANSFER, true);
$searchNameResult = json_decode(curl_exec($searchNameSession));
curl_close($searchNameSession);
echo "Result:\n";
print_r($searchNameResult);

echo "Modifying the test user:\n";
$testUser->first_name = 'Marge';
$modifySession = curl_init(CUSTOMER_URL);
curl_setopt($modifySession, CURLOPT_POST, true);
curl_setopt($modifySession, CURLOPT_RETURNTRANSFER, true);
curl_setopt($modifySession, CURLOPT_POSTFIELDS, array(
    'token' => $authResult->token,
    'customer' => json_encode($testUser)
));
$modifyResult = json_decode(curl_exec($modifySession));
curl_close($modifySession);
echo "Result:\n";
print_r($modifyResult);

echo "Deleting the test user:\n";
$deleteSession = curl_init(DELETE_CUSTOMER_URL);
curl_setopt($deleteSession, CURLOPT_POST, true);
curl_setopt($deleteSession, CURLOPT_RETURNTRANSFER, true);
curl_setopt($deleteSession, CURLOPT_POSTFIELDS, array(
    'token' => $authResult->token,
    'id' => $testUser->id
));
$deleteResult = json_decode(curl_exec($deleteSession));
echo "Result:\n";
print_r($deleteResult);

echo "Done!\n";