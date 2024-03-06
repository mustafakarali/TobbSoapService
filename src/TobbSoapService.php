<?php

/**
 *  TobbSoapService
 *
 * TOBB üye dizini SOAP istemcisi
 */

namespace Karali;

use SoapClient;
use SoapFault;
use SoapHeader;
use SoapVar;

class TobbSoapService
{
    private SoapClient $client;
    private string $username;
    private string $password;
    private string $odaBorsaNo;
    private string $servisAdi;
    private string $girdiler;

    /**
     * @param string $username Kimlik doğrulama için kullanılan kullanıcı adı
     * @param string $password Kimlik doğrulama için kullanılan parola
     * @param string $odaBorsaNo OdaBorsa numarası
     * @param string $servisAdi Hizmet adı
     * @param string $girdiler Giriş verileri
     * @throws SoapFault
     */
    public function __construct(string $username, string $password, string $odaBorsaNo, string $servisAdi, string $girdiler)
    {
        $this->username = $username;
        $this->password = $password;
        $this->odaBorsaNo = $odaBorsaNo;
        $this->servisAdi = $servisAdi;
        $this->girdiler = $girdiler;
        $this->client = new SoapClient("https://odaborsaws.tobb.org.tr/OdaBorsa?wsdl", [
            'trace' => 1,
            'exceptions' => true,
            'soap_version' => SOAP_1_1,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'location' => 'https://odaborsaws.tobb.org.tr/OdaBorsa',
        ]);
    }

    /**
     * Testler sırasında SoapClient nesnesini taklit etmek için kullanılır
     * @param SoapClient $client
     * @return void
     */
    public function setClient(SoapClient $client): void
    {
        $this->client = $client;
    }

    /**
     * Tobb üzerindeki servise istekte bulunur, dönüşü json olarak verir
     */
    public function callService()
    {
        try {
            $nonce = mt_rand();
            $created = gmdate('Y-m-d\TH:i:s\Z');

            $encodedNonce = base64_encode($nonce);

            $digestString = $nonce . $created . $this->password;
            $digest = base64_encode(sha1($digestString, true));

            $securityHeader = '
            <wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" SOAP-ENV:mustUnderstand="1">
                <wsse:UsernameToken wsu:Id="UsernameToken-1">
                    <wsse:Username>' . $this->username . '</wsse:Username>
                    <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordDigest">' . $digest . '</wsse:Password>
                    <wsse:Nonce EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary">' . $encodedNonce . '</wsse:Nonce>
                    <wsu:Created>' . $created . '</wsu:Created>
                </wsse:UsernameToken>
            </wsse:Security>';

            $headerBody = new SoapVar($securityHeader, XSD_ANYXML);
            $header = new SoapHeader('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd', 'Security', $headerBody, true);
            $this->client->__setSoapHeaders($header);

            $params = [
                'metadata' => [
                    'transactionId' => '1',
                    'odaBorsaNo' => $this->odaBorsaNo,
                    'terminalNo' => '1',
                    'istekYapanKullanici' => '1',
                    'istekZamani' => $created,
                    'istekOzeti' => 'İstekOzeti'
                ],
                'servisAdi' => $this->servisAdi,
                'versiyon' => '1.9',
                'girdiler' => $this->girdiler,
            ];

            $response = $this->client->__soapCall('servisIstegi', [$params]);
            $data = [
                'status' => true,
                'message' => "Veri getirildi",
                'data' => $response->return->donusDegeri
            ];
        } catch (SoapFault $fault) {
            $data = [
                'status' => false,
                'message' => $fault->getMessage()
            ];
        }

        header('Content-Type: application/json; charset=utf-8');
        return $data['status'] ? $data['data'] : json_encode($data);
    }
}
