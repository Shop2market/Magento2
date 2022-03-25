<?php

namespace Adcurve\Adcurve\Block\Adminhtml\Connection\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Adcurve\Adcurve\Block\Adminhtml\Connection\GenericButton;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array $data
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getModelId()) {
            $data = [
                'label' => __('Delete connection to Adcurve'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this? The connection to Adcurve will be lost, requiring a new registration at the platform.'
                ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * Get URL for delete button
     *
     * @return string $url
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['connection_id' => $this->getModelId()]);
    }
}
