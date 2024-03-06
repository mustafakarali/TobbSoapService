# TOBB Soap Servisi

## Kurulum

Paketi Composer ile projenize ekleyin:

```bash
composer require karali/tobb-soap-service
````

## Kullanım
```php
require_once 'vendor/autoload.php';

$username = '...';
$password = '...';
$odaBorsaNo = '...';
$servisAdi = 'uyeKartiSorgula';
$girdiler = '{"uyeOid":"..." }';

try {
    $soapService = new Karali\TobbSoapService($username, $password, $odaBorsaNo, $servisAdi, $girdiler);
    echo $soapService->callService();
} catch (SoapFault $e) {
    echo $e->getMessage();
}
```

## Konfigürasyon

Paketin kullanımı için gerekli olan username, password, odaBorsaNo, servisAdi, ve girdiler bilgilerini sağlamanız gerekmektedir.

Paket içeriğinde bulunan Kilavuz.docx dosyasında kullanım şeklini görebilirsiniz.

### Servis adları
>odayaAitUyeleriSorgula,uyeKartiSorgula,tarihAraligindaKaydolanUyeleriSorgula,uyeDegisiklikSorgula,meslekGruplariGetir,meslekGrubuNaceDagilimlariGetir,naceSorgula,meslekGrubuUyeleriGetir,organlariSorgula,organUyeleriSorgula,kisiSorgula,kisiKartiSorgula,kisiDegisiklikSorgula,uyeTarihtekiBilgileriSorgula,tahakkukEkle,tahakkukSorgula,tahakkukIptalEt,tahsilatKaydet,TahsilatSorgula,tahsilatIptalEt,bankaKartiSorgula,kodSorgula,hesapPlaniSorgula,fisSorgula,odaBorsaKayitDegisiklikSorgula,odaBorsaKayitDegisiklikAl,odaBorsaKayitDegisiklikOnayla,odadanVerilenBelgeler,odadanVerilenHizmetler
