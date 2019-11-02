<?php

namespace SendGrid\EmailDeliverySimplified\Block\Adminhtml;

use \Magento\Backend\Block\Template;
use SendGrid\EmailDeliverySimplified\Model\GeneralSettings;
use SendGrid\EmailDeliverySimplified\Helper\Logo;
use SendGrid\EmailDeliverySimplified\Helper\Tools;

class SettingsGeneralBlock extends Template
{
    /**
     * @var array
     */
    private $_allowedSendMethods;

    /**
     * @var array
     */
    private $_allowedSMTPPorts;

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
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @const   integer  SMTP TLS Port
     */
    const TLS = 587;

    /**
     * @const   integer  SMTP TLS Alternative Port
     */
    const TLS_ALTERNATIVE = 25;

    /**
     * @const   integer  SMTP SSL Port
     */
    const SSL = 465;

    /**
     * Constructor for the General Settings Block
     *
     * @param    Template\Context     $context
     * @param    GeneralSettings      $generalSettingsModel
     * @param    Logo                 $logoHelper
     * @param    Tools                $toolsHelper
     * @param    array                $data
     */
    public function __construct(
        Template\Context $context,
        GeneralSettings $generalSettingsModel,
        Logo $logoHelper,
        Tools $toolsHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->_generalSettingsModel  = $generalSettingsModel;
        $this->_allowedSendMethods    = [ 'api', 'smtp' ];
        $this->_allowedSMTPPorts      = [ self::TLS, self::TLS_ALTERNATIVE, self::SSL ];
        $this->_logger                = $context->getLogger();
        $this->_latestErrors          = [];
        $this->_logoHelper            = $logoHelper;
        $this->_toolsHelper           = $toolsHelper;
    }

    /**
     * Retrieves the API Key from the model
     *
     * @return  string
     */
    public function getAPIKey()
    {
        return $this->_generalSettingsModel->getAPIKey();
    }

    /**
     * Sets the API Key in the model
     *
     * @param     string    $apikey
     * @return    bool
     */
    public function setAPIKey($apikey)
    {
        if (empty(trim($apikey))) {
            $this->_latestErrors[] = 'API Key was set to an empty value';

            return $this->_generalSettingsModel->setAPIKey('');
        }

        $apikey_status = $this->_toolsHelper->checkAPIKey($apikey);
        if (Tools::SG_REASON_INVALID_KEY == $apikey_status) {
            return false;
        } elseif (Tools::SG_REASON_NO_SCOPES == $apikey_status) {
            $this->_latestErrors[] = 'API Key is valid but without permissions';
            return true;
        }

        return $this->_generalSettingsModel->setAPIKey($apikey);
    }

    /**
     * Checks wether the current API Key is set and valid
     *
     * @return  bool
     */
    public function isAPIKeyValid()
    {
        $apikey = $this->getAPIKey();

        if (empty(trim($apikey))) {
            return false;
        }

        $apikey_status = $this->_toolsHelper->checkAPIKey($apikey);
        if (Tools::SG_VALID != $apikey_status) {
            return false;
        }

        return true;
    }

    /**
     * Retrieves the send method from the model
     *
     * @return  string
     */
    public function getSendMethod()
    {
        return $this->_generalSettingsModel->getSendMethod();
    }

    /**
     * Sets the send method in the model (only if it is valid)
     *
     * @param     string    $send_method
     * @return    bool
     */
    public function setSendMethod($send_method)
    {
        if (! in_array($send_method, $this->_allowedSendMethods)) {
            return false;
        }

        return $this->_generalSettingsModel->setSendMethod($send_method);
    }

    /**
     * Retrieves the SMTP Port from the model
     *
     * @return  string
     */
    public function getSMTPPort()
    {
        return $this->_generalSettingsModel->getSMTPPort();
    }

    /**
     * Sets the SMTP Port in the model
     *
     * @param     string    $port
     * @return    bool
     */
    public function setSMTPPort($port)
    {
        if (! in_array((int)$port, $this->_allowedSMTPPorts)) {
            return false;
        }

        return $this->_generalSettingsModel->setSMTPPort($port);
    }

    /**
     * Retrieves the from email address from the model
     *
     * @return  string
     */
    public function getFrom()
    {
        return $this->_generalSettingsModel->getFrom();
    }

    /**
     * Sets the from email address in the model (only if it is valid)
     *
     * @param     string    $from
     * @return    bool
     */
    public function setFrom($from)
    {
        if (empty(trim($from))) {
            return $this->_generalSettingsModel->setFrom('');
        }

        if (! $this->_toolsHelper->isEmailValid($from)) {
            return false;
        }

        return $this->_generalSettingsModel->setFrom($from);
    }

    /**
     * Retrieves the from name from the model
     *
     * @return  string
     */
    public function getFromName()
    {
        return $this->_generalSettingsModel->getFromName();
    }

    /**
     * Sets the from name in the model
     *
     * @param     string    $from_name
     * @return    bool
     */
    public function setFromName($from_name)
    {
        return $this->_generalSettingsModel->setFromName($from_name);
    }

    /**
     * Retrieves the reply to email address from the model
     *
     * @return  string
     */
    public function getReplyTo()
    {
        return $this->_generalSettingsModel->getReplyTo();
    }

    /**
     * Sets the reply to email address in the model
     *
     * @param     string    $reply_to
     * @return    bool
     */
    public function setReplyTo($reply_to)
    {
        if (empty(trim($reply_to))) {
            return $this->_generalSettingsModel->setReplyTo('');
        }

        if (! $this->_toolsHelper->isEmailValid($reply_to)) {
            return false;
        }

        return $this->_generalSettingsModel->setReplyTo($reply_to);
    }

    /**
     * Retrieves the categories from the model
     *
     * @return  string
     */
    public function getCategories()
    {
        return $this->_generalSettingsModel->getCategories();
    }

    /**
     * Sets the categories in the model
     *
     * @param     string    $categories
     * @return    bool
     */
    public function setCategories($categories)
    {
        return $this->_generalSettingsModel->setCategories($categories);
    }

    /**
     * Retrieves the template ID from the model
     *
     * @return  string
     */
    public function getTemplateID()
    {
        return $this->_generalSettingsModel->getTemplateID();
    }

    /**
     * Sets the template ID in the model
     *
     * @param     string    $template
     * @return    bool
     */
    public function setTemplateID($template)
    {
        if (empty(trim($template))) {
            return $this->_generalSettingsModel->setTemplateID('');
        }

        $apikey = $this->getAPIKey();

        $template_status = $this->_toolsHelper->checkTemplate($apikey, $template);
        if (true != $template_status) {
            return false;
        }

        return $this->_generalSettingsModel->setTemplateID($template);
    }

    /**
     * Returns an array of the allowed send methods
     *
     * @return  array
     */
    public function getAllowedSendMethods()
    {
        return $this->_allowedSendMethods;
    }

    /**
     * Returns an array of the allowed SMTP Ports
     *
     * @return  array
     */
    public function getAllowedSMTPPorts()
    {
        return $this->_allowedSMTPPorts;
    }

    /**
     * Retrieves the asm group id from the model
     *
     * @return  string
     */
    public function getAsmGroupId()
    {
        return $this->_generalSettingsModel->getAsmGroupId();
    }

    /**
     * Sets the asm group id in the model
     *
     * @param     string    $asm_group_id
     * @return    bool
     */
    public function setAsmGroupId($asm_group_id)
    {
        return $this->_generalSettingsModel->setAsmGroupId($asm_group_id);
    }

    /**
     * Retrieves the stats categories from the model
     *
     * @return  string
     */
    public function getStatsCategories()
    {
        return $this->_generalSettingsModel->getStatsCategories();
    }

    /**
     * Sets the stats categories in the model
     *
     * @param     string    $stats_categories
     * @return    bool
     */
    public function setStatsCategories($stats_categories)
    {
        return $this->_generalSettingsModel->setStatsCategories($stats_categories);
    }

    /**
     * Sets the list of latest errors
     *
     * @return  void
     */
    public function setLatestErrors($errors = [])
    {
        $this->_latestErrors = $errors;

        $this->_logger->debug('[SendGrid] Latest errors : ' . var_export($errors, true));
    }

    /**
     * Returns an array of the latest errors
     *
     * @return  array
     */
    public function getLatestErrors()
    {
        return $this->_latestErrors;
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
     * Get asm groups
     *
     * @return  array
     */
    public function getAsmGroups()
    {
        $apikey = $this->getAPIKey();

        $asm_groups = $this->_toolsHelper->getAsmGroups($apikey);

        if (false === $asm_groups) {
            return [];
        }
        return $asm_groups;
    }
}
