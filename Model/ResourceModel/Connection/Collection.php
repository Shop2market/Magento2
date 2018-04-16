<?php
namespace Adcurve\Adcurve\Model\ResourceModel\Connection;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Adcurve\Adcurve\Model\Connection',
            'Adcurve\Adcurve\Model\ResourceModel\Connection'
        );
    }
}
