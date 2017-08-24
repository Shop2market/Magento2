<?php
namespace Adcurve\Adcurve\Model\ResourceModel;

class Update extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('adcurve_update', 'update_id');
    }
}