<?php
$id=$_GET['id'];
set_time_limit(1200);

$conn = new mysqli('ls-3c0c538286def4da7f8273aa5531e0b6eee0990c.cylsiewx0zgx.us-east-1.rds.amazonaws.com', 'dbmasteruser', 'eF5D;6VzP$^7qDryBzDd,`+w(5e4*qI+', 'masgps');

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Configurar la zona horaria para Santiago de Chile
date_default_timezone_set('America/Santiago');

//$id = 39;

$sql = "SELECT id, login, contraseña FROM hash where id=$id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row["id"];
       echo $login = trim($row["login"]);
       echo "<br>";
        echo $contraseña = trim($row["contraseña"]);
        echo "<br>";

        // Configuración de cURL
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://www.trackermasgps.com/api-v2/user/auth',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{"login":"' . $login . '","password":"' . $contraseña . '","dealer_id":10004282,"locale":"es","hash":null}',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json, text/plain, */*',
                'Accept-Language: es-419,es;q=0.9,en;q=0.8',
                'Connection: keep-alive',
                'Content-Type: application/json',
                'Cookie: _ga=GA1.2.728367267.1665672802; locale=es; _gid=GA1.2.967319985.1673009696; _gat=1',
                'Origin: http://www.trackermasgps.com',
                'Referer: http://www.trackermasgps.com/',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36'
            ),
        ));
        echo 
        $response = curl_exec($curl);
        curl_close($curl);

        $json = json_decode($response);

       
        // Obtener la fecha y hora actual de Santiago de Chile
        $timestamp = date("Y-m-d H:i:s");

        // Actualizar la tabla con el hash y el timestamp obtenidos
        if (isset($json->hash)) {
            $hash = $json->hash;
            $updateSql = "UPDATE hash SET hash = '$hash', timestamp = '$timestamp' WHERE id = $id";
            if ($conn->query($updateSql) === TRUE) {
                echo "Registro ID $id actualizado con éxito.<br>";
            } else {
                echo "Error actualizando el registro ID $id: " . $conn->error . "<br>";
            }
        } else {
            echo "Error al obtener hash para el registro ID $id.<br>";
        }
    }
} else {
    echo "No se encontraron registros en la tabla.<br>";
}

$conn->close();
