<?php
/**
 * This helper builds the request body for a /mail/send API call.
 *
 * PHP version 5.3
 *
 * @author    Elmer Thomas <dx@sendgrid.com>
 * @copyright 2016 SendGrid
 * @license   https://opensource.org/licenses/MIT The MIT License
 * @version   GIT: <git_id>
 * @link      http://packagist.org/packages/sendgrid/sendgrid
 */
namespace SendGrid\EmailDeliverySimplified\Helper\API;

class ReplyTo implements \jsonSerializable
{
    private $email;

    public function __construct($email)
    {
        $this->email = $email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function jsonSerialize()
    {
        return array_filter(
            [
                'email' => $this->getEmail()
            ]
        );
    }
}
