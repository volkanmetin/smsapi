<?php

/**
 * Laravel 5 SMS Api
 * @license MIT License
 * @author Volkan Metin <ben@volkanmetin.com>
 * @link http://www.volkanmetin.com
 *
*/

return [

	'api' => [
		'1' 	=> 'SMS başarıyla gönderildi.',
		'2'		=> 'Bir alıcı girmelisiniz.',
		'3'		=> 'Alıcı dizisinde hatalı eleman var.',
		'4'		=> 'Bir SMS metni girmelisiniz.',
		'5'		=> 'SMS gönderilecek tarih formatını hatalı girdiniz. (Format: Y-m-d H:i)',
		'6'		=> 'Ayar dosyasında varsayılan SMS bağlığı boş olamaz.',
		'7'		=> 'Birden fazla alıcıya birden fazla farklı mesaj tanımlanmış. Ancak alıcı sayısıyla mesaj sayısı uyuşmuyor.',


		'20'	=> 'Geçersiz bir SMS hizmet sağlayıcı girdiniz.',
		'21'	=> 'Seçtiğiniz SMS hizmet sağlayıcı için API adresi bulunmuyor.',
		'22'	=> 'Geçersiz bir SMS başlığı(originator - senderID) girdiniz.',



		'900'	=> 'İstek işlenirken teknik bir problem meydana geldi.',
		'901'	=> 'SMS gönderilemedi. (Hizmet sağlayıcıdan dönen hata mesajı yakalanamadı)',
		'902'	=> 'SMS gönderilemedi. (Parametre kontrolünde hata mevcut @todo)',
	],


	'turacell' => [

		'default' => [
			'00' 	=> 'Sistem Hatası.',
			'20' 	=> 'Tanımsız Hata (XML formatını kontrol ediniz veya TURATEL’den destek alınız).',
			'21' 	=> 'Hatalı XML Formatı (\n - carriage return – newline vb içeriyor olabilir).',
			'22' 	=> 'Kullanıcı Aktif Değil.',
			'23' 	=> 'Kullanıcı Zaman Aşımında.',
		],


		'services' => [
			'01' 	=> 'Kullanıcı adı ya da şifre hatalı.',
			'02' 	=> 'Kredisi yeterli değil.',
			'03' 	=> 'Geçersiz içerik.',
			'04' 	=> 'Bilinmeyen SMS tipi.',
			'05' 	=> 'Hatalı gönderen ismi.',
			'06' 	=> 'Mesaj metni ya da Alıcı bilgisi girilmemiş.',
			'07' 	=> 'İçerik uzun fakat Concat özelliği ayarlanmadığından mesaj birleştirilemiyor.',
			'08' 	=> 'Kullanıcının mesaj göndereceği gateway tanımlı değil ya da şu anda çalışmıyor.',
			'09' 	=> 'Yanlış tarih formatı.Tarih ddMMyyyyhhmm formatında olmalıdır.',
		],

		'reports' => [
			'1' 	=> 'xxx.',
		],

	],
];