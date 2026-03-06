<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Ver Datos</title>

    <link rel="stylesheet" href="estilos.css?v=2">

    <!-- Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <!-- PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>

    <style>

        /* ===== HEADER ===== */

        .hero{
            display:flex;
            align-items:center;
            justify-content:center;
            gap:60px;
            padding:20px 40px;
        }

        /* LOGOS */

        .logo{
            background:white;
            padding:10px 18px;
            border-radius:15px;
            box-shadow:0 3px 8px rgba(0,0,0,0.15);
            display:flex;
            align-items:center;
            justify-content:center;
        }

        .logo img{
            height:65px;
            object-fit:contain;
        }

        .hero .ancho{
            text-align:center;
            max-width:600px;
        }

    </style>

</head>

<body>

<?php

    require "conexion.php";

    if (!$conectar) {
        die("<p>Error al conectar a la base de datos.</p>");
    }

?>


<!-- HEADER CON LOGOS -->

<header class="hero">

    <div class="logo">
        <img src="imagenes/ITM.svg" alt="Logo ITM">
    </div>

    <div class="ancho">
        <h2>
            <span class="highlight">DATOS DE LOS CURSOS</span>
        </h2>
    </div>

    <div class="logo">
        <img src="imagenes/TECNM.png" alt="Logo TECNM">
    </div>

</header>


<!-- BOTONES -->

<div class="ancho centrado">

    <a href="inicial.php" class="btn">
        Volver al inicio
    </a>

    <button onclick="imprimirTabla()" class="btn">
        Imprimir
    </button>

    <button onclick="exportarPDF()" class="btn">
        Exportar PDF
    </button>

    <button onclick="exportarExcel()" class="btn">
        Exportar Excel
    </button>

</div>


<!-- TABLA -->

<div class="tabla-problemas">

    <h2>Tabla: Datos De los Cursos</h2>


    <!-- FILTROS -->

    <div class="filtros">

        <input type="text" class="filtro" data-col="0" placeholder="Buscar correo">
        <input type="text" class="filtro" data-col="1" placeholder="Buscar columna 1">
        <input type="text" class="filtro" data-col="2" placeholder="Buscar columna 2">
        <input type="text" class="filtro" data-col="3" placeholder="Buscar desempeño">
        <input type="text" class="filtro" data-col="4" placeholder="Buscar curso concluido">
        <input type="text" class="filtro" data-col="5" placeholder="Buscar fecha corte">
        <input type="text" class="filtro" data-col="6" placeholder="Buscar título curso">

    </div>

    <br>


    <div class="tabla-scroll">

        <table id="tablaDatos">

            <thead>

                <tr>
                    <th>Correo</th>
                    <th>Columna 1</th>
                    <th>Columna 2</th>
                    <th>Desempeño</th>
                    <th>Curso Concluido</th>
                    <th>Fecha Corte</th>
                    <th>Título Curso</th>
                    <th>Eliminar</th>
                </tr>

            </thead>

            <tbody>

<?php

    $consulta = "SELECT * FROM datos2 ORDER BY correo ASC";
    $resultado = mysqli_query($conectar,$consulta);

    if($resultado && mysqli_num_rows($resultado)>0){

        while($fila = mysqli_fetch_assoc($resultado)){

            echo "<tr>";

                echo "<td>".htmlspecialchars($fila['correo'])."</td>";
                echo "<td>".htmlspecialchars($fila['columna1'])."</td>";
                echo "<td>".htmlspecialchars($fila['columna2'])."</td>";
                echo "<td>".htmlspecialchars($fila['desempeño'])."</td>";
                echo "<td>".htmlspecialchars($fila['curso_concluido'])."</td>";
                echo "<td>".htmlspecialchars($fila['fecha_corte'])."</td>";
                echo "<td>".htmlspecialchars($fila['titulo_curso'])."</td>";

                echo "<td>
                        <a href='#' onclick=\"validar('eliminar_datos.php?correo=".urlencode($fila['correo'])."')\">
                            Eliminar
                        </a>
                      </td>";

            echo "</tr>";

        }

    }else{

        echo "<tr>
                <td colspan='8'>No se encontraron registros.</td>
              </tr>";

    }

?>

            </tbody>

        </table>

    </div>

</div>


<script>

/////////////////////////////////////////////////
// CONFIRMAR ELIMINAR
/////////////////////////////////////////////////

function validar(url){

    let eliminar = confirm("¿Realmente deseas eliminar este registro?");

    if(eliminar){
        window.location = url;
    }

}

/////////////////////////////////////////////////
// FILTRADO MULTICOLUMNA
/////////////////////////////////////////////////

const filtros = document.querySelectorAll(".filtro");
const filas = document.querySelectorAll("#tablaDatos tbody tr");

filtros.forEach(input => {

    input.addEventListener("keyup", filtrarTabla);

});

function filtrarTabla(){

    filas.forEach(fila => {

        let mostrar = true;

        filtros.forEach(filtro => {

            let textoFiltro = filtro.value.toLowerCase();
            let columna = filtro.dataset.col;

            let celda = fila.children[columna];

            if(celda){

                let textoCelda = celda.textContent.toLowerCase();

                if(textoFiltro !== "" && !textoCelda.includes(textoFiltro)){
                    mostrar = false;
                }

            }

        });

        fila.style.display = mostrar ? "" : "none";

    });

}

/////////////////////////////////////////////////
// FUNCIONES DE EXPORTACIÓN
/////////////////////////////////////////////////

function obtenerFilasFiltradas(){

    let filasVisibles = [];

    document.querySelectorAll("#tablaDatos tbody tr").forEach(fila => {

        if(fila.style.display !== "none"){

            let datos=[];

            for(let i=0;i<7;i++){
                datos.push(fila.children[i].textContent);
            }

            filasVisibles.push(datos);

        }

    });

    return filasVisibles;

}

function obtenerFechaHora(){

    let ahora = new Date();
    return ahora.toLocaleString();

}

/////////////////////////////////////////////////
// EXPORTAR PDF
/////////////////////////////////////////////////

function exportarPDF(){

    const { jsPDF } = window.jspdf;

    let doc = new jsPDF('l','pt','a4');

    let filas = obtenerFilasFiltradas();

    doc.setFontSize(18);
    doc.text("Reporte Cursos MOOCS",40,40);

    doc.setFontSize(10);
    doc.text("Generado: "+obtenerFechaHora(),40,60);

    doc.autoTable({

        head:[[
            "Correo",
            "Columna 1",
            "Columna 2",
            "Desempeño",
            "Curso Concluido",
            "Fecha Corte",
            "Título Curso"
        ]],

        body:filas,
        startY:80,

        styles:{fontSize:8},

        headStyles:{
            fillColor:[44,62,80],
            textColor:255
        },

        alternateRowStyles:{
            fillColor:[240,240,240]
        }

    });

    doc.save("reporte_cursos.pdf");

}

/////////////////////////////////////////////////
// EXPORTAR EXCEL
/////////////////////////////////////////////////

function exportarExcel(){

    let filas = obtenerFilasFiltradas();

    let encabezados = [
        "Correo",
        "Columna 1",
        "Columna 2",
        "Desempeño",
        "Curso Concluido",
        "Fecha Corte",
        "Título Curso"
    ];

    let datos = [encabezados,...filas];

    let wb = XLSX.utils.book_new();
    let ws = XLSX.utils.aoa_to_sheet(datos);

    ws['!cols']=[
        {wch:35},
        {wch:20},
        {wch:20},
        {wch:12},
        {wch:18},
        {wch:18},
        {wch:40}
    ];

    ws['!autofilter'] = { ref: "A1:G1" };

    XLSX.utils.book_append_sheet(wb,ws,"Cursos");

    let fecha = new Date().toISOString().slice(0,10);

    XLSX.writeFile(wb,`reporte_cursos_${fecha}.xlsx`);

}

</script>

</body>
</html>