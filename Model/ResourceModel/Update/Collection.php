<?php

namespace Adcurve\Adcurve\Model\ResourceModel\Update;

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
            'Adcurve\Adcurve\Model\Update',
            'Adcurve\Adcurve\Model\ResourceModel\Update'
        );
    }
}
