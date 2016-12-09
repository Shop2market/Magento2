<?php


namespace Adcurve\Adcurve\Model\ResourceModel;

class Batch extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('adcurve_batch', 'batch_id');
    }
}
