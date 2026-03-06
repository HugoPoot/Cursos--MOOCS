<?php

header('Content-Type: application/json');

// Verificar si ya está activo
$conexion = @fsockopen("localhost",3000);

if($conexion){
    fclose($conexion);

    echo json_encode([
        "estado"=>"activo",
        "mensaje"=>"Servidor ya activo"
    ]);
    exit;
}

// Rutas
$node = "C:\\Program Files\\nodejs\\node.exe";
$server = "C:\\xampp\\htdocs\\Servicio\\server3.js";
$log = "C:\\xampp\\htdocs\\Servicio\\node_log.txt";

// Ejecutar Node en segundo plano
$comando = "cmd /c start /B \"nodeServer\" \"$node\" \"$server\" > \"$log\" 2>&1";

pclose(popen($comando,"r"));

sleep(3);

// Verificar si inició
$conexion = @fsockopen("localhost",3000);

if($conexion){

    fclose($conexion);

    echo json_encode([
        "estado"=>"iniciado",
        "mensaje"=>"Servidor iniciado correctamente"
    ]);

}else{

    echo json_encode([
        "estado"=>"error",
        "mensaje"=>"No se pudo iniciar el servidor"
    ]);

}

?>