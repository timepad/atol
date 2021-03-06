<?php
/**
 * Cart Item
 */

namespace Omnipay\Atol\Message;

use Omnipay\Common\Item as OmnipayItem;

/**
 * Cart Item
 *
 * @method $this setPrice($value)
 * @method $this setParameter($key, $value)
 * @method $this setDescription($value)
 * @method $this setQuantity($value)
 * @method $this setName($value)
 */
class Item extends OmnipayItem
{
    public function getSum()
    {
        return $this->getSumFormat('sum');
    }

    public function setSum($value)
    {
        return $this->setParameter('sum', $value);
    }

    public function getTax()
    {
        return $this->getParameter('tax');
    }

    public function setTax($value)
    {
        return $this->setParameter('tax', $value);
    }

    public function getTaxSum()
    {
        return $this->getSumFormat('taxSum');
    }

    public function setTaxSum($value)
    {
        return $this->setParameter('taxSum', $value);
    }

    /**
     * «none» – без НДС;
     */
    public function setTaxNone()
    {
        return $this->setTax('none');
    }

    /**
     * «vat0» – НДС по ставке 0%;
     */
    public function setTaxVat0()
    {
        return $this->setTax('vat0');
    }

    /**
     * «vat10» – НДС чека по ставке 10%;
     */
    public function setTaxVat10()
    {
        return $this->setTax('vat10');
    }

    /**
     * «vat18» – НДС чека по ставке 18%;
     */
    public function setTaxVat18()
    {
        return $this->setTax('vat18');
    }

    /**
     * «vat110» – НДС чека по расчетной ставке 10/110;
     */
    public function setTaxVat110()
    {
        return $this->setTax('vat110');
    }

    /**
     * «vat118» – НДС чека по расчетной ставке 18/118.
     */
    public function setTaxVat118()
    {
        return $this->setTax('vat118');
    }

    public function getPrice()
    {
        return $this->getSumFormat('price');
    }

    public function getSumFormat($name)
    {
        if (is_null($this->getParameter($name))) {
            return null;
        }
        return floatval($this->getParameter($name));
    }

    public function getQuantity()
    {
        return $this->getSumFormat('quantity');
    }

    public function setPaymentMethod($method = 'full_prepayment') {
        return $this->setParameter('payment_method', $method);
    }

    public function getPaymentMethod() {
        return $this->getParameter('payment_method');
    }

    public function setPaymentObject($method = 'service') {
        return $this->setParameter('payment_object', $method);
    }

    public function getPaymentObject() {
        return $this->getParameter('payment_object');
    }

    public function setSupplierPhones(array $phones = []) {
        return $this->setParameter('supplier_phones', $phones);
    }

    public function getSupplierPhones() {
        return $this->getParameter('supplier_phones');
    }

    public function setSupplierName(string $name) {
        return $this->setParameter('supplier_name', $name);
    }

    public function getSupplierName() {
        return $this->getParameter('supplier_name');
    }

    public function setSupplierInn(string $inn) {
        return $this->setParameter('supplier_inn', $inn);
    }

    public function getSupplierInn() {
        return $this->getParameter('supplier_inn');
    }
}

