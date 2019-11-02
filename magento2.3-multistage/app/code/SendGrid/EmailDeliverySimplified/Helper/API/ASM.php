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

class ASM implements \jsonSerializable
{
    private $group_id;
    private $groups_to_display;

    public function setGroupId($group_id)
    {
        $this->group_id = $group_id;
    }

    public function getGroupId()
    {
        return $this->group_id;
    }

    public function setGroupsToDisplay($group_ids)
    {
        $this->groups_to_display = $group_ids;
    }

    public function getGroupsToDisplay()
    {
        return $this->groups_to_display;
    }

    public function jsonSerialize()
    {
        return array_filter(
            [
                'group_id' => $this->getGroupId(),
                'groups_to_display' => $this->getGroupsToDisplay()
            ]
        );
    }
}
