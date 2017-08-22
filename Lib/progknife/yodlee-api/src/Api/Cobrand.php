<?php

namespace YodleeApi\Api;

class Cobrand extends ApiAbstract
{
    /**
     * Authenticate cobrand to get cobrand session token.
     *
     * @param string
     * @param string
     * @return bool
     */
    public function login($cobrandLogin, $cobrandPassword)
    {
        $url = $this->getEndpoint('/cobrand/login');
        
        return $this->verifyLogin($this->postLogin($url, $cobrandLogin, $cobrandPassword));
    }

    /**
    *   Verify the request, if empty throw an error
    */
    private function verifyLogin($response)
    {
        $response = json_decode($response);
        if (empty($response->session->cobSession)) {
            return false;}

        $this->sessionManager->setCobrandSessionToken(
            $response->session->cobSession);

        return true;
    }

    /**
    *   Post to yodlee servers
    */
    private function postLogin($url, $cobrandLogin, $cobrandPassword)
    {
        $response = $this->httpClient->post($url, [
            'cobrandLogin'    => $cobrandLogin,
            'cobrandPassword' => $cobrandPassword
        ]);

        return $response;
    }

    /**
    *   Print out the response from the server
    */
    public function printLogin($cobrandLogin, $cobrandPassword)
    {
        $url = $this->getEndpoint('/cobrand/login');
        $response = $this->postLogin($url, $cobrandLogin, $cobrandPassword);

        print 'COBRAND LOGIN DETAILS<pre>URL:  ';
        print $url;
        print '<br>Name:  ';
        print $cobrandLogin;
        print '<br>Password:  ';
        print $cobrandPassword;
        print '<br><br>';
        var_dump($response);
        print '</pre>';

        return $this->verifyLogin($response);
    }

    /**
     * Log cobrand out of the Yodlee system.
     *
     * This also unsets the cobrand and user session tokens from the session
     * manager.
     */
    public function logout()
    {
        $url = $this->getEndpoint('/cobrand/logout');

        $requestHeaders = [
            $this->sessionManager->getAuthorizationHeaderString()
        ];

        $this->httpClient->post($url, null, $requestHeaders);

        $this->sessionManager->setUserSessionToken('');
        $this->sessionManager->setCobrandSessionToken('');
    }

    /**
     * Get the public key.
     *
     * @see https://developer.yodlee.com/apidocs/index.php#Encryption
     *
     * @return \stdClass
     */
    public function publicKey()
    {
        $url = $this->getEndpoint('/cobrand/publicKey');

        $requestHeaders = [
            $this->sessionManager->getAuthorizationHeaderString()
        ];

        $response = $this->httpClient->get($url, $requestHeaders);

        $response = json_decode($response);

        return $response;
    }
}
