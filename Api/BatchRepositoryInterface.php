<?php


namespace Adcurve\Adcurve\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface BatchRepositoryInterface
{


    /**
     * Save Batch
     * @param \Adcurve\Adcurve\Api\Data\BatchInterface $batch
     * @return \Adcurve\Adcurve\Api\Data\BatchInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function save(
        \Adcurve\Adcurve\Api\Data\BatchInterface $batch
    );

    /**
     * Retrieve Batch
     * @param string $batchId
     * @return \Adcurve\Adcurve\Api\Data\BatchInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function getById($batchId);

    /**
     * Retrieve Batch matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Adcurve\Adcurve\Api\Data\BatchSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Batch
     * @param \Adcurve\Adcurve\Api\Data\BatchInterface $batch
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function delete(
        \Adcurve\Adcurve\Api\Data\BatchInterface $batch
    );

    /**
     * Delete Batch by ID
     * @param string $batchId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function deleteById($batchId);
}
