<?php

namespace SendGrid\EmailDeliverySimplified\Controller\Adminhtml\Settings;

use \Psr\Log\LoggerInterface;
use \Magento\Backend\App\Action\Context;

use SendGrid\EmailDeliverySimplified\Model\Message;
use SendGrid\EmailDeliverySimplified\Model\Transport;
use SendGrid\EmailDeliverySimplified\Block\Adminhtml\SettingsGeneralBlock;

class Index extends \Magento\Backend\App\Action
{
  /**
   * @var \Psr\Log\LoggerInterface
   */
    protected $_logger;

  /**
   * @var SendGrid\EmailDeliverySimplified\Block\Adminhtml\SettingsGeneralBlock
   */
    protected $_settingsBlock;

  /**
   * @var SendGrid\EmailDeliverySimplified\Model\Transport
   */
    protected $_mailTransport;

  /**
   * Constructor for the Index action of the settings page
   *
   * @param    Context             $context
   * @param    GeneralSettings     $generalSettingsModel
   * @param    array               $data
   */
    function __construct(
        LoggerInterface $loggerInterface,
        Context $context,
        SettingsGeneralBlock $settingsBlock,
        Transport $mailTransport
    ) {
        $this->_logger          = $loggerInterface;
        $this->_settingsBlock   = $settingsBlock;
        $this->_mailTransport   = $mailTransport;

        parent::__construct($context);
    }

  /**
   * Updates the settings posted in the request
   *
   * @return void
   */
    private function _updateSettings()
    {
        $request = $this->getRequest();

        $this->_logger->debug('[SendGrid] Updating Settings.');

        $apikey           = $request->getParam('apikey');
        $send_method      = $request->getParam('send_method');
        $content_type     = $request->getParam('content_type');
        $smtp_port        = $request->getParam('smtp_port');
        $from             = $request->getParam('from');
        $from_name        = $request->getParam('from_name');
        $reply_to         = $request->getParam('reply_to');
        $categories       = $request->getParam('categories');
        $template         = $request->getParam('template');
        $asm_group_id     = $request->getParam('asm_group');
        $stats_categories = $request->getParam('stats_categories');

        $errors = [];

        if (false == $this->_settingsBlock->setAPIKey($apikey)) {
            $errors[] = 'API Key is invalid or could not be saved.';
        }

        if (false == $this->_settingsBlock->setSendMethod($send_method)) {
            $errors[] = 'The Send Method is invalid or could not be saved.';
        }

        if (false == $this->_settingsBlock->setSMTPPort($smtp_port)) {
            $errors[] = 'The SMTP Port is invalid or could not be saved.';
        }

        if (false == $this->_settingsBlock->setContentType($content_type)) {
            $errors[] = 'The Content-Type is invalid or could not be saved.';
        }

        if (false == $this->_settingsBlock->setFrom($from)) {
            $errors[] = 'The From Address is invalid or could not be saved.';
        }

        if (false == $this->_settingsBlock->setFromName($from_name)) {
            $errors[] = 'The From Name could not be saved.';
        }

        if (false == $this->_settingsBlock->setReplyTo($reply_to)) {
            $errors[] = 'The Reply To Address is invalid or could not be saved.';
        }

        if (false == $this->_settingsBlock->setCategories($categories)) {
            $errors[] = 'The Categories are invalid or could not be saved.';
        }

        if (false == $this->_settingsBlock->setTemplateID($template)) {
            $errors[] = 'The Template is invalid or could not be saved.';
        }

        if (false == $this->_settingsBlock->setAsmGroupId($asm_group_id)) {
            $errors[] = 'The Asm Group Id could not be saved.';
        }

        if (false == $this->_settingsBlock->setStatsCategories($stats_categories)) {
            $errors[] = 'The Stats Categories could not be saved.';
        }

        // Get internal errors from the block controller (if any)
        $existing_errors = $this->_settingsBlock->getLatestErrors();

        // Prepare errors for display
        $errors = array_merge($existing_errors, $errors);
        if (count($errors)) {
            foreach ($errors as $error_message) {
                $this->messageManager->addError($error_message);
            }
        } else {
            $this->messageManager->addSuccess('All settings were updated successfully.');
        }

        // Reset internal errors
        $this->_settingsBlock->setLatestErrors();
    }

  /**
   * Sends a test email based on the parameters from the request
   *
   * @return void
   */
    private function _sendTestEmail()
    {
        $request = $this->getRequest();

        $this->_logger->debug('[SendGrid] Sending a test email.');

        $to         = $request->getParam('send_to');
        $subject    = $request->getParam('send_subject');
        $body_text  = $request->getParam('send_body_text');
        $body_html  = $request->getParam('send_body_html');
        $from       = $this->_settingsBlock->getFrom();

        $errors = [];

        if (empty(trim($to))) {
            $errors[] = 'To address is required';
        }

        if (empty(trim($subject))) {
            $errors[] = 'Email subject is required';
        }

        if (empty(trim($body_text)) and empty(trim($body_html))) {
            $errors[] = 'Email body is required';
        }

        if (empty(trim($from))) {
            $from = 'sendtest@sendgrid.magento';
        }

        if (! count($errors)) {
            try {
                $mail = new Message();

                $mail->setFrom($from);
                $mail->addTo($to);
                $mail->setSubject($subject);

                if (! empty(trim($body_text))) {
                    $mail->setBodyText($body_text);
                }

                if (! empty(trim($body_html))) {
                    $mail->setBodyHtml($body_html);
                }

                $this->_mailTransport->setMessage($mail);
                $this->_mailTransport->sendMessage();
            } catch (\Exception $e) {
                $this->_logger->debug('[SendGrid] Error occured in send mail test : ' . $e->getMessage());
                $errors[] = 'The email could not be sent. Please check your settings and try again.';
            }
        }

        // Get internal errors from the block controller (if any)
        $existing_errors = $this->_settingsBlock->getLatestErrors();

        // Prepare errors for display
        $errors = array_merge($existing_errors, $errors);
        if (count($errors)) {
            foreach ($errors as $error_message) {
                $this->messageManager->addError($error_message);
            }
        } else {
            $this->messageManager->addSuccess('Email was sent successfully');
        }

        // Reset internal errors
        $this->_settingsBlock->setLatestErrors();
    }

  /**
   * Index action
   *
   * @return void
   */
    public function execute()
    {
        $this->_logger->debug('[SendGrid] Displaying settings page layout');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $general_settings_scope = $request->getParam('general_settings');
            $send_test_scope        = $request->getParam('send_test');

            if ('true' == $general_settings_scope) {
                $this->_updateSettings();
            }

            if ('true' == $send_test_scope) {
                $this->_sendTestEmail();
            }
        }

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
