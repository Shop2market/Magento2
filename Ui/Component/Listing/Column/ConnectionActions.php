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
						$item[$this->getData('name')]['setup'] = [
                            'href' => $this->urlBuilder->getUrl(
                                'adcurve_adcurve/connection/register', ['connection_id' => $item['connection_id']]
                            ),
                            'label' => __('Setup Adcurve Connection')
                        ];
					}
					
					if ($item['status'] == \Adcurve\Adcurve\Model\Connection::STATUS_POST_REGISTRATION) {
						$item[$this->getData('name')]['setup'] = [
                            'href' => $this->urlBuilder->getUrl(
                                'adcurve_adcurve/connection/validate', ['connection_id' => $item['connection_id']]
                            ),
                            'label' => __('Validate Connection')
                        ];
					}
					
                    $item[$this->getData('name')]['edit'] = [
                        'href' => $this->urlBuilder->getUrl(
                            'adcurve_adcurve/connection/edit', ['connection_id' => $item['connection_id']]
                        ),
                        'label' => __('Edit')
                    ];
					
                    $item[$this->getData('name')]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(
                            'adcurve_adcurve/connection/delete', ['connection_id' => $item['connection_id']]
                        ),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete "${ $.$data.title }"'),
                            'message' => __('Are you sure you wan\'t to delete a "${ $.$data.title }" record?')
                        ]
                    ];
                }
            }
        }
        
        return $dataSource;
    }
}
