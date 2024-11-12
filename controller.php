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
        
        // Añadir un nuevo elemento
        if ($data['action'] === 'add' && !empty($data['content'])) {
            $content = trim($data['content']);
            
            $stmt = $db->prepare("INSERT INTO $table (content) VALUES (:content)");
            $stmt->bindValue(':content', $content, PDO::PARAM_STR); // Usar bindValue con tipo de parámetro

            $stmt->execute();

            // Obtener el ID del elemento recién insertado
            $item_id = $db->lastInsertId();
            
            // Responder con el nuevo elemento
            echo json_encode([
                'action' => 'add',
                'item_id' => $item_id,
                'content' => htmlspecialchars($content) // Escapar el contenido al enviarlo de vuelta
            ]);
            exit();

        // Eliminar un elemento
        } elseif ($data['action'] === 'delete' && !empty($data['item_id'])) {
            $id = (int) $data['item_id']; // Convertir a entero para evitar inyección

            $stmt = $db->prepare("DELETE FROM $table WHERE item_id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT); // Asegurarse de que se usa como entero
            $stmt->execute();

            // Responder con la acción de eliminación
            echo json_encode([
                'action' => 'delete',
                'item_id' => $id
            ]);
            exit();

        // Editar un elemento
        } elseif ($data['action'] === 'edit' && !empty($data['item_id']) && !empty($data['updated_content'])) {
            $id = (int) $data['item_id']; // Convertir a entero para mayor seguridad
            $updated_content = trim($data['updated_content']);

            $stmt = $db->prepare("UPDATE $table SET content = :content WHERE item_id = :id");
            $stmt->bindValue(':content', $updated_content, PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Responder con el contenido actualizado
            echo json_encode([
                'action' => 'edit',
                'item_id' => $id,
                'updated_content' => htmlspecialchars($updated_content) // Escapar al enviar de vuelta
            ]);
            exit();
        }
    }

    // Obtener todos los elementos para mostrar en la lista
    $stmt = $db->prepare("SELECT item_id, content FROM $table");
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // En caso de error, mostrar mensaje y detener la ejecución
    echo "Error: " . htmlspecialchars($e->getMessage()) . "<br/>";
    die();
}
?>

