<?php

include '../utils/db_config.php';

// Crear una conexión utilizando PDO
$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Obtener datos del cuerpo de la solicitud
$data = json_decode(file_get_contents("php://input"));

// Verificar si se proporcionaron datos
if (isset($data->usuario) && isset($data->contrasenia)) {
    // Consulta preparada para evitar inyecciones SQL
    $stmt = $conn->prepare("SELECT id, id_tipo_usuario FROM usuarios WHERE usuario = :usuario AND contrasenia = :contrasenia");
    $stmt->bindParam(':usuario', $data->usuario);
    $stmt->bindParam(':contrasenia', $data->contrasenia);

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener el resultado
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $id_usuario = $user['id'];
        $id_tipo_usuario = $user['id_tipo_usuario'];

        // Obtener datos del residente
        $stmt_residente = $conn->prepare("SELECT r.id, r.nombre, r.apellido, r.telefono, r.codigo_acceso, d.calle, d.numero
                                          FROM residentes r
                                          JOIN usuarios u ON r.id_usuario = u.id
                                          INNER JOIN domicilios d ON r.id_direccion = d.id
                                             WHERE u.id = :id_usuario");
        $stmt_residente->bindParam(':id_usuario', $id_usuario);
        $stmt_residente->execute();

        if ($stmt_residente->rowCount() > 0) {
            $residente = $stmt_residente->fetch(PDO::FETCH_ASSOC);
            $response = [
                'status' => 'success',
                'id_tipo_usuario' => $id_tipo_usuario,
                'residente' => $residente
            ];
            http_response_code(200);
        } else {
            $response = ['status' => 'error', 'message' => 'No se encontró el residente'];
            http_response_code(404);
        }
    } else {
        // Credenciales inválidas
        $response = ['status' => 'error', 'message' => 'Usuario o contraseña incorrectos'];
        http_response_code(401);
    }
} else {
    // Datos insuficientes
    $response = ['status' => 'error', 'message' => 'Datos insuficientes'];
    http_response_code(400);
}

// Establecer el encabezado de tipo de contenido y devolver la respuesta
header('Content-Type: application/json');
echo json_encode($response);

// Cerrar la conexión
$conn = null;
?>
