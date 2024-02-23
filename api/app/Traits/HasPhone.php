<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasPhone
{
  /**
   * Interact with the models's phone.
   *
   * @return  \Illuminate\Database\Eloquent\Casts\Attribute
   */
  public function phone() : Attribute
  {
    return Attribute::make(
      get: fn ($phone) => $this->formatPhone($phone),
      set: fn ($phone) => preg_replace("/ /", "", preg_replace("/[^[:alnum:]]/u", '', $phone))
    );
  }

  /**
   * Interact with the models's phones.
   *
   * @return  \Illuminate\Database\Eloquent\Casts\Attribute
   */
  public function phones() : Attribute
  {
    return Attribute::make(
      get: fn ($phones) => $this->formatPhone($phones, true),
      set: fn ($phones) => [
        'phones' => json_encode(array_map(function ($phone) {
          if ($phone)
            return preg_replace("/ /", "", preg_replace("/[^[:alnum:]]/u", '', $phone));
        }, $phones))
      ]
    );
  }

  /**
   * Phone Formatter
   *
   * @var string $phones
   * @var bool $json
   *
   * @return string
   */
  public function formatPhone($phones, $json = false) : string|array
  {
    if ($json) {
      $arrayPhones = array_filter(json_decode($phones));

      $formatedPhones = array_map(function ($phone) {
        if (strlen($phone) === 11) {
          return vsprintf("(%d%d) %d%d%d%d%d-%d%d%d%d", str_split($phone));
        }

        return vsprintf("(%d%d) %d%d%d%d-%d%d%d%d", str_split($phone));
      }, $arrayPhones);

      return (array) $formatedPhones;
    }

    if (strlen($phones) === 11) {
      return vsprintf("(%d%d) %d%d%d%d%d-%d%d%d%d", str_split($phones));
    }

    return vsprintf("(%d%d) %d%d%d%d-%d%d%d%d", str_split($phones));
  }
}