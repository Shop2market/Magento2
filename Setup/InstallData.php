<?php

namespace Adcurve\Adcurve\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * @var ConfigBasedIntegrationManager
     */
    private $integrationManager;

    public function __construct(
        \Magento\Integration\Model\ConfigBasedIntegrationManager $integrationManager
    ) {
        $this->integrationManager = $integrationManager;
    }

    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $this->integrationManager->processIntegrationConfig(['AdcurveIntegration']);
    }
}
