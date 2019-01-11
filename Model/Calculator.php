<?php

namespace Adcurve\Adcurve\Model;
use Adcurve\Adcurve\Api\CalculatorInterface;
 
class Calculator implements CalculatorInterface
{
	/**
     * Return the sum of the two numbers.
     *
     * @api
     * @param int $num1
     * @param int $num2
     * @return
     */
    public function add($num1, $num2) {
        return $num1 + $num2;
    }

}