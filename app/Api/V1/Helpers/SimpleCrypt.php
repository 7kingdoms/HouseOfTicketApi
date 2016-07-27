<?php namespace App\Api\V1\Helpers;

class SimpleCrypt{
  public static function ecode($value){
    $key = env('APP_KEY');
    return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $value, MCRYPT_MODE_CBC, md5(md5($key))));
  }

  public static function decode($value){
    $key = env('APP_KEY');
    return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($value), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
  }
}
