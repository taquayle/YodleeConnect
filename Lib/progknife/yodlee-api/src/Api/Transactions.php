<?php

namespace YodleeApi\Api;

class Transactions extends ApiAbstract
{
    /**
     * Get all the transactions of the user in session.
     *
     * @param array
     * @return array
     */
    public function get(array $parameters = [])
    {
        $url = $this->getEndpoint('/transactions', $parameters);

        $requestHeaders = [
            $this->sessionManager->getAuthorizationHeaderString()
        ];
        
        $response = $this->httpClient->get($url, $requestHeaders);

        $response = json_decode($response);

        if (empty($response->transaction)) {

            return [];
        }
        
        return $response->transaction;
    }

    /**
    *   Default of 90 days of transaction history
    */
    public function getDefaultTransactions()
    {
        $DEFAULT_DAYS = 90;
        $parameters = [ 'fromDate' => date('Y-m-d', strtotime('-'.$DEFAULT_DAYS.' days')),
                        'toDate'   => date('Y-m-d')];
        return($this->get($parameters));
    }

    /**
    *   Get previous [months] worth of data, each month is considered 30 days
    *   regardless
    */
    public function getPreviousMonths($months)
    {
        $parameters = [ 'fromDate' => date('Y-m-d', strtotime('-'.$months.' months')),
                        'toDate'   => date('Y-m-d')];
        //var_dump($parameters);
        return($this->get($parameters));
    }

    public function getPreviousDays($days)
    {
        $parameters = [ 'fromDate' => date('Y-m-d', strtotime('-'.$days.' days')),
                        'toDate'   => date('Y-m-d')];
        //var_dump($parameters);
        return($this->get($parameters));
    }

    public function getPreviousYears($years)
    {
        /*$parameters = [ 'fromDate' => date('Y-m-d', strtotime('-'.$years.' years')),
                        'toDate'   => date('Y-m-d'),
                        'categoryType' => 'EXPENSE'];*/
        $parameters = [ 'fromDate' => date('Y-m-d', strtotime('-'.$years.' years')),
                        'toDate'   => date('Y-m-d')];
        //var_dump($parameters);
        return($this->get($parameters));
    }
}
