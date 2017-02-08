<?php
namespace Adcurve\Adcurve\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\InstallSchemaInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();
		
        $adcurveUpdateTable = $setup->getConnection()->newTable($setup->getTable('adcurve_update'));
        
        $adcurveUpdateTable->addColumn(
            'update_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
                'unsigned' => true,
                'nullable' => false,
                'primary'  => true,
                'identity' => true,
            ), 'Update ID'
        );
        $adcurveUpdateTable->addColumn('product_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(), 'Product ID');
        $adcurveUpdateTable->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(), 'Store ID');
        $adcurveUpdateTable->addColumn('product_data', \Magento\Framework\DB\Ddl\Table::TYPE_BLOB, null, array(), 'Product Data');
        $adcurveUpdateTable->addColumn('status', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(), 'Status');
        $adcurveUpdateTable->addColumn('retry_count', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array('default' => 0), 'Retry Count');
        $adcurveUpdateTable->addColumn('exception_message', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, array(), 'Exception Message');
        $adcurveUpdateTable->addColumn('exported_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(), 'Exported At');
        $adcurveUpdateTable->addColumn('created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(), 'Created At');
        $adcurveUpdateTable->addColumn('updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(), 'Updated At');
		
        $setup->getConnection()->createTable($adcurveUpdateTable);
        $setup->endSetup();
    }
}