<?php
namespace Adcurve\Adcurve\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();
        if (version_compare($context->getVersion(), "1.0.1", "<")) {
        	

			$installer = $setup;
			$installer->startSetup();

			$installer->getConnection()->addColumn(
            $installer->getTable('adcurve_connection'),
            'excluded_attributes',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
		'comments'=> 'Excluded Attributes',
		'nullable' => true
            ]
			);
			$setup->endSetup();

        }
		if (version_compare($context->getVersion(), "1.0.2", "<")) {
        	

			$installer = $setup;
			$installer->startSetup();
			$connection = $installer->getConnection();
			//sales_order
			if ($connection->tableColumnExists($installer->getTable('sales_order'), 'adcurve_shipping_price') === false) {
				$installer->getConnection()->addColumn(
				$installer->getTable('sales_order'),
				'adcurve_shipping_price',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'length' => 255,
					'comment' => 'adcurve shipping price',
					'nullable' => true
				]
				);
			}
			if ($connection->tableColumnExists($installer->getTable('sales_order'), 'adcurve_order_source') === false) {
			
				$installer->getConnection()->addColumn(
				$installer->getTable('sales_order'),
				'adcurve_order_source',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'length' => 255,
					'comment' => 'adcurve order source',
					'nullable' => true
				]
				);
			}
			
			if ($connection->tableColumnExists($installer->getTable('sales_order'), 'adcurve_order_id') === false) 
			{
				$installer->getConnection()->addColumn(
				$installer->getTable('sales_order'),
				'adcurve_order_id',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'length' => 255,
					'comment' => 'adcurve order id',
					'nullable' => true
				]
				);
			}
			
			//quote
			if ($connection->tableColumnExists($installer->getTable('quote'), 'adcurve_shipping_price') === false) {
				$installer->getConnection()->addColumn(
				$installer->getTable('quote'),
				'adcurve_shipping_price',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'length' => 255,
					'comment' => 'adcurve shipping price',
					'nullable' => true
				]
				);
			}
			if ($connection->tableColumnExists($installer->getTable('quote'), 'adcurve_order_source') === false) {
			
				$installer->getConnection()->addColumn(
				$installer->getTable('quote'),
				'adcurve_order_source',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'length' => 255,
					'comment' => 'adcurve order source',
					'nullable' => true
				]
				);
			}
			
			if ($connection->tableColumnExists($installer->getTable('quote'), 'adcurve_order_id') === false) 
			{
				$installer->getConnection()->addColumn(
				$installer->getTable('quote'),
				'adcurve_order_id',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'length' => 255,
					'comment' => 'adcurve order id',
					'nullable' => true
				]
				);
			}
			
			//sales_order_grid
			if ($connection->tableColumnExists($installer->getTable('sales_order_grid'), 'adcurve_shipping_price') === false) {
				$installer->getConnection()->addColumn(
				$installer->getTable('sales_order_grid'),
				'adcurve_shipping_price',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'length' => 255,
					'comment' => 'adcurve shipping price',
					'nullable' => true
				]
				);
			}
			if ($connection->tableColumnExists($installer->getTable('sales_order_grid'), 'adcurve_order_source') === false) {
			
				$installer->getConnection()->addColumn(
				$installer->getTable('sales_order_grid'),
				'adcurve_order_source',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'length' => 255,
					'comment' => 'adcurve order source',
					'nullable' => true
				]
				);
			}
			
			if ($connection->tableColumnExists($installer->getTable('sales_order_grid'), 'adcurve_order_id') === false) 
			{
				$installer->getConnection()->addColumn(
				$installer->getTable('sales_order_grid'),
				'adcurve_order_id',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'length' => 255,
					'comment' => 'adcurve order id',
					'nullable' => true
				]
				);
			}
			$setup->endSetup();
		

        }
		
		if (version_compare($context->getVersion(), "1.0.3", "<")) {
			
				$installer = $setup;
				$installer->startSetup();
				
			    $installer->getConnection()->addColumn(
					$installer->getTable('adcurve_queue'),
					'page_no',
					[
						'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
						'nullable' => true,
						'default' => 0,
						'comment' =>'Page No',
					]
			    );

			   $installer->getConnection()->addColumn(
					$installer->getTable('adcurve_queue'),
					'status',
					[
						'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
						'length' => '20',
						'default' => \Adcurve\Adcurve\Model\Queue::QUEUE_STATUS_NEW,
						'comment' =>'Queue status',
					]
			   );

			   $setup->endSetup();
		
		}

    }
}
