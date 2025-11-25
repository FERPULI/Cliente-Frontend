<?php

// Importamos la librería de Mockery para simular la BD
use Mockery as m;

test('Usuario getAll prepara la consulta SQL correcta', function () {
    // 1. PREPARACIÓN (Arrange)
    // Simulamos el "Statement" (lo que devuelve $conn->prepare)
    $stmtSimulado = m::mock(PDOStatement::class);
    // Esperamos que se ejecute el método execute() una vez y devuelva true
    $stmtSimulado->shouldReceive('execute')->once()->andReturn(true);

    // Simulamos la Conexión a Base de Datos (PDO)
    $dbSimulada = m::mock(PDO::class);
    
    // Aquí está la magia: Esperamos que prepare() sea llamado con TU consulta SQL exacta
    $sqlEsperado = "SELECT id, dni, nombres, apellidos, correo FROM usuarios";
    
    $dbSimulada->shouldReceive('prepare')
        ->with($sqlEsperado) // Verificamos que el SQL sea el correcto
        ->once()
        ->andReturn($stmtSimulado); // Devolvemos el statement simulado

    // 2. EJECUCIÓN (Act)
    // Instanciamos tu modelo pasando la BD falsa
    $usuario = new Usuario($dbSimulada);
    $resultado = $usuario->getAll();

    // 3. VERIFICACIÓN (Assert)
    // Verificamos que el resultado sea el objeto statement que simulamos
    expect($resultado)->toBe($stmtSimulado);
});

// Limpieza de Mocks después del test
afterEach(function () {
    m::close();
});