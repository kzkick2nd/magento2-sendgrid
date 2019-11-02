<?php

namespace SendGrid\EmailDeliverySimplified\Model\ResourceModel\Setting;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
  /**
   * @var \Psr\Log\LoggerInterface
   */
    protected $_logger;

  /**
   * Constructor for the Setting Collection model
   *
   * @param    EntityFactoryInterface     $entityFactory
   * @param    LoggerInterface            $LoggerInterface
   * @param    FetchStrategyInterface     $fetchStrategy
   * @param    ManagerInterface           $eventManager
   * @param    AdapterInterface           $connection
   * @param    AbstractDb                 $resource
   */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $loggerInterface,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->_logger = $loggerInterface;

        parent::__construct($entityFactory, $loggerInterface, $fetchStrategy, $eventManager, $connection, $resource);
    }

  /**
   * Special constructor for the Setting collection model.
   * Initializes the collection with the Setting model and it's resource model.
   *
   * @return void
   */
    public function _construct()
    {
        $this->_init('SendGrid\EmailDeliverySimplified\Model\Setting', 'SendGrid\EmailDeliverySimplified\Model\ResourceModel\Setting');
    }
}
