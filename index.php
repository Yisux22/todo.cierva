<?php
// Incluir el archivo de controllador
require 'Controller.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TODO List</title>
</head>
<body>

    <h3>Añadir nuevo elemento a la lista</h3>
    <form method="POST" action="">
        <label for="content">Elemento:</label>
        <input type="text" id="content" name="content" required>
        <input type="submit" value="Añadir">
    </form>

    <?php
    // Mostrar la lista de elementos después del formulario
    echo "<h2>TODO List</h2><ul>";
    foreach($db->query("SELECT * FROM $table") as $row) {
        echo "<li>" . $row['item_id'] . ". " . htmlspecialchars($row['content']) . "</li>";
    }
    echo "</ul>";
    ?>

</body>
</html>
