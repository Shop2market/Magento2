<?php

namespace Adcurve\Adcurve\Api\Data;

interface UpdateInterface
{
    public const STATUS = 'status';
    public const UPDATE_ID = 'update_id';

    /**
     * Get update_id
     * @return string|null
     */
    public function getUpdateId();

    /**
     * Set update_id
     * @param string $update_id
     * @return Adcurve\Adcurve\Api\Data\UpdateInterface
     */
    public function setUpdateId($updateId);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return Adcurve\Adcurve\Api\Data\UpdateInterface
     */
    public function setStatus($status);
}
