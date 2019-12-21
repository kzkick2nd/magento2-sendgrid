<?php

namespace SendGrid\EmailDeliverySimplified\Helper;

use \Psr\Log\LoggerInterface;

class Tools
{
  /**
   * @const   string  Base URL for the SendGrid API
   */
    const SG_API_URL  = 'https://api.sendgrid.com/';

  /**
   * @const   string  Reason for invalid API Key
   */
    const SG_REASON_INVALID_KEY  = 'invalid_key';

  /**
   * @const   string  Reason for no scopes on API Key
   */
    const SG_REASON_NO_SCOPES  = 'no_scopes';

  /**
   * @const   string  Valid response
   */
    const SG_VALID  = 'valid';

  /**
   * @var array
   */
    private $_requiredAPIKeyScopes;

  /**
   * @var array
   */
    private $_requiredUnsubscribeScope;

  /**
   * @var array
   */
    private $_lastScopeResult;

  /**
   * @var \Psr\Log\LoggerInterface
   */
    protected $_logger;

  /**
   * Constructor for the General Settings Block
   */
    public function __construct(LoggerInterface $loggerInterface)
    {
        $this->_logger = $loggerInterface;

        // API Key required scopes
        $this->_requiredAPIKeyScopes = [ 'mail.send', 'stats.read', 'categories.stats.read', 'categories.stats.sums.read' ];
        // API Key unsubscribe scope
        $this->_requiredUnsubscribeScope = [ 'asm.groups.read' ];
        // scoopes
        $this->_lastScopeResult = [];
    }

  /**
   * Get scopes for an API Key
   *
   * @param     string    $apikey
   * @return    bool
   */
    public function getScopes($apikey)
    {
        // Do not do the request if values are already set
        if (count($this->_lastScopeResult)) {
            return true;
        }

        $url = 'v3/scopes';
        $response = $this->doSendgridRequest($apikey, $url, 'GET');

        // Process response
        $response = json_decode($response, true);

        if (isset($response['error']) or isset($response['errors'])) {
            return false;
        }

        if (! isset($response['scopes'])) {
            return false;
        }

        $this->_lastScopeResult = $response['scopes'];

        return true;
    }

  /**
   * Checks if the specified API Key has the specified scopes
   *
   * @param     string    $apikey
   * @param     array     $scopes
   * @return    string
   */
    public function checkAPIKeyScopes($apikey, $scopes)
    {

        if (! $this->getScopes($apikey)) {
            return self::SG_REASON_INVALID_KEY;
        }

        foreach ($scopes as $scope) {
            if (! in_array($scope, $this->_lastScopeResult)) {
                return self::SG_REASON_NO_SCOPES;
            }
        }

        return self::SG_VALID;
    }

  /**
   * Checks if the specified API Key has the required scopes
   *
   * @param     string    $apikey
   * @return    string
   */
    public function checkAPIKey($apikey)
    {
        return $this->checkAPIKeyScopes($apikey, $this->_requiredAPIKeyScopes);
    }

  /**
   * Checks if the specified API Key has the unsubscribe group scope
   *
   * @param     string    $apikey
   * @return    string
   */
    public function checkUnsubscribeScope($apikey)
    {
        return $this->checkAPIKeyScopes($apikey, $this->_requiredUnsubscribeScope);
    }

  /**
   * Checks if the specified template exists
   *
   * @param     string    $apikey
   * @param     string    $template_id
   * @return    bool
   */
    public function checkTemplate($apikey, $template_id)
    {
        if (empty(trim($template_id)) or empty(trim($apikey))) {
            return false;
        }

        $url = 'v3/templates/' . $template_id;
        $response = $this->doSendgridRequest($apikey, $url, 'GET');

        // Process response
        $response = json_decode($response, true);

        if (isset($response['error']) or isset($response['errors'])) {
            return false;
        }

        return true;
    }

  /**
   * Performs a SendGrid HTTP request
   *
   * @param     string   $apikey
   * @param     string   $url_suffix
   * @param     string   $method
   * @return    string   HTTP response body
   */
    private function doSendgridRequest($apikey, $url_suffix, $method)
    {
        // Template ID check URL
        $url = self::SG_API_URL . $url_suffix;

        // Request headers
        $headers = [ 'Authorization' => 'Bearer ' . $apikey ];

        // Send request
        $client = new \Zend_Http_Client($url, [ 'strict' => false ]);
        $response = $client->setHeaders($headers)->request($method)->getBody();

        return $response;
    }

  /**
   * Checks if the specified email is valid
   *
   * @param     string    $email
   * @return    bool
   */
    public function isEmailValid($email)
    {
        $validator = new \Zend_Validate_EmailAddress();
        if (! $validator->isValid($email)) {
            return false;
        }

        return true;
    }

  /**
   * Get ASM groups
   *
   * @param     string   $apikey
   * @return    mixed    false on error / array with asm groups
   */
    public function getAsmGroups($apikey)
    {
        if (self::SG_VALID != $this->checkUnsubscribeScope($apikey)) {
            return false;
        }

        $url = 'v3/asm/groups';
        $response = $this->doSendgridRequest($apikey, $url, 'GET');

        $response = json_decode($response, true);
        if (empty($response) or isset($response['error']) or ( isset($response['errors']) and isset($response['errors'][0]['message']) )) {
            return false;
        }

        return $response;
    }

  /**
   * Get stats by category
   *
   * @param     string   $apikey
   * @param     string   $category
   * @param     string   $start_date
   * @param     string   $end_date
   * @return    mixed    false on error / array with stats
   */
    public function getStats($apikey, $category, $start_date, $end_date)
    {

        $url = 'v3/categories/stats?start_date=' . $start_date . '&end_date=' . $end_date . '&categories=' . $category;

        $response = $this->doSendgridRequest($apikey, $url, 'GET');

        $response = json_decode($response, true);
        if (isset($response['error']) or ( isset($response['errors']) and isset($response['errors'][0]['message']) )) {
            return [];
        }

        return $response;
    }
}
