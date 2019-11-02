<?php

namespace SendGrid\EmailDeliverySimplified\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
  /**
   * @var Logger
   */
    protected $_logger;

  /**
   * @param   LoggerInterface   $loggerInterface
   */
    public function __construct(\Psr\Log\LoggerInterface $loggerInterface)
    {
        $this->_logger = $loggerInterface;
    }

  /**
   * {@inheritdoc}
   * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
   */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->_logger->debug('[SendGrid] Install script is running.');
        $this->_logger->debug('[SendGrid] Install script is creating sendgrid_settings.');

        $setup->startSetup();

        $table = $setup->getConnection()->newTable(
            $setup->getTable('sendgrid_settings')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Setting ID'
        )->addColumn(
            'key',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Setting Key'
        )->addColumn(
            'value',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Setting Value'
        )->setComment(
            'SendGrid Settings Table'
        );

        $setup->getConnection()->createTable($table);
        $setup->endSetup();

        $this->_logger->debug('[SendGrid] Install script is done.');
    }
}
