<?php


namespace Adcurve\Adcurve\Model\ResourceModel\Batch;

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
            'Adcurve\Adcurve\Model\Batch',
            'Adcurve\Adcurve\Model\ResourceModel\Batch'
        );
    }
}
