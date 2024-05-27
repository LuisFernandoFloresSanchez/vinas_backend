<?php

include '../utils/db_config.php';

try {
    // Crear una conexión utilizando PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener datos del cuerpo de la solicitud
    $data = json_decode(file_get_contents("php://input"));

    // Verificar si se proporcionaron datos
    if (isset($data->id_usuario) && isset($data->id_noticia)) {
        // Consulta preparada para eliminar el like
        $stmt = $conn->prepare("DELETE FROM likes WHERE id_usuario = :id_usuario AND id_noticia = :id_noticia");
        $stmt->bindParam(':id_usuario', $data->id_usuario);
        $stmt->bindParam(':id_noticia', $data->id_noticia);

        // Ejecutar la consulta
        $stmt->execute();

        // Verificar si se eliminó algún registro
        if ($stmt->rowCount() > 0) {
            $response = ['status' => 'success', 'message' => 'Like eliminado exitosamente'];
            http_response_code(200);
        } else {
            $response = ['status' => 'error', 'message' => 'No se encontró el like para eliminar'];
            http_response_code(404);
        }
    } else {
        // Datos insuficientes
        $response = ['status' => 'error', 'message' => 'Datos insuficientes'];
        http_response_code(400);
    }
} catch (PDOException $e) {
    // Manejo de errores de base de datos
    $response = ['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()];
    http_response_code(500);
}

// Establecer el encabezado de tipo de contenido y devolver la respuesta
header('Content-Type: application/json');
echo json_encode($response);

// Cerrar la conexión
$conn = null;

?>
