<?php

header('Content-Type: application/json');

exec("for /f \"tokens=5\" %a in ('netstat -ano ^| findstr :3000') do taskkill /PID %a /F");

echo json_encode([
"estado"=>"detenido"
]);

?>