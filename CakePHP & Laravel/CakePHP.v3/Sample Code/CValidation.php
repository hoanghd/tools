<?php
namespace App\Model\Validation;

use Cake\Validation\Validation;
class CValidation extends Validation
{
  public static function phone($check)
  {
    return (bool)preg_match('/^(0([1-9]{1}-[1-9]\d{3}|[1-9]{2}-\d{3}|[1-9]{2}\d{1}-\d{2}|[1-9]{2}\d{2}-\d{1})-\d{4}|0[789]0-\d{4}-\d{4}|050-\d{4}-\d{4})$/', (string)$check);
  }
}

/**
    $validator->provider('fa', 'App\Model\Validation\CValidation');
    $validator->add('tel', 'valid-phone', [
        'rule' => 'phone',
        'provider' => 'fa',
        'message'=>__('valid.not_correctly', '代表電話番号')
    ]); 
 */