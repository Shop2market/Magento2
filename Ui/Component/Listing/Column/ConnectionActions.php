<?php

namespace Adcurve\Adcurve\Ui\Component\Listing\Column;

class ConnectionActions extends \Magento\Ui\Component\Listing\Columns\Column
{
    protected $urlBuilder;

    /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['connection_id'])) {
                    if ($item['status'] == \Adcurve\Adcurve\Model\Connection::STATUS_PRE_REGISTRATION) {
                        $item[$this->getData('name')]['register'] = [
                            'href' => $this->urlBuilder->getUrl(
                                'adcurve_adcurve/connection/register',
                                ['connection_id' => $item['connection_id']]
                            ),
                            'label' => __('Register to Adcurve')
                        ];
                    }

                    if (
                        $item['status'] == \Adcurve\Adcurve\Model\Connection::STATUS_POST_REGISTRATION
                        || $item['status'] == \Adcurve\Adcurve\Model\Connection::STATUS_ERROR_CONNECTION_TO_ADCURVE
                        || $item['status'] == \Adcurve\Adcurve\Model\Connection::STATUS_ERROR_RESULT_FROM_ADCURVE
                    ) {
                        $item[$this->getData('name')]['validate_connection'] = [
                            'href' => $this->urlBuilder->getUrl(
                                'adcurve_adcurve/connection/validate',
                                ['connection_id' => $item['connection_id']]
                            ),
                            'label' => __('Validate Connection')
                        ];
                    }

                    if ($item['status'] == \Adcurve\Adcurve\Model\Connection::STATUS_SUCCESS) {
                        $item[$this->getData('name')]['queue_products'] = [
                            'href' => $this->urlBuilder->getUrl(
                                'adcurve_adcurve/connection/products_queueall',
                                ['store_id' => $item['store_id']]
                            ),
                            'label' => __('Queue complete sync')
                        ];
                    }

                    $item[$this->getData('name')]['edit'] = [
                        'href' => $this->urlBuilder->getUrl(
                            'adcurve_adcurve/connection/edit',
                            ['connection_id' => $item['connection_id']]
                        ),
                        'label' => __('Edit')
                    ];

                    $item[$this->getData('name')]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(
                            'adcurve_adcurve/connection/delete',
                            ['connection_id' => $item['connection_id']]
                        ),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Warning!'),
                            'message' => __('You are about to delete the connection to Adcurve for "%1" (%2)<br /><br />Are you sure you want to proceed?', $item['store_name'], $item['store_code'])
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
