<?php
require "conexion.php";

  $id_elemento= $_GET['correo'];
    // Consulta para eliminar el registro
    $consulta = "DELETE FROM datos2 WHERE correo = '$id_elemento'";
    $resultado = mysqli_query($conectar, $consulta); // Corrección: Usar la variable $consulta

    // Verificar si la consulta fue exitosa
    if ($resultado) {
        echo "
        <script>
            alert('Registro eliminado correctamente.');
            location.href='vercursos.php';
        </script>";
    } 
// Cerrar la conexión (opcional, pero recomendado)
//mysqli_close($conectar);
?>