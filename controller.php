<?php
// Incluir el archivo de contraseñas
require 'contra.php';

// Nombre de la tabla
$table = "todo_list";

try {
    // Conexión a la base de datos usando PDO con las variables importadas de contra.php
    $db = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Leer el contenido de la solicitud
    $data = json_decode(file_get_contents('php://input'), true);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['action'])) {
        if ($data['action'] === 'add' && !empty($data['content'])) {
            // Insertar un nuevo elemento
            $content = $data['content'];
            $stmt = $db->prepare("INSERT INTO $table (content) VALUES (:content)");
            $stmt->bindParam(':content', $content);
            $stmt->execute();

            // Obtener el ID del elemento recién insertado
            $item_id = $db->lastInsertId();
            
            // Responder con el nuevo elemento
            echo json_encode([
                'action' => 'add',
                'item_id' => $item_id,
                'content' => $content
            ]);
            exit();
        } elseif ($data['action'] === 'delete' && !empty($data['item_id'])) {
            // Eliminar un elemento
            $id = $data['item_id'];
            $stmt = $db->prepare("DELETE FROM $table WHERE item_id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // Responder con la acción de eliminación
            echo json_encode([
                'action' => 'delete',
                'item_id' => $id
            ]);
            exit();
        } elseif ($data['action'] === 'edit' && !empty($data['item_id']) && !empty($data['updated_content'])) {
            // Editar un elemento
            $id = $data['item_id'];
            $updated_content = $data['updated_content'];
            $stmt = $db->prepare("UPDATE $table SET content = :content WHERE item_id = :id");
            $stmt->bindParam(':content', $updated_content);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // Responder con el contenido actualizado
            echo json_encode([
                'action' => 'edit',
                'item_id' => $id,
                'updated_content' => $updated_content
            ]);
            exit();
        }
    }

    // Obtener todos los elementos para mostrar en la lista (sin redirigir)
    $stmt = $db->prepare("SELECT item_id, content FROM $table");
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // En caso de error, mostrar mensaje y detener la ejecución
    echo "Error: " . $e->getMessage() . "<br/>";
    die();
}
?>
