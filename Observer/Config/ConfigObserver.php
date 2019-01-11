<?php
namespace Adcurve\Adcurve\Observer\Config;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Psr\Log\LoggerInterface as Logger;

class ConfigObserver implements ObserverInterface
{
	const XPATH_SETTINGS_FEATURES   = 'adcurve/settings/enabled_features';
	const XPATH_PAYMENT_METHOD      = 'payment/checkmo/active';
	/**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
	/**
     *  @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param Logger $logger
     */
    public function __construct(
        Logger $logger,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\App\Config\Storage\WriterInterface $configWriter
    ) {
         $this->logger = $logger;
		 $this->scopeConfig = $scopeConfig;
		 $this->configWriter = $configWriter;
    }

    public function execute(EventObserver $observer)
    {
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $enabled_features = $this->scopeConfig->getValue(self::XPATH_SETTINGS_FEATURES, $storeScope);
		//enable checkmo, if enabled features
        if ($enabled_features) {
			$this->configWriter->save(self::XPATH_PAYMENT_METHOD, $enabled_features,'default',0);
		}
        
    }
}
