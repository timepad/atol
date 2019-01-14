<?php

namespace Omnipay\Atol\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractRequest;

/**
 * Class AbstractRestRequest
 * @package Omnipay\Atol\Message
 * @method $this setParameter($name, $value);
 */
abstract class AbstractRestRequest extends AbstractRequest
{
    const API_VERSION = 'v4';
    protected $liveEndpoint = 'https://online.atol.ru/possystem';
    protected $testEndpoint = 'https://testonline.atol.ru/possystem';


    public function getLogin()
    {
        return $this->getParameter('login');
    }

    public function setLogin($value)
    {
        return $this->setParameter('login', $value);
    }

    public function getPass()
    {
        return $this->getParameter('pass');
    }

    public function setPass($value)
    {
        return $this->setParameter('pass', $value);
    }

    public function getToken()
    {
        return $this->getParameter('token');
    }

    public function setToken($value)
    {
        return $this->setParameter('token', $value);
    }

    /**
     * Get HTTP Method.
     *
     * This is nearly always POST but can be over-ridden in sub classes.
     *
     * @return string
     */
    protected function getHttpMethod()
    {
        return 'POST';
    }

    protected function getEndpoint()
    {
        $base = $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
        return $base . '/' . self::API_VERSION;
    }

    public function sendData($data)
    {
        $this->validate('token');

        // don't throw exceptions for 4xx errors
        $this->httpClient->getEventDispatcher()->addListener(
            'request.error',
            function ($event) {
                if ($event['response']->isClientError()) {
                    $event->stopPropagation();
                }
            }
        );

        // Guzzle HTTP Client createRequest does funny things when a GET request
        // has attached data, so don't send the data if the method is GET.

        $ssl_verify = \TPConfig::Get('remote:atol:ssl_verify');
        $this->httpClient->setSslVerification($ssl_verify, $ssl_verify);
        $headers = [
            'Accept'       => 'application/json',
            'Content-type' => 'application/json; charset=utf-8',
            'Token'        => $this->getToken(),
        ];

        if ($this->getHttpMethod() == 'GET') {
            $httpRequest = $this->httpClient->createRequest(
                $this->getHttpMethod(),
                $this->getEndpoint(),
                $headers
            );

        } else {
            $httpRequest = $this->httpClient->createRequest(
                $this->getHttpMethod(),
                $this->getEndpoint(),
                $headers,
                $this->toJSON($data),
                $this->getCurlOptions()
            );
        }

        try {
            $httpRequest->getCurlOptions()->set(CURLOPT_SSLVERSION, 6); // CURL_SSLVERSION_TLSv1_2 for libcurl < 7.35
            $httpResponse = $httpRequest->send();

            // Empty response body should be parsed also as and empty array
            $body = $httpResponse->getBody(true);
            $jsonToArrayResponse = !empty($body) ? $httpResponse->json() : [];

            return $this->response = $this->createResponse($jsonToArrayResponse, $httpResponse->getStatusCode());
        } catch (\Exception $e) {
            throw new InvalidResponseException(
                'Error communicating with payment gateway: ' . $e->getMessage(),
                $e->getCode()
            );
        }
    }

    public function getCurlOptions() {
        $options = [];

        $proxy = \TPConfig::Get('remote:atol:proxy');
        if($proxy) {
            $options['proxy'] = $proxy;
        }

        return $options;
    }

    public function toJSON($data, $options = 0)
    {
        return json_encode($data, $options | 64);
    }

    protected function createResponse($data, $statusCode)
    {
        return $this->response = new RestResponse($this, $data, $statusCode);
    }

    public function getGroupCode()
    {
        return $this->getParameter('groupCode');
    }

    public function setGroupCode($value)
    {
        return $this->setParameter('groupCode', $value);
    }

    public function getSumFormat($name)
    {
        if (is_null($this->getParameter($name))){
            return null;
        }
        return floatval($this->getParameter($name));
    }
}
