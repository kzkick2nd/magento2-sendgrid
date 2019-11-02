<?php

namespace SendGrid\EmailDeliverySimplified\Model;

use \Magento\Framework\Model\Context;
use \Magento\Framework\Registry;
use \Magento\Framework\Model\AbstractModel;
use \Magento\Framework\Model\ResourceModel\AbstractResource;
use \Magento\Framework\Data\Collection\AbstractDb;

class Setting extends AbstractModel
{

  /**
   * Constructor for the Setting model
   *
   * @param    Context              $context
   * @param    Registry             $registry
   * @param    AbstractResource     $resource
   * @param    AbstractDb           $resourceCollection
   * @param    array                $data
   */
    public function __construct(
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

  /**
   * Special constructor for the Setting model.
   * Associates the Setting model with it's resource model.
   *
   * @return void
   */
    public function _construct()
    {
        $this->_init('SendGrid\EmailDeliverySimplified\Model\ResourceModel\Setting');
    }
}
