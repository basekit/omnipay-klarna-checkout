<?php

namespace MyOnlineStore\Tests\Omnipay\KlarnaCheckout\Message;

use MyOnlineStore\Omnipay\KlarnaCheckout\Message\UpdateCustomerAddressRequest;
use MyOnlineStore\Omnipay\KlarnaCheckout\Message\UpdateCustomerAddressResponse;
use Omnipay\Common\Exception\InvalidRequestException;

final class UpdateCustomerAddressRequestTest extends RequestTestCase
{
    /**
     * @var UpdateCustomerAddressRequest
     */
    private $updateCustomerAddressRequest;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->updateCustomerAddressRequest = new UpdateCustomerAddressRequest(
            $this->httpClient,
            $this->getHttpRequest()
        );
    }

    /**
     * @return array
     */
    public function addressDataProvider()
    {
        return [
            [
                [
                    'organization_name' => null,
                    'reference' => 'ref',
                    'attention' => 'quz',
                    'given_name' => 'foo',
                    'family_name' => 'bar',
                    'email' => 'foo@bar.com',
                    'title' => 'Mr.',
                    'street_address' => 'Foo Street 1',
                    'street_address2' => 'App. 12A',
                    'street_name' => 'Foo Street',
                    'street_number' => '1',
                    'house_extension' => 'C',
                    'postal_code' => '523354',
                    'city' => 'Oss',
                    'region' => 'NB',
                    'phone' => '24234234',
                    'country' => 'NL',
                ],
                [
                    'reference' => 'ref',
                    'attention' => 'quz',
                    'given_name' => 'foo',
                    'family_name' => 'bar',
                    'email' => 'foo@bar.com',
                    'title' => 'Mr.',
                    'street_address' => 'Foo Street 1',
                    'street_address2' => 'App. 12A',
                    'street_name' => 'Foo Street',
                    'street_number' => '1',
                    'house_extension' => 'C',
                    'postal_code' => '523354',
                    'city' => 'Oss',
                    'region' => 'NB',
                    'phone' => '24234234',
                    'country' => 'NL',
                ],
            ],
            [
                [
                    'organization_name' => 'Foobar BV',
                    'reference' => 'ref',
                    'attention' => 'quz',
                    'given_name' => 'foo',
                    'family_name' => 'bar',
                    'email' => 'foo@bar.com',
                    'title' => 'Mr.',
                    'street_address' => 'Foo Street 1',
                    'street_address2' => 'App. 12A',
                    'street_name' => 'Foo Street',
                    'street_number' => '1',
                    'house_extension' => 'C',
                    'postal_code' => '523354',
                    'city' => 'Oss',
                    'region' => 'NB',
                    'phone' => '24234234',
                    'country' => 'NL',
                ],
                [
                    'organization_name' => 'Foobar BV',
                    'reference' => 'ref',
                    'attention' => 'quz',
                    'given_name' => 'foo',
                    'family_name' => 'bar',
                    'email' => 'foo@bar.com',
                    'title' => 'Mr.',
                    'street_address' => 'Foo Street 1',
                    'street_address2' => 'App. 12A',
                    'street_name' => 'Foo Street',
                    'street_number' => '1',
                    'house_extension' => 'C',
                    'postal_code' => '523354',
                    'city' => 'Oss',
                    'region' => 'NB',
                    'phone' => '24234234',
                    'country' => 'NL',
                ],
            ],
        ];
    }

    /**
     * @dataProvider addressDataProvider
     *
     * @param array $addressData
     * @param array $expectedOutcome
     */
    public function testGetDataWillReturnCorrectData(array $addressData, array $expectedOutcome)
    {
        $this->updateCustomerAddressRequest->initialize(
            [
                'transactionReference' => 123,
                'billing_address' => $addressData,
                'shipping_address' => $addressData,
                'exclude_empty_values' => ['organization_name'],
            ]
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        self::assertEquals(
            [
                'shipping_address' => $expectedOutcome,
                'billing_address' => $expectedOutcome,
            ],
            $this->updateCustomerAddressRequest->getData()
        );
    }

    public function testGetDataWillThrowExceptionOnMissingData()
    {
        $this->setExpectedException(InvalidRequestException::class);

        $this->updateCustomerAddressRequest->getData();
    }

    public function testSendDataWillWillSendDataToKlarnaEndPointAndReturnCorrectResponse()
    {
        $transactionReference = 'foo';
        $data = ['foo' => 'bar'];
        $responseData = ['hello' => 'world'];

        $response = $this->setExpectedPatchRequest(
            $data,
            $responseData,
            sprintf('%s/ordermanagement/v1/orders/%s/customer-details', self::BASE_URL, $transactionReference)
        );

        $response->shouldReceive('getStatusCode')->andReturn(204);

        $this->updateCustomerAddressRequest->initialize(
            [
                'base_url' => self::BASE_URL,
                'merchant_id' => self::USERNAME,
                'secret' => self::SECRET,
                'transactionReference' => $transactionReference,
            ]
        );

        $updateCustomerAddressResponse = $this->updateCustomerAddressRequest->sendData($data);

        self::assertInstanceOf(UpdateCustomerAddressResponse::class, $updateCustomerAddressResponse);
        self::assertSame($transactionReference, $updateCustomerAddressResponse->getTransactionReference());
        self::assertSame(
            array_merge($responseData, ['order_id' => $transactionReference]),
            $updateCustomerAddressResponse->getData()
        );
        self::assertTrue($updateCustomerAddressResponse->isSuccessful());
    }

    public function testSetAndGetExcludeEmptyValues()
    {
        $parameters = ['foo'];
        $this->updateCustomerAddressRequest->setExcludeEmptyValues($parameters);

        self::assertSame($parameters, $this->updateCustomerAddressRequest->getExcludeEmptyValues());
    }
}
