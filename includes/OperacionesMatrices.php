<?php

/**
 * Clase abstracta base que define operaciones comunes sobre matrices.
 */
abstract class MatrizAbstracta
{
    protected $matriz;

    public function __construct($matriz)
    {
        $this->matriz = $matriz;
    }

    // Método abstracto para multiplicar matrices
    abstract public function multiplicar($matriz);

    // Método abstracto para calcular la inversa
    abstract public function inversa();

    public function getMatriz()
    {
        return $this->matriz;
    }
}

/**
 * Clase concreta que implementa operaciones sobre matrices
 */
class Matriz extends MatrizAbstracta
{
    /**
     * Multiplica la matriz actual por otra matriz.
     */
    public function multiplicar($matriz)
    {
        $filas1 = count($this->matriz);
        $columnas1 = count($this->matriz[0]);
        $filas2 = count($matriz);
        $columnas2 = count($matriz[0]);

        if ($columnas1 !== $filas2) {
            throw new Exception("No se pueden multiplicar las matrices: dimensiones incompatibles");
        }

        $resultado = [];
        for ($i = 0; $i < $filas1; $i++) {
            for ($j = 0; $j < $columnas2; $j++) {
                $resultado[$i][$j] = 0;
                for ($k = 0; $k < $columnas1; $k++) {
                    $resultado[$i][$j] += $this->matriz[$i][$k] * $matriz[$k][$j];
                }
            }
        }

        return $resultado;
    }

    /**
     * Calcula la inversa de una matriz cuadrada usando eliminación Gauss-Jordan.
     */
    public function inversa()
    {
        $n = count($this->matriz);//Calculo de numero de filas y columnas
        // Verificar si la matriz es cuadrada

        if ($n !== count($this->matriz[0])) {
            throw new Exception("La matriz debe ser cuadrada para calcular la inversa.");
        }

        // Crear una matriz aumentada [A | I]
        // donde I es la matriz identidad de tamaño n

        $aumentada = [];
        for ($i = 0; $i < $n; $i++) {
            $aumentada[$i] = array_merge($this->matriz[$i], array_fill(0, $n, 0)); 
            $aumentada[$i][$i + $n] = 1; // añadir identidad
        }

        // Gauss-Jordan
        for ($i = 0; $i < $n; $i++) {
            $pivote = $aumentada[$i][$i];
            if (abs($pivote) < 1e-10) {
                throw new Exception("La matriz no es invertible (determinante = 0)");
            }

            for ($j = 0; $j < 2 * $n; $j++) { 
                $aumentada[$i][$j] /= $pivote;
            }

            for ($k = 0; $k < $n; $k++) {
                if ($k !== $i) {
                    $factor = $aumentada[$k][$i];
                    for ($j = 0; $j < 2 * $n; $j++) {
                        $aumentada[$k][$j] -= $factor * $aumentada[$i][$j];
                    }
                }
            }
        }

        $inversa = [];
        for ($i = 0; $i < $n; $i++) {
            $inversa[$i] = array_slice($aumentada[$i], $n);
        }

        return $inversa;
    }
}

/**
 * Calcula el determinante de una matriz cuadrada usando expansión por cofactores.
 */
function determinante($matriz)
{
    $n = count($matriz);

    if ($n !== count($matriz[0])) {
        throw new Exception("La matriz debe ser cuadrada");
    }

    if ($n === 1) return $matriz[0][0];

    if ($n === 2) return $matriz[0][0] * $matriz[1][1] - $matriz[0][1] * $matriz[1][0];

    $det = 0;
    for ($j = 0; $j < $n; $j++) {
        $submatriz = [];
        for ($i = 1; $i < $n; $i++) {
            $fila = [];
            for ($k = 0; $k < $n; $k++) {
                if ($k !== $j) $fila[] = $matriz[$i][$k];
            }
            $submatriz[] = $fila;
        }
        $det += pow(-1, $j) * $matriz[0][$j] * determinante($submatriz);
    }

    return $det;
}

/**
 * Muestra la matriz en formato bonito
 */
function mostrarMatriz($matriz, $titulo = "Matriz")
{
    echo "\n$titulo:\n";
    foreach ($matriz as $fila) {
        echo "| ";
        foreach ($fila as $valor) {
            printf("%8.3f ", $valor); //
        }
        echo "|\n";
    }
}

/**
 * Solicita al usuario que ingrese una matriz completa línea por línea.
 */
function ingresarMatrizManual($filas, $columnas, $nombre = "matriz")
{
    $matriz = [];
    echo "Ingresa los valores de la $nombre, separados por espacios:\n";
    for ($i = 0; $i < $filas; $i++) {
        echo "Fila " . ($i + 1) . ": ";
        $linea = readline();
        $valores = array_map('floatval', explode(' ', $linea));
        if (count($valores) !== $columnas) {
            echo "Error: Debes ingresar exactamente $columnas valores.\n";
            $i--;
            continue;
        }
        $matriz[] = $valores;
    }
    return $matriz;
}

// ========================== PROGRAMA PRINCIPAL ==========================

$opcion = (int)readline("Elige una opción: ");

switch ($opcion) {
    case 1:
        echo "\n--- Multiplicación de matrices ---\n";
        $filas1 = (int)readline("Filas de la primera matriz: ");
        $columnas1 = (int)readline("Columnas de la primera matriz: ");
        $matriz1 = ingresarMatrizManual($filas1, $columnas1, "primera matriz");

        $filas2 = (int)readline("Filas de la segunda matriz: ");
        $columnas2 = (int)readline("Columnas de la segunda matriz: ");
        $matriz2 = ingresarMatrizManual($filas2, $columnas2, "segunda matriz");

        $obj = new Matriz($matriz1);
        $resultado = $obj->multiplicar($matriz2);

        mostrarMatriz($matriz1, "Primera Matriz");
        mostrarMatriz($matriz2, "Segunda Matriz");
        mostrarMatriz($resultado, "Resultado");
        break;

    case 2:
        echo "\n--- Cálculo de la inversa ---\n";
        $n = (int)readline("Tamaño de la matriz cuadrada: ");
        $matriz = ingresarMatrizManual($n, $n, "matriz");

        $obj = new Matriz($matriz);
        mostrarMatriz($matriz, "Matriz original");

        $inversa = $obj->inversa();
        mostrarMatriz($inversa, "Matriz inversa");

        $verificacion = $obj->multiplicar($inversa);
        mostrarMatriz($verificacion, "Verificación (A × A⁻¹)");
        break;

    case 3:
        echo "\n--- Cálculo del determinante ---\n";
        $n = (int)readline("Tamaño de la matriz cuadrada: ");
        $matriz = ingresarMatrizManual($n, $n, "matriz");

        mostrarMatriz($matriz, "Matriz");
        $det = determinante($matriz);
        echo "Determinante: $det\n";
        break;

    default:
}
