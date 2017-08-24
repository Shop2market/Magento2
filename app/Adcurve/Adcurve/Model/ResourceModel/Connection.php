<?php
namespace Adcurve\Adcurve\Model\ResourceModel;

class Connection extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('adcurve_connection', 'connection_id');
    }
}
