<?php

namespace SendGrid\EmailDeliverySimplified\Api;

use \Psr\Log\LoggerInterface;
use \Magento\Integration\Model\Oauth\TokenFactory;
use SendGrid\EmailDeliverySimplified\Model\GeneralSettings;
use SendGrid\EmailDeliverySimplified\Helper\Tools;

/**
 * Defines the implementaiton class of the stats.
 */
class Statistics
{
  /**
   * @var SendGrid\EmailDeliverySimplified\Model\GeneralSettings
   */
    protected $_generalSettingsModel;

  /**
   * @var SendGrid\EmailDeliverySimplified\Helper\Tools
   */
    protected $_toolsHelper;

  /**
   * @var \Magento\Integration\Model\Oauth\TokenFactory
   */
    protected $_tokenFactory;

  /**
   * @var \Psr\Log\LoggerInterface
   */
    protected $_logger;

  /**
   * Constructor for the Statistics API Class
   *
   * @param    LoggerInterface      $loggerInterface
   * @param    GeneralSettings      $generalSettingsModel
   * @param    Tools                $toolsHelper
   * @param    TokenFactory         $tokenFactory
   */
    public function __construct(
        LoggerInterface $loggerInterface,
        GeneralSettings $generalSettingsModel,
        Tools $toolsHelper,
        TokenFactory $tokenFactory
    ) {
        $this->_generalSettingsModel  = $generalSettingsModel;
        $this->_logger                = $loggerInterface;
        $this->_toolsHelper           = $toolsHelper;
        $this->_tokenFactory          = $tokenFactory;
    }

  /**
   * Return the statistics data
   *
   * @api
   * @param   string $category    category name
   * @param   string $start_date  start date
   * @param   string $end_date    end date
   * @return  string              stats data points
   */
    public function getStats($category, $start_date, $end_date)
    {
        $this->_logger->debug('[SendGrid] Get Statistics');

        $api_key = $this->_generalSettingsModel->getAPIKey();

        $results = $this->_toolsHelper->getStats($api_key, $category, $start_date, $end_date);

        $data = [
        'dates' => [],
        'metrics' => []
        ];
    
        foreach ($results as $key => $value) {
            $data['dates'][] = $value['date'];

            foreach ($value['stats'][0]['metrics'] as $label => $unit_value) {
                if (! isset($data['metrics'][$label])) {
                    $data['metrics'][$label] = [
                    'label' => ucwords(str_replace("_", " ", $label)),
                    'values' => []
                    ];
                }

                $data['metrics'][$label]['values'][] = $unit_value;
            }
        }

        return json_encode($data);
    }

  /**
   * Invalidates statistics token
   *
   * @api
   * @param   string $token       token
   * @return  string
   */
    public function invalidateToken($token)
    {
        $this->_logger->debug('[SendGrid] Invalidate Statistics Token');

        $tokenEntry = $this->_tokenFactory->create()->loadByToken($token);
        if ($tokenEntry->getToken() == $token) {
            $tokenEntry->setRevoked(1);
            $tokenEntry->save();
        }

        return "";
    }
}
