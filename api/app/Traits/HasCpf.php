<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasCpf
{
  /**
   * Interact with the models's cpf.
   *
   * @return  \Illuminate\Database\Eloquent\Casts\Attribute
   */
  public function cpf() : Attribute
  {
    return Attribute::make(
      get: fn ($cpf) => $this->formatCpf($cpf),
      set: fn ($cpf) => preg_replace("/ /", "", preg_replace("/[^[:alnum:]]/u", '', $cpf))
    );
  }

  /**
   * CPF Formatter
   *
   * @var string $cpf
   * @return string
   */
  public function formatCpf($cpf) : string
  {
    return vsprintf("%d%d%d.%d%d%d.%d%d%d-%d%d", str_split($cpf));
  }

  /**
   * CPF Validator
   * 
   * @var string $cpf
   * @return bool
   */
  public function validateCpf($cpf) : bool
  {
    $cpf = str_pad(preg_replace('/[^0-9]/', '', $cpf), 11, '0', STR_PAD_LEFT);

    if (strlen($cpf) != 11) {
      return false;
    }

    if (preg_match('/(\d)\1{10}/', $cpf)) {
      return false;
    }

    for ($t = 9; $t < 11; $t++) {
      for ($d = 0, $c = 0; $c < $t; $c++) {
        $d += $cpf[$c] * (($t + 1) - $c);
      }

      $d = ((10 * $d) % 11) % 10;

      if ($cpf[$c] != $d) {
        return false;
      }
    }

    return true;
  }
}