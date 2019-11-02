<?php

namespace SendGrid\EmailDeliverySimplified\Controller\Adminhtml\Statistics;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Index extends \Magento\Backend\App\Action
{
  /**
   * @var PageFactory
   */
    protected $_resultPageFactory;

  /**
   * Constructor for the Statistics page controller
   *
   * @param   Context                 $context
   * @param   PageFactory             $resultPageFactory
   * @param   ScopeConfigInterface    $scopeConfig
   */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
    }

  /**
   * Index action
   *
   * @return void
   */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();

        return $resultPage;
    }
}
