Laravel 5 için SMS Paketi
=========

[![Latest Stable Version](https://poser.pugx.org/volkanmetin/smsapi/v/stable.svg)](https://packagist.org/packages/volkanmetin/smsapi) [![Total Downloads](https://poser.pugx.org/volkanmetin/smsapi/downloads.svg)](https://packagist.org/packages/volkanmetin/smsapi) [![Latest Unstable Version](https://poser.pugx.org/volkanmetin/smsapi/v/unstable.svg)](https://packagist.org/packages/volkanmetin/smsapi) [![License](https://poser.pugx.org/volkanmetin/smsapi/license.svg)](https://packagist.org/packages/volkanmetin/smsapi)

Bu paket sayesinde Laravel 5.x kullanılan projelerinizde tekli veya çoklu sms gönderebilir, bakiye ve originator sorgulayabilirsiniz. 

Uyarı, hata ve bilgilendirme için Türkçe dillerinde uyarı ve bilgi mesajlarını barındırır.


Kurulum
-----------

* Öncelikle `composer.json` dosyanızdaki `require` kısmına aşağıdaki değeri ekleyin:

    ```json
    "volkanmetin/smsapi": "~1"
    ```

    Alternatif olarak `composer require volkanmetin/smsapi:~1` komutu ile de paketi ekleyebilirsiniz.
* Ardından composer paketlerinizi güncellemelisiniz. `composer update` komutu ile bunu yapabilirsiniz.
* Şimdi de `app/config/app.php` dosyasını açın, `providers` içine en alta şunu girin:

    ```php
    'Volkanmetin\Smsapi\SmsapiServiceProvider',
    ```
* Şimdi yine aynı dosyada `aliases` altına şu değeri girin:

    ```php
    'Smsapi' => 'Volkanmetin\Smsapi\Facades\Smsapi',
    ```
* Şimdi de environment'ınıza konfigürasyon dosyasını paylaşmalısınız. Bunun için aşağıdaki komutu çalıştırın:

    ```shell
    php artisan vendor:publish
    ```
* `app/config/smsapi.php` dosyası paylaşılacak. Burada smsapi için size atanan kullanıcı adı, parola ve originator (sender_id) değerlerini girmelisiniz.

Kullanım
-------------

####Birine o anda tekil SMS göndermek için:

```php
$send = Smsapi::send('05355469076', 'Merhaba');
echo $send->last_message;
```

####SMS gönderildi mi ?

```php
$send = Smsapi::send('05355469076', 'Merhaba');
if($send) {
    echo 'SMS başarı ile gönderildi!';
} else {
    echo $send->last_message;
}
```

####Birden fazla kişiye aynı anda aynı SMS'i göndermek için:

```php
$kisiler = array('00905355469076', '+905355469076', '05355469076', '5355469076');
$send = Smsapi::send($kisiler, 'Merhaba');
echo $send->last_message;
```

Veya 

```php
$send = Smsapi::send('00905355469076', '+905355469076', '05355469076', '5355469076', 'Merhaba');
echo $send->last_message;
```

####Kalan Kontör Sorgulaması için:

```php
echo Smsapi::checkBalance();
```

####Originatörleri listelemek için:

```php
echo Smsapi::listOriginators();
```

#### Gelecek bir tarihe SMS yollamak için:

```php
echo Smsapi::send('05355469076', 'Geç gidecek mesaj', '2099-06-30 15:00'); //saniye yok, dikkat!
```

#### Farklı bir Originatör (Sender ID) kullanarak SMS yollamak için:

```php
echo Smsapi::send('05355469076', 'merhaba', '', 'diğerOriginator');
```

Notlar
----
Oldukça geliştirilmesi gerek.

Lisans
----

Bu yazılım paketi MIT lisansı ile lisanslanmıştır.
