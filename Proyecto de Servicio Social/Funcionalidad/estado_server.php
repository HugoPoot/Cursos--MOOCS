<?php

header('Content-Type: application/json');

$conexion=@fsockopen("localhost",3000);

if($conexion){

fclose($conexion);

echo json_encode([
"estado"=>"activo"
]);

}else{

echo json_encode([
"estado"=>"inactivo"
]);

}

?>