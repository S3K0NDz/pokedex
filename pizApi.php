<?php
$host = 'localhost'; // Cambia esto si tu base de datos está alojada en un servidor diferente.
$user = 'id21075064_riki';
$password = 'DopperPass321,';
$database = 'id21075064_pizarras';

// Conexión a la base de datos
$connection = new mysqli($host, $user, $password, $database);

// Verificar si hay errores en la conexión
if ($connection->connect_error) {
    die('Error de conexión: ' . $connection->connect_error);
}

// Establecer encabezados para permitir el acceso desde diferentes dominios (CORS)
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");


// Verbo de solicitud HTTP (GET, POST, PUT, DELETE)
$method = $_SERVER['REQUEST_METHOD'];

// Incluir el archivo de configuración de la base de datos
include('config.php');

// Función para responder con el resultado en formato JSON
function respond($status, $data = null) {
    $response = array('status' => $status, 'data' => $data);
    echo json_encode($response);
    exit;
}

// Verificar el método HTTP y realizar la operación correspondiente
switch ($method) {
    case 'GET':
        // Obtener registros
        // Ejemplo de URL: api.php?id=1
        $id = $_GET['id'] ?? null;
        if ($id) {
            // Obtener un registro por ID
            $query = "SELECT * FROM datos WHERE id = $id";
        } else {
            // Obtener todos los registros
            $query = "SELECT * FROM datos";
        }

        $result = $connection->query($query);
        if ($result->num_rows > 0) {
            $data = array();
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            respond('success', $data);
        } else {
            respond('success', 'No se encontraron registros.');
        }
        break;

    case 'POST':
            // Obtener el JSON enviado en el cuerpo de la solicitud
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
    
            // Verificar si se recibió correctamente el JSON y contiene el campo "valor" y opcionalmente "fecha_hora" y "device"
            if ($data !== null && isset($data['valor'])) {
                $valor = $data['valor'];
                
                // Verificar si se proporcionó la fecha, si no, utilizar la fecha y hora actual del servidor
                $fecha_hora = isset($data['fecha_hora']) ? $data['fecha_hora'] : date('Y-m-d H:i:s');
    
                // Verificar si se proporcionó el campo "device", si no, utilizar un valor predeterminado o dejarlo vacío según tus necesidades
                $device = isset($data['device']) ? $data['device'] : '';
    
                // Insertar el registro en la base de datos
                $query = "INSERT INTO datos (valor, fecha_hora, device) VALUES ('$valor', '$fecha_hora', '$device')";
                if ($connection->query($query) === true) {
                    respond('success', 'Registro creado correctamente.');
                } else {
                    respond('error', 'Error al crear el registro: ' . $connection->error);
                }
            } else {
                respond('error', 'Datos incorrectos o incompletos en el JSON.');
            }
            break;

    case 'PUT':
                // Obtener el JSON enviado en el cuerpo de la solicitud
                $json = file_get_contents('php://input');
                $data = json_decode($json, true);
        
                // Verificar si se recibió correctamente el JSON y contiene los campos requeridos
                if ($data !== null && isset($data['id']) && isset($data['valor']) && isset($data['fecha_hora']) && isset($data['device'])) {
                    $id = $data['id'];
                    $valor = $data['valor'];
                    $fecha_hora = $data['fecha_hora'];
                    $device = $data['device'];
        
                    // Actualizar el registro en la base de datos
                    $query = "UPDATE datos SET valor='$valor', fecha_hora='$fecha_hora', device='$device' WHERE id = $id";
                    if ($connection->query($query) === true) {
                        respond('success', 'Registro actualizado correctamente.');
                    } else {
                        respond('error', 'Error al actualizar el registro: ' . $connection->error);
                    }
                } else {
                    respond('error', 'Datos incorrectos o incompletos en el JSON.');
                }
                
    

    case 'DELETE':
        // Obtener el JSON enviado en el cpoaerpo de la solicitud
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        // Verificar si se recibió correctamente el JSON y contiene el campo "id"
        if ($data !== null && isset($data['id'])) {
            $id = $data['id'];

            // Eliminar el registro de la base de datos
            $query = "DELETE FROM datos WHERE id = $id";
            if ($connection->query($query) === true) {
                respond('success', 'Registro eliminado correctamente.');
            } else {
                respond('error', 'Error al eliminar el registro: ' . $connection->error);
            }
        } else {
            respond('error', 'Datos incorrectos o incompletos en el JSON.');
        }
        break;

    default:
        respond('error', 'Método no permitido.');
}

