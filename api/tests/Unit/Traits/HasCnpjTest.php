<?php

describe('HasCnpj Trait', function () {
  it('should format a cnpj number', function () {
    // Arrange
    $class = new class {
      use \App\Traits\HasCnpj;
    };

    // Act
    $result = $class->formatCnpj('12345678901234');

    // Assert
    expect($result)->toBe('12.345.678/9012-34');
  });

  it('should return false if an invalid cnpj number is provided', function () {
    // Arrange
    $class = new class {
      use \App\Traits\HasCnpj;
    };

    // Act
    $result = $class->validateCnpj('12345678901234');

    // Assert
    expect($result)->toBeFalse();
  });

  it('should return true if a valid cnpj number is provided', function () {
    // Arrange
    $class = new class {
      use \App\Traits\HasCnpj;
    };

    $faker = Faker\Factory::create('pt_BR');

    // Act
    $result = $class->validateCnpj($faker->cnpj(false));

    // Assert
    expect($result)->toBeTrue();
  });
});
