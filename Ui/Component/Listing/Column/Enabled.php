<?php

namespace Adcurve\Adcurve\Ui\Component\Listing\Column;

use Magento\Sales\Api\OrderRepositoryInterface;

class Enabled extends \Magento\Ui\Component\Listing\Columns\Column implements \Magento\Framework\Option\ArrayInterface
{
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**#@+
     * Adcurve Conection Enabled values
     */

    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED = 1;


    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }

    /**
     * Retrieve option array
     *
     * @return string[]
     * phpcs:disable Magento2.Functions.StaticFunction
     */
    public static function getOptionArray()
    {
        return [
            self::STATUS_DISABLED => __('Disabled'),
            self::STATUS_ENABLED => __('Enabled'),

            ];
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * Retrieve option text by option value
     *
     * @param string $optionId
     * @return string
     */
    public function getOptionText($optionId)
    {
        $options = self::getOptionArray();

        return $options[$optionId] ?? null;
    }
}
