<?php
namespace Adcurve\Adcurve\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface UpdateRepositoryInterface
{
    /**
     * Save Update
     * @param \Adcurve\Adcurve\Api\Data\UpdateInterface $update
     * @return \Adcurve\Adcurve\Api\Data\UpdateInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Adcurve\Adcurve\Api\Data\UpdateInterface $update
    );

    /**
     * Retrieve Update
     * @param string $updateId
     * @return \Adcurve\Adcurve\Api\Data\UpdateInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($updateId);

    /**
     * Retrieve Update matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Adcurve\Adcurve\Api\Data\UpdateSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Update
     * @param \Adcurve\Adcurve\Api\Data\UpdateInterface $update
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Adcurve\Adcurve\Api\Data\UpdateInterface $update
    );

    /**
     * Delete Update by ID
     * @param string $updateId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($updateId);
}
