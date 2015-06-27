<?php

/**
 * Laravel 5 SMS Api
 * @license MIT License
 * @author Volkan Metin <ben@volkanmetin.com>
 * @link http://www.volkanmetin.com
 *
*/

return [

    //Queue service
    'queue'             => false,

    //Convert Turkish chars to English
    'convert_tr'        => false,

    //Active Sms provider (For now, suport turacell only!)
    'sms_provider'      => 'turacell',

    // allow react character limit
    'concat'        => true, 


    /*
        Turacell Hesap Bilgileri
    */
    'turacell' => [
        'username'      => 'xxx',
        'password'      => 'xxx',
        'channel_code'  => 'xxx',
        'platform_id'   => 1,
        'originator'    => 'xxx', // default sender name | must be filled
    ],

];