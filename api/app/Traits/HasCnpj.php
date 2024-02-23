<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasCnpj
{
  /**
   * Interact with the models's cnpj.
   *
   * @return  \Illuminate\Database\Eloquent\Casts\Attribute
   */
  public function cnpj() : Attribute
  {
    return Attribute::make(
      get: fn ($cnpj) => $this->formatCnpj($cnpj),
      set: fn ($cnpj) => preg_replace("/ /", "", preg_replace("/[^[:alnum:]]/u", '', $cnpj))
    );
  }

  /**
   * CNPJ Formatter
   *
   * @var string $cnpj
   * @return string
   */
  public function formatCnpj($cnpj) : string
  {
    return vsprintf("%d%d.%d%d%d.%d%d%d/%d%d%d%d-%d%d", str_split($cnpj));
  }

  /**
   * CNPJ Validator
   * 
   * @var string $cnpj
   * @return bool
   */
  public function validateCnpj($cnpj) : bool
  {
    $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);

    if (strlen($cnpj) != 14)
      return false;

    if (preg_match('/(\d)\1{13}/', $cnpj))
      return false;

    for ($i = 0, $j = 5, $sum = 0; $i < 12; $i++) {
      $sum += $cnpj[$i] * $j;
      $j = ($j == 2) ? 9 : $j - 1;
    }

    $rest = $sum % 11;

    if ($cnpj[12] != ($rest < 2 ? 0 : 11 - $rest))
      return false;

    for ($i = 0, $j = 6, $sum = 0; $i < 13; $i++) {
      $sum += $cnpj[$i] * $j;
      $j = ($j == 2) ? 9 : $j - 1;
    }

    $rest = $sum % 11;

    return $cnpj[13] == ($rest < 2 ? 0 : 11 - $rest);
  }
}