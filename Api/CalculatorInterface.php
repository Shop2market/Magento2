<?php
namespace Adcurve\Adcurve\Api;
 
interface CalculatorInterface
{
	/**
     * Return the sum of the two numbers.
     *
     * @api
     * @param int $num1
     * @param int $num2
     * @return
     */
    public function add($num1, $num2);
}