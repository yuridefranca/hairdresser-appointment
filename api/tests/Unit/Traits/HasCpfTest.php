<?php

describe('HasCpf Trait', function () {
  it('should format a CPF number', function () {
    // Arrange
    $class = new class {
      use \App\Traits\HasCpf;
    };

    // Act
    $result = $class->formatCPF('12345678901');

    // Assert
    expect($result)->toBe('123.456.789-01');
  });

  it('should return false if an invalid CPF number is provided', function () {
    // Arrange
    $class = new class {
      use \App\Traits\HasCpf;
    };

    // Act
    $result = $class->validateCPF('12345678901');

    // Assert
    expect($result)->toBeFalse();
  });

  it('should return true if a valid CPF number is provided', function () {
    // Arrange
    $class = new class {
      use \App\Traits\HasCpf;
    };

    $faker = Faker\Factory::create('pt_BR');

    // Act
    $result = $class->validateCPF($faker->cpf(false));

    // Assert
    expect($result)->toBeTrue();
  });
});
