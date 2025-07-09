<?php require_once 'includes/OperacionesMatrices.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Operaciones con Matrices</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">Operaciones con Matrices</h1>

    <form method="post" class="border p-4 shadow rounded bg-light">
        <div class="mb-3">
            <label for="operacion" class="form-label">Operación</label>
            <select name="operacion" id="operacion" class="form-select">
                <option value="suma">Suma</option>
                <option value="resta">Resta</option>
                <option value="multiplicacion">Multiplicación</option>
                <option value="inversa">Inversa (solo A)</option>
                <option value="determinante">Determinante (solo A)</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Matriz A</label>
            <textarea name="matrizA" class="form-control" placeholder="Ej: 1,2;3,4" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Matriz B (si aplica)</label>
            <textarea name="matrizB" class="form-control" placeholder="Ej: 5,6;7,8"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Calcular</button>
    </form>

    <?php
    if ($_POST) {
        try {
            $op = $_POST['operacion'];
            $matrizA = array_map('str_getcsv', explode(';', trim($_POST['matrizA'])));
            $matrizB = isset($_POST['matrizB']) && $_POST['matrizB'] ? array_map('str_getcsv', explode(';', trim($_POST['matrizB']))) : null;

            $matrizAObj = new Matriz($matrizA);

            switch ($op) {
                case 'suma':
                    if (!$matrizB) throw new Exception("Se requiere Matriz B para la suma.");
                    $matrizBObj = new Matriz($matrizB);
                    $resultado = [];
                    for ($i = 0; $i < count($matrizA); $i++) {
                        for ($j = 0; $j < count($matrizA[0]); $j++) {
                            $resultado[$i][$j] = $matrizA[$i][$j] + $matrizB[$i][$j];
                        }
                    }
                    break;

                case 'resta':
                    if (!$matrizB) throw new Exception("Se requiere Matriz B para la resta.");
                    $matrizBObj = new Matriz($matrizB);
                    $resultado = [];
                    for ($i = 0; $i < count($matrizA); $i++) {
                        for ($j = 0; $j < count($matrizA[0]); $j++) {
                            $resultado[$i][$j] = $matrizA[$i][$j] - $matrizB[$i][$j];
                        }
                    }
                    break;

                case 'multiplicacion':
                    if (!$matrizB) throw new Exception("Se requiere Matriz B para la multiplicación.");
                    $resultado = $matrizAObj->multiplicar($matrizB);
                    break;

                case 'inversa':
                    $resultado = $matrizAObj->inversa();
                    break;

                case 'determinante':
                    $n = count($matrizA);
                    if ($n != count($matrizA[0])) throw new Exception("La matriz debe ser cuadrada.");
                    // Determinante recursivo
                    function determinante($m) {
                        $n = count($m);
                        if ($n == 1) return $m[0][0];
                        if ($n == 2) return $m[0][0] * $m[1][1] - $m[0][1] * $m[1][0];

                        $det = 0;
                        for ($c = 0; $c < $n; $c++) {
                            $sub = [];
                            for ($i = 1; $i < $n; $i++) {
                                $subfila = [];
                                for ($j = 0; $j < $n; $j++) {
                                    if ($j != $c) $subfila[] = $m[$i][$j];
                                }
                                $sub[] = $subfila;
                            }
                            $det += pow(-1, $c) * $m[0][$c] * determinante($sub);
                        }
                        return $det;
                    }

                    $resultado = determinante($matrizA);
                    break;

                default:
                    throw new Exception("Operación no válida");
            }

            echo "<h4 class='mt-4'>Resultado:</h4><pre class='bg-white p-3 border rounded'>";
            print_r($resultado);
            echo "</pre>";

        } catch (Exception $e) {
            echo "<div class='alert alert-danger mt-3'>{$e->getMessage()}</div>";
        }
    }
    ?>
</div>
</body>
</html>
