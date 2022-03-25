<?php

namespace Adcurve\Adcurve\Ui\Component\Listing\Column;

use Magento\Sales\Api\OrderRepositoryInterface;

class Status extends \Magento\Ui\Component\Listing\Columns\Column implements \Magento\Framework\Option\ArrayInterface
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
     * Adcurve Conection Status values
     */

    public const STATUS_INITIAL = 1;
    public const STATUS_PRE_REGISTRATION = 2;
    public const STATUS_POST_REGISTRATION = 33;
    public const STATUS_ERROR_CONNECTION_TO_ADCURVE = 4;
    public const STATUS_ERROR_RESULT_FROM_ADCURVE = 5;
    public const STATUS_SUCCESS = 3;


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
            self::STATUS_INITIAL => __('Not Registered'),
            self::STATUS_PRE_REGISTRATION => __('Ready to Register'),
            self::STATUS_POST_REGISTRATION => __('Registration in progress'),
            self::STATUS_ERROR_CONNECTION_TO_ADCURVE => __('Connection Error'),
            self::STATUS_ERROR_RESULT_FROM_ADCURVE => __('Registration Error'),
            self::STATUS_SUCCESS => __('Registered'),

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
