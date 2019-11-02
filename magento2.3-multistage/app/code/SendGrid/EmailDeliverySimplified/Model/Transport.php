<?php

namespace SendGrid\EmailDeliverySimplified\Model;

use \Psr\Log\LoggerInterface;
use \Magento\Framework\Phrase;
use \Magento\Framework\Mail\MessageInterface;
use \Magento\Framework\Exception\MailException;
use \Magento\Framework\Mail\TransportInterface;
use \Magento\Framework\Module\Manager;
use SendGrid\EmailDeliverySimplified\Helper\API;
use SendGrid\EmailDeliverySimplified\Helper\Tools;
use SendGrid\EmailDeliverySimplified\Model\GeneralSettings;

class Transport implements TransportInterface
{
  /**
   * @var \Magento\Framework\Mail\MessageInterface
   */
    protected $message;

  /**
   * @var \Psr\Log\LoggerInterface
   */
    protected $logger;

  /**
   * @var SendGrid\EmailDeliverySimplified\Model\GeneralSettings
   */
    protected $generalSettings;

  /**
   * @var \Magento\Framework\Module\Manager
   */
    protected $moduleManager;

    protected $zendTransport;

  /**
   * @const   string  SendGrid SMTP hostname
   */
    const SMTP_HOSTNAME = 'smtp.sendgrid.net';

  /**
   * @param   MessageInterface  $message
   * @param   GeneralSettings   $generalSettings
   * @param   LoggerInterface   $loggerInterface
   * @throws  \InvalidArgumentException
   */
    public function __construct(
        MessageInterface $message,
        GeneralSettings $generalSettings,
        LoggerInterface $loggerInterface,
        Manager         $moduleManager
    ) {
        $this->logger          = $loggerInterface;
        $this->message         = $message;
        $this->generalSettings = $generalSettings;
        $this->moduleManager   = $moduleManager;

        $smtp_port = $this->generalSettings->getSMTPPort();
        if (empty($smtp_port)) {
            $smtp_port = 587;
        }

        $apikey = $this->generalSettings->getAPIKey();
        if (empty($apikey) || ! $this->moduleManager->isOutputEnabled('SendGrid_EmailDeliverySimplified')) {
            return;
        }

        $options = new SmtpOptions([
            'name'              => 'localhost',
            'host'              => self::SMTP_HOSTNAME,
            'port'              => $smtp_port,
            'connection_class'  => 'login',
            'connection_config' => [
                'ssl'      => 'tls',
                'username' => 'apikey',
                'password' => $apikey
            ]
        ]);

        $this->updateInternalMessage();
        $this->_sent = false;

        $this->zendTransport = new Smtp($options);
    }

  /**
   * Updates the message with the settings configured in the model
   *
   * @return void
   */
    private function updateInternalMessage()
    {
        $from         = trim($this->generalSettings->getFrom());
        $from_name    = trim($this->generalSettings->getFromName());
        $reply_to     = trim($this->generalSettings->getReplyTo());
        $categories   = explode(',', $this->generalSettings->getCategories());
        $template     = trim($this->generalSettings->getTemplateID());
        $asm_group_id = trim($this->generalSettings->getAsmGroupId());

        $xsmtpapi_header['category'] = [ 'magento2_sendgrid_plugin' ];
        foreach ($categories as $category) {
            $xsmtpapi_header['category'][] = trim($category);
        }

        if (! empty($template)) {
            $xsmtpapi_header['filters']['templates']['settings']['enable']      = 1;
            $xsmtpapi_header['filters']['templates']['settings']['template_id'] = $template;
        }

        // asm group id
        if ($asm_group_id != false and $asm_group_id != 0) {
            $xsmtpapi_header['asm_group_id'] = intval($asm_group_id);
        }

        $this->message->getHeaders()->addHeaderLine('x-smtpapi', json_encode($xsmtpapi_header));

        if (! empty($from)) {
            $this->message->setFrom($from);
        }

        if (! empty($from_name) and ! empty($from)) {
            $this->message->setFrom($from, $from_name);
        }

        if (! empty($from_name) and empty($from)) {
            $initial_from = $this->message->getFrom();
            $this->message->setFrom($initial_from, $from_name);
        }

        if (! empty($reply_to)) {
            $this->message->setReplyTo($reply_to);
        }
    }

  /**
   * Returns a string with the JSON request for the API from the current message
   *
   * @return string
   */
    private function getAPIMessage()
    {
        // Model values
        $from         = trim($this->generalSettings->getFrom());
        $from_name    = trim($this->generalSettings->getFromName());
        $reply_to     = trim($this->generalSettings->getReplyTo());
        $categories   = explode(',', $this->generalSettings->getCategories());
        $template     = trim($this->generalSettings->getTemplateID());
        $asm_group_id = trim($this->generalSettings->getAsmGroupId());

        // Default category
        $categories[] = 'magento2_sendgrid_plugin';

        // Message values
        $recipients = $this->message->getTo();
        $subject    = trim($this->message->getSubject());
        $text       = $this->message->getBodyText(false);
        // $html       = $this->message->getBodyHtml(false);

        if ($text instanceof \Zend_Mime_Part) {
            $text = $text->getRawContent();
        }

        // if ($html instanceof \Zend_Mime_Part) {
        //     $html = $html->getRawContent();
        // }

        // If no from field in model, get message from
        if (empty($from)) {
            $from = $this->message->getFrom();
        }

        // If no reply to field in model, get message reply to
        if (empty($reply_to)) {
            $reply_to = $this->message->getReplyTo();
        }

        // Initializations
        $mail = new API\Mail();
        $personalization = new API\Personalization();

        // Add To's
        foreach ($recipients as $to) {
            $email = new API\Email(null, trim($to->getEmail()));
            $personalization->addTo($email);
        }

        // Add from with or without name
        if (! empty($from_name)) {
            $email = new API\Email($from_name, $from);
            $mail->setFrom($email);
        } else {
            $email = new API\Email(null, $from);
            $mail->setFrom($email);
        }

        // Plain content
        if (! empty($text)) {
            $content = new API\Content('text/plain', $text);
            $mail->addContent($content);
        }

        // HTML content
        // if (! empty($html)) {
        //     $content = new API\Content('text/html', $html);
        //     $mail->addContent($content);
        // }

        // Reply to
        if (! empty($reply_to)) {
            $email = new API\Email(null, $reply_to);
            $mail->setReplyTo($email);
        }

        // Categories
        foreach ($categories as $category) {
            if (! empty(trim($category))) {
                $mail->addCategory(trim($category));
            }
        }

        // Template ID
        if (! empty($template)) {
            $mail->setTemplateId($template);
        }

        $mail->setSubject($subject);
        $mail->addPersonalization($personalization);

        // Attachments
        // $parts = $this->_message->getParts();
        // foreach ($parts as $part) {
        //     $attachment = new API\Attachment();
        //     $attachment->setContent(base64_encode($part->getRawContent()));
        //     $attachment->setType($part->type);
        //     $attachment->setFilename($part->filename);
        //     $attachment->setDisposition($part->disposition);

        //     $mail->addAttachment($attachment);
        // }

        // asm group id
        if ($asm_group_id != false and $asm_group_id != 0) {
            $asm = new API\ASM();
            $asm->setGroupId(intval($asm_group_id));

            $mail->setASM($asm);
        }

        return $mail->jsonSerialize();
    }

  /**
   * Sets the message
   *
   * @param   MessageInterface  $message
   * @return  void
   * @throws  \Magento\Framework\Exception\MailException
   */
    public function setMessage(MessageInterface $message)
    {

        $this->message = $message;
        $this->updateInternalMessage();
    }

  /**
   * Send a mail using this transport
   *
   * @return void
   * @throws \Magento\Framework\Exception\MailException
   */
    public function sendMessage()
    {
        try {
            $this->logger->debug('[SendGrid] Sending email.');

            $apikey = $this->generalSettings->getAPIKey();
            $send_method = $this->generalSettings->getSendMethod();

            if (! $this->moduleManager->isOutputEnabled('SendGrid_EmailDeliverySimplified')) {
                $this->logger->debug('[SendGrid] Module is not enabled. Email is sent via vendor Zend Mail.');

                $this->zendTransport->send(
                    ZendMessage::fromString($this->message->getRawMessage())
                );

                return;
            }

            if ('smtp' == $send_method or empty(trim($apikey))) {
                $this->zendTransport->send(
                    ZendMessage::fromString($this->message->getRawMessage())
                );
            } else {
                // Compose JSON payload of email send request
                $payload = $this->getAPIMessage();

                // Mail send URL
                $url = Tools::SG_API_URL . 'v3/mail/send';

                // Request headers
                $headers = [ 'Authorization' => 'Bearer ' . $apikey ];

                // Send request
                $client = new \Zend_Http_Client($url, [ 'strict' => true ]);

                $response = $client->setHeaders($headers)
                           ->setRawData(json_encode($payload), 'application/json')
                           ->request('POST');

                // Process response
                if (202 != $response->getStatus()) {
                    $response = $response->getBody();

                    throw new \Exception($response);
                }
            }
        } catch (\Exception $e) {
            $this->logger->debug('[SendGrid] Error sending email : ' . $e->getMessage());
            throw new MailException(new Phrase($e->getMessage()), $e);
        }
    }

  /**
   * Get message
   *
   * @return string
   */
    public function getMessage()
    {
        return $this->message;
    }
}
