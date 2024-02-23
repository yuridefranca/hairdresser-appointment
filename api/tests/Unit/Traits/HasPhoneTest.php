<?php

describe('HasPhone Trait', function () {
  it('should format the phone number (9 characters)', function () {
    // Arrange
    $class = new class {
      use \App\Traits\HasPhone;
    };

    // Act
    $result = $class->formatPhone('11987654321');

    // Assert
    expect($result)->toBe('(11) 98765-4321');
  });

  it('should format the phone number (8 characters)', function () {
    // Arrange
    $class = new class {
      use \App\Traits\HasPhone;
    };

    // Act
    $result = $class->formatPhone('1187654321');

    // Assert
    expect($result)->toBe('(11) 8765-4321');
  });

  it('should format the phone number (json)', function () {
    // Arrange
    $class = new class {
      use \App\Traits\HasPhone;
    };

    // Act
    $result = $class->formatPhone(json_encode(['11987654321', '1187654321']), true);

    // Assert
    expect($result)->toBe(['(11) 98765-4321', '(11) 8765-4321']);
  });
});
