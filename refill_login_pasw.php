<?php

include __DIR__.'/conexion.php';


// JSON con los datos de login y contraseña
$jsonData = '[
   
    {"login": "faenazaldivar@masgps.com", "passWord": "Zaldivar2023"},
    {"login": "monitoreogps@pullman.cl", "passWord": "2024_MonitoreoGPS"},
    {"login": "ti.contador@masgps.com", "passWord": "Contador24"},
    {"login": "monitoreo@ingegroup.cl", "passWord": "Monitoreo2023."},
    {"login": "Monitoreo_MVDM@masgps.cl", "passWord": "Monitoreo2023."},
    {"login": "monitoreosanclemente@masgps.com", "passWord": "Monitoreo2023."},
    {"login": "monitoreo_araucaniasur@masgps.la", "passWord": "Monitoreo_Araucania"},
    {"login": "mineraventanas@masgps.com", "passWord": "Ventanas2023"},
    {"login": "monitoreogps_lascondes@wit.la", "passWord": "MonitoreoLasCondes"},
    {"login": "mineraescondida@masgps.com", "passWord": "MEL_2023"},
    {"login": "mineracentinela@masgps.com", "passWord": "Centinela_2024"},
    {"login": "bi_pullman@masgps.com", "passWord": "BI_2024"},
    {"login": "proyecto_integral@masgps.com", "passWord": "Integral_2024"},
    {"login": "monitoreomreina@masgps.cl", "passWord": "Monitoreo2024."},
    {"login": "Monitoreo_MVDM@masgps.cl", "passWord": "Monitoreo2023."},
    {"login": "monitoreoandesmar@wit.la", "passWord": "Monitoreo2024."},
    {"login": "monitoreo_araucaniasur@masgps.la", "passWord": "Monitoreo_Araucania"},
    {"login": "monitoreo@tacoha.com", "passWord": "Monitoreo_Tacoha"},
    {"login": "monitoreotranspas@masgps.cl", "passWord": "Monitoreo2024."},
    {"login": "monitoreosanclemente@masgps.com", "passWord": "Monitoreo2024"},
    {"login": "monitoreo@masgps.py", "passWord": "Monitoreo2023."},
    {"login": "Monitoreomorales@masgps.cl", "passWord": "Monitoreo2024."},
    {"login": "Monitoreomarcel@masgps.cl", "passWord": "Monitoreo2024."},
    {"login": "Monitoreofernando@masgps.cl", "passWord": "Monitoreo2024."},
    {"login": "Monitoreoagricola@masgps.cl", "passWord": "Monitoreo2024."},
    {"login": "Monitoreorentabus@masgps.cl", "passWord": "Monitoreo2024."},
    {"login": "Monitoreosanta@masgps.cl", "passWord": "Monitoreo2024."},
    {"login": "Monitoreovillaroel@masgps.cl", "passWord": "Monitoreo2024."},
    {"login": "Monitoreotip@masgps.cl", "passWord": "Monitoreo2024."}
  ]';
  
  // Decodificar el JSON en un array asociativo
  $dataArray = json_decode($jsonData, true);
  
  // Iterar sobre cada elemento del array y actualizar la base de datos para IDs entre 2 y 32
  $id = 4;
  foreach ($dataArray as $item) {
      if ($id > 32) {
          break;
      }
  
      $login = $item['login'];
      $passWord = $item['passWord'];
  
      // Consulta SQL para actualizar la base de datos
      $sql = "UPDATE hash SET login = ?, contraseña = ? WHERE id = ?";
  
      // Preparar y ejecutar la consulta
      if ($stmt = $mysqli->prepare($sql)) {
          $stmt->bind_param("ssi", $login, $passWord, $id);
          $stmt->execute();
  
          if ($stmt->affected_rows > 0) {
              echo "Registro actualizado: ID = $id, Login = $login\n";
          } else {
              echo "No se encontró el registro o no se realizaron cambios para ID = $id\n";
          }
  
          $stmt->close();
      } else {
          echo "Error al preparar la consulta: " . $mysqli->error . "\n";
      }
  
      $id++;
  }
  
  // Cerrar la conexión
  $mysqli->close();
  ?>