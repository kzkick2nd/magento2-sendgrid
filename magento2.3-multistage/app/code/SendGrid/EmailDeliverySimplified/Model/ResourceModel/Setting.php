<?php

namespace SendGrid\EmailDeliverySimplified\Model\ResourceModel;

class Setting extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
  /**
   * @var \Psr\Log\LoggerInterface
   */
    protected $_logger;

  /**
   * Constructor for the Setting resource model
   *
   * @param    LoggerInterface  $loggerInterface
   * @param    Context          $context
   * @param    mixed            $connectionName
   */
    public function __construct(
        \Psr\Log\LoggerInterface $loggerInterface,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null
    ) {
        $this->_logger = $loggerInterface;

        parent::__construct($context, $connectionName);
    }

  /**
   * Special constructor for the Setting resource model.
   * Associates this resource with an entry in the "sendgrid_settings" table
   * from the database based on the "id" column.
   *
   * @return void
   */
    public function _construct()
    {
        $this->_init('sendgrid_settings', 'id');
    }
}
