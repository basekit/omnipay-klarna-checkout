<?php

namespace MyOnlineStore\Omnipay\KlarnaCheckout\Message;

use Guzzle\Http\Message\RequestInterface;

final class FetchTransactionRequest extends AbstractRequest
{
    /**
     * @inheritDoc
     */
    public function getData()
    {
        $this->validate('transactionReference');

        return null;
    }

    /**
     * @inheritDoc
     */
    public function sendData($data)
    {
        $response = $this->sendRequest(
            RequestInterface::GET,
            '/checkout/v3/orders/'.$this->getTransactionReference(),
            $data
        );

        $responseData['checkout'] = $this->getResponseBody($response);

        if (404 === $response->getStatusCode() ||
            (isset($responseData['checkout']['status']) && 'checkout_complete' === $responseData['checkout']['status'])
        ) {
            $responseData['management'] = $this->getResponseBody(
                $this->sendRequest(
                    RequestInterface::GET,
                    '/ordermanagement/v1/orders/'.$this->getTransactionReference(),
                    $data
                )
            );
        }

        return new FetchTransactionResponse($this, $responseData);
    }
}
