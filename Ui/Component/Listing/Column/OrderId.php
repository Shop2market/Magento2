<?php
namespace Adcurve\Adcurve\Ui\Component\Listing\Column;
use \Magento\Sales\Api\OrderRepositoryInterface; 
class OrderId extends \Magento\Ui\Component\Listing\Columns\Column
{
	const XPATH_SETTINGS_FEATURES   = 'adcurve/settings/enabled_features';
    protected $urlBuilder;
	protected $_orderRepository;

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
		OrderRepositoryInterface $orderRepository,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
		$this->scopeConfig = $scopeConfig;
		$this->_orderRepository = $orderRepository;
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
			
				
				$order  = $this->_orderRepository->get($item["entity_id"]);
				$adcurve_order_id = $order->getData("adcurve_order_id");

				$item[$this->getData('name')] = $adcurve_order_id; 
            }
        }
        
        return $dataSource;
    }
	/**
     * Prepare component configuration
     * @return void
     */
    public function prepare()
    {
        parent::prepare();
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $enabled_features = $this->scopeConfig->getValue(self::XPATH_SETTINGS_FEATURES, $storeScope);
		if (!$enabled_features) {
			 $this->_data['config']['componentDisabled'] = true;
		}
    }
}
