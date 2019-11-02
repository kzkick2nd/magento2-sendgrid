<?php

namespace SendGrid\EmailDeliverySimplified\Block\Adminhtml;

use \Magento\Backend\Block\Template;
use \Magento\Backend\Model\Auth\Session;
use \Magento\Integration\Model\Oauth\TokenFactory;
use SendGrid\EmailDeliverySimplified\Model\GeneralSettings;
use SendGrid\EmailDeliverySimplified\Helper\Logo;
use SendGrid\EmailDeliverySimplified\Helper\Tools;

class StatisticsBlock extends Template
{
    /**
     * @var array
     */
    private $_latestErrors;

    /**
     * @var SendGrid\EmailDeliverySimplified\Model\GeneralSettings
     */
    protected $_generalSettingsModel;

    /**
     * @var SendGrid\EmailDeliverySimplified\Helper\Logo
     */
    protected $_logoHelper;

    /**
     * @var SendGrid\EmailDeliverySimplified\Helper\Tools
     */
    protected $_toolsHelper;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_adminSession;

    /**
     * @var \Magento\Integration\Model\Oauth\TokenFactory
     */
    protected $_tokenFactory;

    /**
     * Constructor for the Statistics Block
     *
     * @param    Template\Context       $context
     * @param    StoreManagerInterface  $storeManager
     * @param    Session                $adminSession
     * @param    TokenFactory           $tokenFactory
     * @param    GeneralSettings        $generalSettingsModel
     * @param    Logo                   $logoHelper
     * @param    Tools                  $toolsHelper
     * @param    array                  $data
     */
    public function __construct(
        Template\Context $context,
        Session $adminSession,
        TokenFactory $tokenFactory,
        GeneralSettings $generalSettingsModel,
        Logo $logoHelper,
        Tools $toolsHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->_generalSettingsModel  = $generalSettingsModel;
        $this->_adminSession          = $adminSession;
        $this->_tokenFactory          = $tokenFactory;
        $this->_latestErrors          = [];
        $this->_logoHelper            = $logoHelper;
        $this->_toolsHelper           = $toolsHelper;
    }

    /**
     * Returns the SendGrid Logo as a data encoded URI
     *
     * @return  string
     */
    public function getSendGridLogo()
    {
        return $this->_logoHelper->getLogoAsDataURI();
    }

    /**
     * Returns the admin token
     *
     * @return  string
     */
    public function getStatsToken()
    {
        $user = $this->_adminSession->getUser();
        if (null == $user) {
            return "";
        }

        $token = $this->_tokenFactory->create()->createAdminToken($user->getId())->getToken();
        
        return $token;
    }

    /**
     * Returns the statistics base url
     *
     * @return  string
     */
    public function getStatsBaseUrl()
    {
        $base_url = $this->_storeManager->getStore()->getBaseUrl();

        return $base_url . 'index.php/rest/v1/sgstats/get/';
    }

    /**
     * Returns the invalidate token base url
     *
     * @return  string
     */
    public function getInvalidateTokenBaseUrl()
    {
        $base_url = $this->_storeManager->getStore()->getBaseUrl();

        return $base_url . 'index.php/rest/v1/sgstats/token/invalidate/';
    }

    /**
     * Retrieves the stats categories from the model
     *
     * @return  string
     */
    public function getStatsCategories()
    {
        $categories = $this->_generalSettingsModel->getStatsCategories();
        if ($categories == "") {
            return [];
        }

        $categories = explode(",", $categories);

        return $categories;
    }
}
