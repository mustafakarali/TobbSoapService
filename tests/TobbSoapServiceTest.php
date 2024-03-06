<?php

use Karali\TobbSoapService;
use PHPUnit\Framework\TestCase;

class TobbSoapServiceTest extends TestCase
{
    public function testCallServiceReturnsExpectedValue()
    {
        // Test için gerekli parametreler
        $username = 'testUsername';
        $password = 'testPassword';
        $odaBorsaNo = '111';
        $servisAdi = 'odayaAitUyeleriSorgula';
        $girdiler = '{}';

        // SOAP istemcisini gerçek bir web servisine bağlamak yerine bir taklit (mock) oluşturalım.
        $soapClientMock = $this->createMock(SoapClient::class);

        // __soapCall metodunun beklenen davranışını ayarlayalım
        $expectedResult = 'testResponse'; // Beklenen dönüş değeri
        $soapClientMock->method('__soapCall')
            ->willReturn((object)['return' => (object)['donusDegeri' => $expectedResult]]);

        // TobbSoapService nesnesini mock SoapClient ile oluşturalım
        $service = new TobbSoapService($username, $password, $odaBorsaNo, $servisAdi, $girdiler);
        $service->setClient($soapClientMock);

        // callService metodunu çağıralım ve sonucu kontrol edelim
        $result = $service->callService();
        $this->assertEquals($expectedResult, $result);
    }
}
