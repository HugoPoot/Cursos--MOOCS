<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Excel a MySQL</title>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

</head>

<body>

<h2>Cargar Excel y Guardar en MySQL</h2>

<input type="file" id="fileInput" accept=".xls,.xlsx">

<button onclick="convertExcelToJson()">Convertir y Enviar</button>

<pre id="jsonOutput"></pre>

<a href="vercursos.php">Cursos MOOCS</a>

<script>

function convertExcelToJson(){

const fileInput = document.getElementById('fileInput');

if(!fileInput.files.length){

alert("Selecciona un archivo Excel");
return;

}

const file = fileInput.files[0];
const reader = new FileReader();

reader.onload = function(e){

const data = new Uint8Array(e.target.result);
const workbook = XLSX.read(data,{type:'array'});

let jsonData=[];

//////////////////////////////////////////////////////
// RECORRER HOJAS DEL EXCEL
//////////////////////////////////////////////////////

workbook.SheetNames.forEach(sheetName=>{

const worksheet = workbook.Sheets[sheetName];

let rows = XLSX.utils.sheet_to_json(worksheet,{header:1});

if(rows.length === 0) return;

let fechaActual = "";
let cursoActual = "";

//////////////////////////////////////////////////////
// RECORRER FILAS
//////////////////////////////////////////////////////

for(let i=0;i<rows.length;i++){

let row = rows[i];

if(!row) continue;

let textoFila = row.join(" ");

//////////////////////////////////////////////////////
// DETECTAR FECHA
//////////////////////////////////////////////////////

let fecha = textoFila.match(/\d{2}\.\d{2}\.\d{4}/);

if(fecha){

fechaActual = fecha[0];

}

//////////////////////////////////////////////////////
// DETECTAR TITULO CURSO
//////////////////////////////////////////////////////

if(textoFila.toLowerCase().includes("desempeño")){

cursoActual = textoFila
.replace(/\s*-Desempeño.*$/i,"")
.trim();

}

//////////////////////////////////////////////////////
// DETECTAR FILAS DE ALUMNOS
//////////////////////////////////////////////////////

if(row[0] && row[0].includes("@")){

jsonData.push({

correo: row[0] ? row[0].toString().trim() : "",

columna1: row[1] ? row[1].toString().trim() : null,

columna2: row[2] ? row[2].toString().trim() : null,

desempeño: row[3] ? parseInt(row[3]) : 0,

curso_concluido:
row[4] && row[4].toString().trim().toUpperCase()==="S"
? "S"
: "N",

fecha_corte: fechaActual,

titulo_curso: cursoActual

});

}

}

});

//////////////////////////////////////////////////////
// VALIDAR DATOS
//////////////////////////////////////////////////////

if(jsonData.length===0){

alert("No se encontraron datos válidos en el Excel");
return;

}

document.getElementById("jsonOutput").textContent =
JSON.stringify(jsonData,null,2);

//////////////////////////////////////////////////////
// ENVIAR AL SERVIDOR NODE
//////////////////////////////////////////////////////

fetch("http://localhost:3000/guardar-json",{

method:"POST",

headers:{
"Content-Type":"application/json"
},

body:JSON.stringify(jsonData)

})
.then(res=>res.json())
.then(data=>{

alert(data.mensaje || "Datos guardados correctamente");

})
.catch(err=>{

console.error(err);
alert("Error enviando datos");

});

};

reader.readAsArrayBuffer(file);

}

</script>

</body>

</html>