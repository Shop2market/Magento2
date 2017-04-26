<?php
namespace Adcurve\Adcurve\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface QueueRepositoryInterface
{
    /**
     * Save Queue
     * @param \Adcurve\Adcurve\Api\Data\QueueInterface $queue
     * @return \Adcurve\Adcurve\Api\Data\QueueInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Adcurve\Adcurve\Api\Data\QueueInterface $queue
    );

    /**
     * Retrieve Queue
     * @param string $queueId
     * @return \Adcurve\Adcurve\Api\Data\QueueInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($queueId);

    /**
     * Retrieve Queue matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Adcurve\Adcurve\Api\Data\QueueSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Queue
     * @param \Adcurve\Adcurve\Api\Data\QueueInterface $queue
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Adcurve\Adcurve\Api\Data\QueueInterface $queue
    );

    /**
     * Delete Queue by ID
     * @param string $queueId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($queueId);
}
