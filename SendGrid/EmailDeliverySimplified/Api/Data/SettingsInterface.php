<?php

namespace SendGrid\EmailDeliverySimplified\Api\Data;

interface SettingsInterface
{
    public function getAPIKey();
    public function setAPIKey($apikey);
    public function getSendMethod();
    public function setSendMethod($send_method);
    public function getSMTPPort();
    public function setSMTPPort($smtp_port);
    public function getFrom();
    public function setFrom($from);
    public function getFromName();
    public function setFromName($from_name);
    public function getReplyTo();
    public function setReplyTo($reply_to);
    public function getCategories();
    public function setCategories($categories);
    public function getTemplateID();
    public function setTemplateID($template);
}
