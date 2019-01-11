<?php
namespace Adcurve\Adcurve\Model\Method;

class Adcurvepayment extends \Magento\Payment\Model\Method\AbstractMethod
{
    protected $_code = 'adcurvepayment';
    protected $_canUseInternal = true;
    protected $_canUseCheckout = true;
    protected $_canUseForMultishipping = false;
}
