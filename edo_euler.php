<?php require_once 'includes/EDOEuler.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Método de Euler - EDO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">Resolución de EDO con el Método de Euler</h1>

    <form method="post" class="border p-4 rounded bg-light shadow">
        <div class="mb-3">
            <label class="form-label">x₀</label>
            <input type="number" step="any" name="x0" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">y₀</label>
            <input type="number" step="any" name="y0" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">h (paso)</label>
            <input type="number" step="any" name="h" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">x final (xf)</label>
            <input type="number" step="any" name="xf" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Función f(x, y)</label>
            <input type="text" name="funcion" class="form-control" placeholder="Ej: x + y" required>
        </div>

        <button type="submit" class="btn btn-primary">Calcular</button>
    </form>

    <?php
    if ($_POST) {
        try {
            $x0 = floatval($_POST['x0']);
            $y0 = floatval($_POST['y0']);
            $h  = floatval($_POST['h']);
            $xf = floatval($_POST['xf']);
            $funcion = str_replace(['x', 'y'], ['$x', '$y'], $_POST['funcion']);

            $callback = function($x, $y) use ($funcion) {
                $resultado = null;
                eval("\$resultado = {$funcion};");
                return $resultado;
            };

            $solucion = aplicarMetodo($callback, ['x0' => $x0, 'y0' => $y0], ['h' => $h, 'xf' => $xf]);

            echo "<h4 class='mt-4'>Resultados:</h4>";
            echo "<table class='table table-bordered'><thead><tr><th>x</th><th>y</th></tr></thead><tbody>";
            foreach ($solucion as $x => $y) {
                echo "<tr><td>{$x}</td><td>{$y}</td></tr>";
            }
            echo "</tbody></table>";

        } catch (Throwable $e) {
            echo "<div class='alert alert-danger mt-3'>Error: " . $e->getMessage() . "</div>";
        }
    }
    ?>
</div>
</body>
</html>
