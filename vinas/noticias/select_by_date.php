<?php

include '../utils/db_config.php';

try {
    // Crear una conexión utilizando PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para obtener las noticias junto con la cuenta de likes
    $stmt = $conn->prepare(
       "SELECT
            n.id,
            n.titulo,
            n.resumen,
            n.contenido,
            n.fecha_publicacion,
            n.hora_publicacion,
            n.tags,
            n.status,
            n.visitas,
            c.nombre AS nombre_categoria,
            COALESCE(likes_count.likes, 0) AS likes
        FROM
            noticias n
        JOIN
            categorias c ON n.id_categoria = c.id
        LEFT JOIN (
            SELECT
                id_noticia,
                COUNT(*) AS likes
            FROM
                likes
            GROUP BY
                id_noticia
        ) likes_count ON n.id = likes_count.id_noticia
        ORDER BY
            n.fecha_publicacion DESC,
            n.hora_publicacion DESC
        LIMIT 10
    ");

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener los resultados
    $noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Responder con éxito y los datos de las noticias
    $response = ['status' => 'success', 'data' => $noticias];
    http_response_code(200);
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
