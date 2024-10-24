<?php
// Incluir el archivo de contraseñas
require 'contra.php';

// Nombre de la tabla
$table = "todo_list";

try {
    // Conexión a la base de datos usando PDO con las variables importadas
    $db = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insertar un nuevo elemento si se ha enviado el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['content'])) {
        $content = $_POST['content'];
        $stmt = $db->prepare("INSERT INTO $table (content) VALUES (:content)");
        $stmt->bindParam(':content', $content);
        $stmt->execute();
        echo "<p>Elemento añadido con éxito.</p>";
    }

} catch (PDOException $e) {
    // En caso de error, mostrar mensaje
    echo "Error: " . $e->getMessage() . "<br/>";
    die();
}
?>
