<?php

/**
 * Clase abstracta para resolver ecuaciones diferenciales
 */
abstract class EcuacionDiferencial
{
    protected $condicionesIniciales;
    protected $parametros;

    public function __construct($condicionesIniciales, $parametros)
    {
        $this->condicionesIniciales = $condicionesIniciales;
        $this->parametros = $parametros;
    }

    abstract public function resolverEuler();
}

/**
 * Clase que implementa el método de Euler
 */
class EulerNumerico extends EcuacionDiferencial
{
    private $funcionCallback;

    // Establece la función f(x, y) a resolver
    public function setFuncion($callback)
    {
        $this->funcionCallback = $callback;
    }

    /**
     * Método de Euler para aproximar soluciones de dy/dx = f(x, y)
     */
    public function resolverEuler()
    {
        if (!$this->funcionCallback) {
            throw new Exception("No se ha definido la función diferencial.");
        }

        $x0 = $this->condicionesIniciales['x0'];
        $y0 = $this->condicionesIniciales['y0'];
        $h  = $this->parametros['h'];     // Paso
        $xf = $this->parametros['xf'];    // Límite superior de x

        $solucion = [];
        $x = $x0;
        $y = $y0;

        $solucion[number_format($x, 6)] = $y;

        // Bucle de iteración de Euler
        while ($x < $xf) {
            $dydx = call_user_func($this->funcionCallback, $x, $y);
            $y = $y + $h * $dydx;
            $x = $x + $h;

            if ($x <= $xf) {
                $solucion[number_format($x, 6)] = $y;
            }
        }

        return $solucion;
    }
}

/**
 * Ejecuta el método de Euler con los parámetros definidos
 */
function aplicarMetodo($funcionCallback, $condicionesIniciales, $parametros)
{
    $euler = new EulerNumerico($condicionesIniciales, $parametros);
    $euler->setFuncion($funcionCallback);
    return $euler->resolverEuler();
}

/**
 * Muestra el resultado en forma de tabla
 */
function mostrarSolucion($solucion, $titulo = "Solución")
{
    echo "\n=== $titulo ===\n";
    echo sprintf("%-12s | %-12s\n", "x", "y");
    echo str_repeat("-", 27) . "\n";

    foreach ($solucion as $x => $y) {
        echo sprintf("%-12.6f | %-12.6f\n", $x, $y);
    }
}

/**
 * Permite que el usuario defina la función f(x, y) como texto
 */
function crearEcuacionPersonalizada()
{
    echo "\nDefina la ecuación diferencial dy/dx = f(x, y)\n";
    echo "Ejemplo: x + y, x * y, -y, sin(x) + cos(y)\n";
    $expresion = readline("Ingrese la función: dy/dx = ");

    // Devuelve una función anónima usando la expresión escrita por el usuario
    return function ($x, $y) use ($expresion) {
        $codigo = str_replace(['x', 'y'], [$x, $y], $expresion);
        $resultado = null;
        eval('$resultado = ' . $codigo . ';');
        return $resultado;
    };
}

// ========================== PROGRAMA PRINCIPAL ==========================


$opcion = (int)readline("Opción: ");

switch ($opcion) {
    case 1:
        echo "\n--- Resolución de ecuación diferencial ---\n";

        $callback = crearEcuacionPersonalizada();

        // Condiciones iniciales
        $x0 = (float)readline("x0 (valor inicial de x): ");
        $y0 = (float)readline("y0 (valor inicial de y): ");

        // Parámetros del método
        $h  = (float)readline("h (tamaño del paso): ");
        $xf = (float)readline("xf (valor final de x): ");

        $condicionesIniciales = ['x0' => $x0, 'y0' => $y0];
        $parametros = ['h' => $h, 'xf' => $xf];

        $solucion = aplicarMetodo($callback, $condicionesIniciales, $parametros);
        mostrarSolucion($solucion);
        break;

    case 2:
        echo "\n--- Comparación de tamaños de paso ---\n";

        $callback = crearEcuacionPersonalizada();

        $x0 = (float)readline("x0 (valor inicial de x): ");
        $y0 = (float)readline("y0 (valor inicial de y): ");
        $xf = (float)readline("xf (valor final de x): ");

        $condicionesIniciales = ['x0' => $x0, 'y0' => $y0];

        $n = (int)readline("¿Cuántos tamaños de paso desea comparar? ");

        $pasos = [];
        for ($i = 1; $i <= $n; $i++) {
            $pasos[] = (float)readline("Ingrese el paso #$i: ");
        }

        foreach ($pasos as $h) {
            echo "\n" . str_repeat("=", 40) . "\n";
            echo "Resultado para h = $h\n";

            $parametros = ['h' => $h, 'xf' => $xf];
            $solucion = aplicarMetodo($callback, $condicionesIniciales, $parametros);

            echo "Último valor: y($xf) ≈ " . number_format(end($solucion), 6) . "\n";
            if (count($solucion) <= 10) {
                mostrarSolucion($solucion);
            } else {
                echo "Mostrando algunos puntos:\n";
                $i = 0;
                $intervalo = ceil(count($solucion) / 10);
                foreach ($solucion as $x => $y) {
                    if ($i % $intervalo == 0 || $i == count($solucion) - 1) {
                        echo sprintf("%-12.6f | %-12.6f\n", $x, $y);
                    }
                    $i++;
                }
            }
        }
        break;

    default:
        
}
