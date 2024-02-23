<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasPostCode
{
  /**
   * Interact with the models's postcode.
   *
   * @return  \Illuminate\Database\Eloquent\Casts\Attribute
   */
  public function postcode() : Attribute
  {
    return Attribute::make(
      get: fn ($postcode) => $this->formatPostCode($postcode),
      set: fn ($postcode) => preg_replace("/ /", "", preg_replace("/[^[:alnum:]]/u", '', $postcode))
    );
  }

  /**
   * Post Code Formatter
   *
   * @var string $postCode
   * @return string
   */
  public function formatPostCode(string|int $postCode) : string
  {
    return vsprintf("%d%d%d%d%d-%d%d%d", str_split($postCode));
  }
}