<?php

namespace SendGrid\EmailDeliverySimplified\Model;

use \Psr\Log\LoggerInterface;
use \Magento\Framework\Model\AbstractModel;
use \Magento\Framework\ObjectManagerInterface;
use SendGrid\EmailDeliverySimplified\Api\Data\SettingsInterface;

class GeneralSettings extends AbstractModel implements SettingsInterface
{
  /**
   * @var \Psr\Log\LoggerInterface
   */
    protected $_logger;

  /**
   * @var \Magento\Framework\ObjectManagerInterface
   */
    protected $_objectManager;

  /**
   * @var array
   */
    protected $_settings;

  /**
   * Constructor for the General Settings model
   *
   * @param    ObjectManagerInterface   $objectManager
   * @param    LoggerInterface          $loggerInterface
   */
    public function __construct(
        ObjectManagerInterface $objectManager,
        LoggerInterface $loggerInterface
    ) {
        $this->_logger        = $loggerInterface;
        $this->_objectManager = $objectManager;
        $this->_settings      = [];

        $this->_loadData();
    }

  /**
   * Retrieves all settings from the database and loads them into the internal map
   *
   * @return void
   */
    private function _loadData()
    {
        $this->_logger->debug('[SendGrid] Loading all settings.');

        $settings = $this->_objectManager->get('SendGrid\EmailDeliverySimplified\Model\Setting')->getCollection();
        foreach ($settings as $setting) {
            $this->_settings[ $setting->getKey() ] = $setting;
        }
    }

  /**
   * Retrieves the API Key from the internal map
   *
   * @return string
   */
    public function getAPIKey()
    {
        if (! isset($this->_settings[ 'apikey' ])) {
            $this->_logger->debug('[SendGrid] No API Key is set.');

            return '';
        }

        $this->_logger->debug('[SendGrid] API Key requested.');

        return $this->_settings[ 'apikey' ]->getValue();
    }

  /**
   * Saves the API Key in the internal map and in the database
   *
   * @param   string   $apikey
   * @return  bool
   */
    public function setAPIKey($apikey)
    {
        if (! isset($this->_settings[ 'apikey' ])) {
            $this->_logger->debug('[SendGrid] No API Key is set. Creating API key record.');

            $setting = $this->_objectManager->create('SendGrid\EmailDeliverySimplified\Model\Setting');
            $setting->setKey('apikey');
            $setting->setValue($apikey);
            $setting->save();

            $this->_settings[ 'apikey' ] = $setting;

            return true;
        }

        $this->_logger->debug('[SendGrid] Updating API key.');

        $this->_settings[ 'apikey' ]->setValue($apikey);
        $this->_settings[ 'apikey' ]->save();

        return true;
    }

  /**
   * Retrieves the send method from the internal map
   *
   * @return string
   */
    public function getSendMethod()
    {
        if (! isset($this->_settings[ 'send_method' ])) {
            $this->_logger->debug('[SendGrid] No send method is set.');

            return '';
        }

        $this->_logger->debug('[SendGrid] Send method requested.');

        return $this->_settings[ 'send_method' ]->getValue();
    }

  /**
   * Saves the send method in the internal map and in the database
   *
   * @param   string   $send_method
   * @return  bool
   */
    public function setSendMethod($send_method)
    {
        if (! isset($this->_settings[ 'send_method' ])) {
            $this->_logger->debug('[SendGrid] No send method is set. Creating send method record.');

            $setting = $this->_objectManager->create('SendGrid\EmailDeliverySimplified\Model\Setting');
            $setting->setKey('send_method');
            $setting->setValue($send_method);
            $setting->save();

            $this->_settings[ 'send_method' ] = $setting;

            return true;
        }

        $this->_logger->debug('[SendGrid] Updating send method.');

        $this->_settings[ 'send_method' ]->setValue($send_method);
        $this->_settings[ 'send_method' ]->save();

        return true;
    }

  /**
   * Retrieves the SMTP Port from the internal map
   *
   * @return string
   */
    public function getSMTPPort()
    {
        if (! isset($this->_settings[ 'smtp_port' ])) {
            $this->_logger->debug('[SendGrid] No SMTP Port is set.');

            return '';
        }

        $this->_logger->debug('[SendGrid] SMTP Port requested.');

        return $this->_settings[ 'smtp_port' ]->getValue();
    }

  /**
   * Saves the SMTP Port in the internal map and in the database
   *
   * @param   string   $smtp_port
   * @return  bool
   */
    public function setSMTPPort($smtp_port)
    {
        if (! isset($this->_settings[ 'smtp_port' ])) {
            $this->_logger->debug('[SendGrid] No SMTP Port is set. Creating SMTP Port record.');

            $setting = $this->_objectManager->create('SendGrid\EmailDeliverySimplified\Model\Setting');
            $setting->setKey('smtp_port');
            $setting->setValue($smtp_port);
            $setting->save();

            $this->_settings[ 'smtp_port' ] = $setting;

            return true;
        }

        $this->_logger->debug('[SendGrid] Updating SMTP Port.');

        $this->_settings[ 'smtp_port' ]->setValue($smtp_port);
        $this->_settings[ 'smtp_port' ]->save();

        return true;
    }

  /**
   * Retrieves the from address from the internal map
   *
   * @return string
   */
    public function getFrom()
    {
        if (! isset($this->_settings[ 'from' ])) {
            $this->_logger->debug('[SendGrid] No from address is set.');

            return '';
        }

        $this->_logger->debug('[SendGrid] From address requested.');

        return $this->_settings[ 'from' ]->getValue();
    }

  /**
   * Saves the from address in the internal map and in the database
   *
   * @param   string   $from
   * @return  bool
   */
    public function setFrom($from)
    {
        if (! isset($this->_settings[ 'from' ])) {
            $this->_logger->debug('[SendGrid] No from address is set. Creating from address record.');

            $setting = $this->_objectManager->create('SendGrid\EmailDeliverySimplified\Model\Setting');
            $setting->setKey('from');
            $setting->setValue($from);
            $setting->save();

            $this->_settings[ 'from' ] = $setting;

            return true;
        }

        $this->_logger->debug('[SendGrid] Updating from address.');

        $this->_settings[ 'from' ]->setValue($from);
        $this->_settings[ 'from' ]->save();

        return true;
    }

  /**
   * Retrieves the from name from the internal map
   *
   * @return string
   */
    public function getFromName()
    {
        if (! isset($this->_settings[ 'from_name' ])) {
            $this->_logger->debug('[SendGrid] No from name is set.');

            return '';
        }

        $this->_logger->debug('[SendGrid] From name requested.');

        return $this->_settings[ 'from_name' ]->getValue();
    }

  /**
   * Saves the from name in the internal map and in the database
   *
   * @param   string   $from_name
   * @return  bool
   */
    public function setFromName($from_name)
    {
        if (! isset($this->_settings[ 'from_name' ])) {
            $this->_logger->debug('[SendGrid] No from name is set. Creating from name record.');

            $setting = $this->_objectManager->create('SendGrid\EmailDeliverySimplified\Model\Setting');
            $setting->setKey('from_name');
            $setting->setValue($from_name);
            $setting->save();

            $this->_settings[ 'from_name' ] = $setting;

            return true;
        }

        $this->_logger->debug('[SendGrid] Updating from name.');

        $this->_settings[ 'from_name' ]->setValue($from_name);
        $this->_settings[ 'from_name' ]->save();

        return true;
    }

  /**
   * Retrieves the reply to address from the internal map
   *
   * @return string
   */
    public function getReplyTo()
    {
        if (! isset($this->_settings[ 'reply_to' ])) {
            $this->_logger->debug('[SendGrid] No reply to address is set.');

            return '';
        }

        $this->_logger->debug('[SendGrid] Reply to address requested.');

        return $this->_settings[ 'reply_to' ]->getValue();
    }

  /**
   * Saves the reply to address in the internal map and in the database
   *
   * @param   string   $reply_to
   * @return  bool
   */
    public function setReplyTo($reply_to)
    {
        if (! isset($this->_settings[ 'reply_to' ])) {
            $this->_logger->debug('[SendGrid] No reply to address is set. Creating reply to address record.');

            $setting = $this->_objectManager->create('SendGrid\EmailDeliverySimplified\Model\Setting');
            $setting->setKey('reply_to');
            $setting->setValue($reply_to);
            $setting->save();

            $this->_settings[ 'reply_to' ] = $setting;

            return true;
        }

        $this->_logger->debug('[SendGrid] Updating reply to address.');

        $this->_settings[ 'reply_to' ]->setValue($reply_to);
        $this->_settings[ 'reply_to' ]->save();

        return true;
    }

  /**
   * Retrieves the categories from the internal map
   *
   * @return string
   */
    public function getCategories()
    {
        if (! isset($this->_settings[ 'categories' ])) {
            $this->_logger->debug('[SendGrid] No categories are set.');

            return '';
        }

        $this->_logger->debug('[SendGrid] Categories requested.');

        return $this->_settings[ 'categories' ]->getValue();
    }

  /**
   * Saves the categories in the internal map and in the database
   *
   * @param   string   $categories
   * @return  bool
   */
    public function setCategories($categories)
    {
        if (! isset($this->_settings[ 'categories' ])) {
            $this->_logger->debug('[SendGrid] No categories are set. Creating categories record.');

            $setting = $this->_objectManager->create('SendGrid\EmailDeliverySimplified\Model\Setting');
            $setting->setKey('categories');
            $setting->setValue($categories);
            $setting->save();

            $this->_settings[ 'categories' ] = $setting;

            return true;
        }

        $this->_logger->debug('[SendGrid] Updating categories.');

        $this->_settings[ 'categories' ]->setValue($categories);
        $this->_settings[ 'categories' ]->save();

        return true;
    }

  /**
   * Retrieves the template ID from the internal map
   *
   * @return string
   */
    public function getTemplateID()
    {
        if (! isset($this->_settings[ 'template' ])) {
            $this->_logger->debug('[SendGrid] No template is set.');

            return '';
        }

        $this->_logger->debug('[SendGrid] Template requested.');

        return $this->_settings[ 'template' ]->getValue();
    }

  /**
   * Saves the template ID in the internal map and in the database
   *
   * @param   string   $template
   * @return  bool
   */
    public function setTemplateID($template)
    {
        if (! isset($this->_settings[ 'template' ])) {
            $this->_logger->debug('[SendGrid] No template is set. Creating template record.');

            $setting = $this->_objectManager->create('SendGrid\EmailDeliverySimplified\Model\Setting');
            $setting->setKey('template');
            $setting->setValue($template);
            $setting->save();

            $this->_settings[ 'template' ] = $setting;

            return true;
        }

        $this->_logger->debug('[SendGrid] Updating template.');

        $this->_settings[ 'template' ]->setValue($template);
        $this->_settings[ 'template' ]->save();

        return true;
    }

  /**
   * Retrieves the ASM Group Id from the internal map
   *
   * @return string
   */
    public function getAsmGroupId()
    {
        if (! isset($this->_settings[ 'asm_group_id' ])) {
            $this->_logger->debug('[SendGrid] No Asm Group Id is set.');

            return '';
        }

        $this->_logger->debug('[SendGrid] Asm Group Id requested.');

        return $this->_settings[ 'asm_group_id' ]->getValue();
    }

  /**
   * Saves the Asm Group Id in the internal map and in the database
   *
   * @param   string   $asm_group_id
   * @return  bool
   */
    public function setAsmGroupId($asm_group_id)
    {
        if (! isset($this->_settings[ 'asm_group_id' ])) {
            $this->_logger->debug('[SendGrid] No Asm Group Id is set. Creating Asm Group Id record.');

            $setting = $this->_objectManager->create('SendGrid\EmailDeliverySimplified\Model\Setting');
            $setting->setKey('asm_group_id');
            $setting->setValue($asm_group_id);
            $setting->save();

            $this->_settings[ 'asm_group_id' ] = $setting;

            return true;
        }

        $this->_logger->debug('[SendGrid] Updating Asm Group Id.');

        $this->_settings[ 'asm_group_id' ]->setValue($asm_group_id);
        $this->_settings[ 'asm_group_id' ]->save();

        return true;
    }

  /**
   * Retrieves the stats categories from the internal map
   *
   * @return string
   */
    public function getStatsCategories()
    {
        if (! isset($this->_settings[ 'stats_categories' ])) {
            $this->_logger->debug('[SendGrid] No stats categories are set.');

            return '';
        }

        $this->_logger->debug('[SendGrid] stats categories requested.');

        return $this->_settings[ 'stats_categories' ]->getValue();
    }

  /**
   * Saves the stats categories in the internal map and in the database
   *
   * @param   string   $stats_categories
   * @return  bool
   */
    public function setStatsCategories($stats_categories)
    {
        if (! isset($this->_settings[ 'stats_categories' ])) {
            $this->_logger->debug('[SendGrid] No stats categories are set. Creating stats categories record.');

            $setting = $this->_objectManager->create('SendGrid\EmailDeliverySimplified\Model\Setting');
            $setting->setKey('stats_categories');
            $setting->setValue($stats_categories);
            $setting->save();

            $this->_settings[ 'stats_categories' ] = $setting;

            return true;
        }

        $this->_logger->debug('[SendGrid] Updating stats categories.');

        $this->_settings[ 'stats_categories' ]->setValue($stats_categories);
        $this->_settings[ 'stats_categories' ]->save();

        return true;
    }
}
