<?php
set_time_limit(1200);

$conn = $conn = new mysqli('ls-3c0c538286def4da7f8273aa5531e0b6eee0990c.cylsiewx0zgx.us-east-1.rds.amazonaws.com','dbmasteruser','eF5D;6VzP$^7qDryBzDd,`+w(5e4*qI+','masgps');

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Configurar la zona horaria para Santiago de Chile
date_default_timezone_set('America/Santiago');

// Consulta para obtener login y contraseña
$sql = "SELECT id, login, contraseña FROM hash";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $multiCurl = array();
    $mh = curl_multi_init();
    $counter = 0;

    while ($row = $result->fetch_assoc()) {
        $id = $row["id"];
        $login = $row["login"];
        $contraseña = $row["contraseña"];

        // Configuración de cURL individual
        $multiCurl[$id] = curl_init();
        curl_setopt_array($multiCurl[$id], array(
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

        curl_multi_add_handle($mh, $multiCurl[$id]);
        $counter++;

        // Si se han agregado 5 solicitudes, ejecutarlas en paralelo
        if ($counter == 5) {
            $running = null;
            do {
                curl_multi_exec($mh, $running);
                curl_multi_select($mh);
            } while ($running > 0);

            // Procesar las respuestas
            foreach ($multiCurl as $id => $ch) {
                $response = curl_multi_getcontent($ch);
                $json = json_decode($response);
                $hash = $json->hash;

                // Obtener la fecha y hora actual de Santiago de Chile
                $timestamp = date("Y-m-d H:i:s");

                // Actualizar la tabla con el hash y el timestamp obtenidos
                if ($hash) {
                    $updateSql = "UPDATE hash SET hash = '$hash', timestamp = '$timestamp' WHERE id = $id";
                    if ($conn->query($updateSql) === TRUE) {
                        echo "Registro ID $id actualizado con éxito.<br>";
                    } else {
                        echo "Error actualizando el registro ID $id: " . $conn->error . "<br>";
                    }
                } else {
                    echo "Error al obtener hash para el registro ID $id.<br>";
                }

                // Cerrar cada handle individual
                curl_multi_remove_handle($mh, $ch);
                curl_close($ch);
            }

            // Reiniciar el array y el contador para el siguiente lote
            $multiCurl = array();
            $counter = 0;
        }
    }

    // Procesar cualquier solicitud restante después del último lote de 5
    if ($counter > 0) {
        $running = null;
        do {
            curl_multi_exec($mh, $running);
            curl_multi_select($mh);
        } while ($running > 0);

        foreach ($multiCurl as $id => $ch) {
            $response = curl_multi_getcontent($ch);
            $json = json_decode($response);
            $hash = $json->hash;

            // Obtener la fecha y hora actual de Santiago de Chile
            $timestamp = date("Y-m-d H:i:s");

            // Actualizar la tabla con el hash y el timestamp obtenidos
            if ($hash) {
                $updateSql = "UPDATE hash SET hash = '$hash', timestamp = '$timestamp' WHERE id = $id";
                if ($conn->query($updateSql) === TRUE) {
                    echo "Registro ID $id actualizado con éxito.<br>";
                } else {
                    echo "Error actualizando el registro ID $id: " . $conn->error . "<br>";
                }
            } else {
                echo "Error al obtener hash para el registro ID $id.<br>";
            }

            // Cerrar cada handle individual
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);
        }
    }

    // Cerrar el multi handle
    curl_multi_close($mh);
} else {
    echo "No se encontraron registros en la tabla.<br>";
}

$conn->close();
?>
