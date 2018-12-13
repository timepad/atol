<?php

namespace Omnipay\Atol\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Class ActionRequest
 * @package Omnipay\Atol\Message
 * @method $this setParameter($name, $value);
 * @method ActionResponse send()
 */
class ActionRequest extends AbstractRestRequest
{
    public function getData()
    {
        $this->validate('externalId', 'inn', 'datePayment', 'paymentAddress', 'totalSum', 'typeSum', 'totalSum');

        if (empty($this->getOrgEmail())) {
            $this->validate('org_email');
        }

        $data = [
            'external_id' => (string)$this->getExternalId(),
            'service'     => [
                'callback_url' => $this->getCallBackUrl(),
            ],
            'timestamp'   => $this->getDatePayment(),// '29.05.2017 17:56:18',
            'receipt'     => [
                'client'   => [
                    'email' => $this->getClientEmail(),
                ],
                'company'  => [
                    'email'           => (string)$this->getOrgEmail(),
                    'sno'             => $this->getSno(),
                    'inn'             => $this->getInn(),
                    'payment_address' => $this->getPaymentAddress(),
                ],
                'payments' => [
                    [
                        'type' => $this->getTypeSum(),
                        'sum'  => $this->getTotalSum(),
                    ]
                ],
                'total'    => $this->getTotalSum(),
            ]
        ];

        /** @var Item $item */
        foreach ($this->getItems() as $item) {
            if (empty($item->getName()) || empty($item->getPrice()) || empty($item->getQuantity())
                || empty($item->getSum()) || empty($item->getTax())
            ) {
                throw new InvalidRequestException("The Item parameter name, price, quantity, sum and tax is required");
            }
            $data['receipt']['items'][] = [
                'name'           => $item->getName(),
                'price'          => $item->getPrice(),
                'quantity'       => $item->getQuantity(),
                'sum'            => $item->getSum(),
                'payment_method' => $item->getPaymentMethod(),//TODO не забыть передать их
                'payment_object' => $item->getPaymentObject(),//TODO не забыть передать их
                'vat'            => [
                    'type' => $item->getTax(),
                    'sum'  => $item->getTaxSum(),
                ]

            ];
        }
        return $data;
    }

    /**
     * Get transaction endpoint.
     *
     * Authorization of payments is done using the /payment resource.
     *
     * @return string
     */
    protected function getEndpoint()
    {
        $this->validate('groupCode', 'action');
        return parent::getEndpoint() . '/' . $this->getGroupCode() . '/' . $this->getAction();
    }

    protected function createResponse($data, $statusCode)
    {
        return $this->response = new ActionResponse($this, $data, $statusCode);
    }

    public function getAction()
    {
        return $this->getParameter('action');
    }

    public function setAction($value)
    {
        return $this->setParameter('action', $value);
    }

    public function getExternalId()
    {
        return $this->getParameter('externalId');
    }

    public function setExternalId($value)
    {
        return $this->setParameter('externalId', $value);
    }

    public function getOrgEmail()
    {
        return $this->getParameter('org_email');
    }

    public function setOrgEmail($value)
    {
        return $this->setParameter('org_email', $value);
    }

    public function getClientEmail()
    {
        return $this->getParameter('client_email');
    }

    public function setClientEmail($value)
    {
        return $this->setParameter('client_email', $value);
    }

    public function getSno()
    {
        return $this->getParameter('sno');
    }

    public function setSno($value)
    {
        return $this->setParameter('sno', $value);
    }

    public function getTypeSum()
    {
        return $this->getParameter('typeSum');
    }

    public function setTypeSum($value)
    {
        return $this->setParameter('typeSum', $value);
    }

    public function getCallBackUrl()
    {
        return $this->getParameter('callBackUrl');
    }

    public function setCallBackUrl($value)
    {
        return $this->setParameter('callBackUrl', $value);
    }

    public function getInn()
    {
        return $this->getParameter('inn');
    }

    public function setInn($value)
    {
        return $this->setParameter('inn', $value);
    }

    public function getPaymentAddress()
    {
        return $this->getParameter('paymentAddress');
    }

    public function setPaymentAddress($value)
    {
        return $this->setParameter('paymentAddress', $value);
    }

    public function getTotalSum()
    {
        return $this->getSumFormat('totalSum');
    }

    public function setTotalSum($value)
    {
        return $this->setParameter('totalSum', $value);
    }

    public function getTax()
    {
        return $this->getSumFormat('tax');
    }

    public function setTax($value)
    {
        return $this->setParameter('tax', $value);
    }

    public function getDatePayment($format = 'd.m.Y H:i:s')// '29.05.2017 17:56:18',
    {
        $date = $this->getParameter('datePayment');
        if (!($date instanceof \DateTime)) {
            $date = new \DateTime($date);
        }
        return $date->format($format);
    }

    public function setDatePayment($value)
    {
        return $this->setParameter('datePayment', $value);
    }

    /**
     * «osn» – общая СН;
     *
     * @return ActionRequest
     */
    public function setSnoOsn()
    {
        return $this->setSno('osn');
    }

    /**
     * «usn_income» – упрощенная СН (доходы);
     *
     * @return ActionRequest
     */
    public function setSnoUsnIncome()
    {
        return $this->setSno('usn_income');
    }

    /**
     * «usn_income_outcome» – упрощенная СН (доходы минус расходы);
     *
     * @return ActionRequest
     */
    public function setSnoUsnIncomeOutcome()
    {
        return $this->setSno('usn_income_outcome');
    }

    /**
     * «envd» – единый налог на вмененный доход;
     *
     * @return ActionRequest
     */
    public function setSnoEnvd()
    {
        return $this->setSno('envd');
    }

    /**
     * «esn» – единый сельскохозяйственный налог;
     *
     * @return ActionRequest
     */
    public function setSnoEsn()
    {
        return $this->setSno('esn');
    }

    /**
     * «patent» – патентная СН.
     *
     * @return ActionRequest
     */
    public function setSnoPatent()
    {
        return $this->setSno('patent');
    }
}
