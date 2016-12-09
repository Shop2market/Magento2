<?php


namespace Adcurve\Adcurve\Api\Data;

interface BatchInterface
{

    const BATCH_ID = 'batch_id';
    const STATUS = 'status';


    /**
     * Get batch_id
     * @return string|null
     */
    
    public function getBatchId();

    /**
     * Set batch_id
     * @param string $batch_id
     * @return Adcurve\Adcurve\Api\Data\BatchInterface
     */
    
    public function setBatchId($batchId);

    /**
     * Get status
     * @return string|null
     */
    
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return Adcurve\Adcurve\Api\Data\BatchInterface
     */
    
    public function setStatus($status);
}
