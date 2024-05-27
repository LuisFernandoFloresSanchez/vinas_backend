<?php

// conexión a la base de datos
$servername = "localhost";
$username = "u469255317_admin";
$password = "LuiEdr1234.";
$dbname = "u469255317_vinas";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Función para ejecutar consultas y manejar errores
function executeQuery($query) {
    global $conn;
    $result = $conn->query($query);
    if (!$result) {
        die("Error al ejecutar la consulta: " . $conn->error);
    }
    return $result;
}

// Cerrar la conexión (opcional)
 $conn->close();
?>