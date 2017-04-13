<?php
namespace Adcurve\Adcurve\Ui\Component\Listing\Column;

class ConnectionActions extends \Magento\Ui\Component\Listing\Columns\Column
{

    protected $urlBuilder;
    const URL_PATH_EDIT 	= 'adcurve_adcurve/connection/edit';
    const URL_PATH_DELETE 	= 'adcurve_adcurve/connection/delete';
    const URL_PATH_DETAILS 	= 'adcurve_adcurve/connection/details';
	const URL_PATH_ADCURVE_REGISTER_FORM = 'adcurve_adcurve/connection/register';

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
                	if ($item['status'] == 2) {
						$item[$this->getData('name')]['setup'] = [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_ADCURVE_REGISTER_FORM,
                                [
                                    'connection_id' => $item['connection_id']
                                ]
                            ),
                            'label' => __('Setup Adcurve Connection')
                        ];
					}
					
                    $item[$this->getData('name')]['edit'] = [
                        'href' => $this->urlBuilder->getUrl(
                            static::URL_PATH_EDIT,
                            [
                                'connection_id' => $item['connection_id']
                            ]
                        ),
                        'label' => __('Edit')
                    ];
					
                    $item[$this->getData('name')]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(
                            static::URL_PATH_DELETE,
                            [
                                'connection_id' => $item['connection_id']
                            ]
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
