<?php
// Incluir el archivo del controlador
require 'controller.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TODO List - Actualizable sin recargar</title>
</head>
<body>

    <h3>Añadir nuevo elemento a la lista</h3>
    <label for="content">Elemento:</label>
    <input type="text" id="content" placeholder="Ingresa una tarea">
    <button id="guardar">Añadir</button>

    <h2>TODO List</h2>
    <ul id="lista">
        <?php foreach ($items as $item): ?>
            <li id="item-<?php echo $item['item_id']; ?>">
                <span><?php echo htmlspecialchars($item['content']); ?></span>
                <button onclick="borrar(<?php echo $item['item_id']; ?>)">X</button>
                <input type="text" id="modificacion-<?php echo $item['item_id']; ?>" placeholder="Editar aquí">
                <button onclick="modificar(<?php echo $item['item_id']; ?>)">Modificar</button>
            </li>
        <?php endforeach; ?>
    </ul>

    <script>
        function llamada_a_controller(metodo, postData) {
            const url = 'controller.php'; 

            fetch(url, {
                method: metodo,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(postData)
            })
            .then(response => response.json())  // Convertir la respuesta a JSON
            .then(data => {
                if (data.action === 'delete') {
                    document.getElementById(`item-${data.item_id}`).remove();
                } else if (data.action === 'add') {
                    var li = document.createElement("li");
                    li.id = `item-${data.item_id}`;
                    li.innerHTML = `
                        <span>${data.content}</span>
                        <button onclick="borrar(${data.item_id})">X</button>
                        <input type="text" id="modificacion-${data.item_id}" placeholder="Editar aquí">
                        <button onclick="modificar(${data.item_id})">Modificar</button>
                    `;
                    document.getElementById('lista').appendChild(li);
                } else if (data.action === 'edit') {
                    document.querySelector(`#item-${data.item_id} span`).textContent = data.updated_content;
                }
            })
            .catch(error => console.error('Error en la solicitud:', error));
        }

        function borrar(item_id) {
            const postData = {
                action: 'delete',
                item_id: item_id
            };
            llamada_a_controller("POST", postData);
        }

        function modificar(item_id) {
            const updated_content = document.getElementById(`modificacion-${item_id}`).value;
            if (!updated_content) {
                alert('Por favor, introduce un valor para modificar.');
                return;
            }

            const postData = {
                action: 'edit',
                item_id: item_id,
                updated_content: updated_content
            };
            llamada_a_controller("POST", postData);
        }

        document.getElementById('guardar').addEventListener('click', function () {
            const contenido = document.getElementById('content').value;
            if (!contenido) {
                alert('Por favor, introduce un valor.');
                return;
            }

            const postData = {
                action: 'add',
                content: contenido
            };
            llamada_a_controller("POST", postData);
        });
    </script>

</body>
</html>
