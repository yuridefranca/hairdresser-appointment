<?php

describe('should format the postcode', function () {
    it('should format the postcode number', function () {
        // Arrange
        $class = new class {
            use \App\Traits\HasPostCode;
        };

        // Act
        $result = $class->formatPostCode('12345678');

        // Assert
        expect($result)->toBe('12345-678');
    });
});