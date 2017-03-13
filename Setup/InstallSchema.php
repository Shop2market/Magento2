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
        $adcurveUpdateTable->addColumn('product_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [], 'Product ID');
        $adcurveUpdateTable->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [], 'Store ID');
        $adcurveUpdateTable->addColumn('product_data', \Magento\Framework\DB\Ddl\Table::TYPE_BLOB, null, [], 'Product Data');
        $adcurveUpdateTable->addColumn('status', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Status');
        $adcurveUpdateTable->addColumn('retry_count', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array('default' => 0), 'Retry Count');
        $adcurveUpdateTable->addColumn('exception_message', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, [], 'Exception Message');
        $adcurveUpdateTable->addColumn('exported_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [], 'Exported At');
        $adcurveUpdateTable->addColumn('created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [], 'Created At');
        $adcurveUpdateTable->addColumn('updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [], 'Updated At');
		
        $setup->getConnection()->createTable($adcurveUpdateTable);
		
		$adcurveConnectionTable = $setup->getConnection()->newTable($setup->getTable('adcurve_connection'));
        $adcurveConnectionTable->addColumn(
        	'connection_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
        		'identity' 	=> true,
        		'nullable' 	=> false,
        		'primary' 	=> true,
        		'unsigned' 	=> true,
			), 'Connection ID'
		);
		$adcurveConnectionTable->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Store ID');
        $adcurveConnectionTable->addColumn('store_code', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Shop Code');
		$adcurveConnectionTable->addColumn('adcurve_shop_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Adcurve Shop ID');
		$adcurveConnectionTable->addColumn('adcurve_token', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Adcurve Token');
		$adcurveConnectionTable->addColumn('enabled', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Enabled');
		$adcurveConnectionTable->addColumn('status', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Status');
		$adcurveConnectionTable->addColumn('suggestion', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Suggestion');
		$adcurveConnectionTable->addColumn('api_user_info', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Api User Info');
		$adcurveConnectionTable->addColumn('updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [], 'Updated At');
		
        $setup->getConnection()->createTable($adcurveConnectionTable);
		
        $setup->endSetup();
    }
}