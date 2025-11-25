<?php

test('suma dos numeros correctamente', function () {
    $resultado = 2 + 2;

    // Esperamos que el resultado sea 4
    expect($resultado)->toBe(4);
});

test('verifica que un texto no este vacio', function () {
    $texto = 'Hola Mundo';

    // Esperamos que sea un string y no esté vacío
    expect($texto)->toBeString()->not->toBeEmpty();
});
