<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excel a MySQL</title>

    <link rel="stylesheet" href="estilos.css">
    <link rel="stylesheet" href="server.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <style>

        /* ===== HEADER ===== */

        .hero {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 60px;
            padding: 20px 40px;
        }

        /* CUADRO BLANCO DE LOS LOGOS */

        .logo {
            background: white;
            padding: 10px 18px;
            border-radius: 15px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* IMÁGENES */

        .logo img {
            height: 65px;
            object-fit: contain;
        }

        /* TEXTO CENTRAL */

        .hero .ancho {
            text-align: center;
            max-width: 600px;
        }

    </style>

</head>


<body>

    <!-- ===== HEADER ===== -->

    <header class="hero">

        <div class="logo">
            <img src="imagenes/ITM.svg" alt="Logo ITM">
        </div>

        <div class="ancho">
            <h1 class="hero-title">Cursos MOOCS</h1>
            <p class="hero-text">
                Carga tu archivo Excel, procesa los datos y dalos de alta
            </p>
        </div>

        <div class="logo">
            <img src="imagenes/TECNM.png" alt="Logo TECNM">
        </div>

    </header>


    <!-- ===== SECCIÓN FORMULARIO ===== -->

    <section class="formulario ancho">

        <h2 class="servicio-titulo">Subir archivo Excel</h2>

        <input 
            type="file" 
            id="fileInput" 
            accept=".xls,.xlsx" 
            class="btn"
        >

        <button onclick="convertExcelToJson()" class="btn">
            Convertir y Enviar
        </button>

        <button 
            id="serverButton" 
            onclick="controlServidor()" 
            class="btn btn-server"
        >
            Verificando servidor...
        </button>

        <div id="serverStatus" class="server-status">
            Verificando servidor...
        </div>

        <div id="progressContainer" class="progress-container hidden">
            <div id="progressBar" class="progress-bar"></div>
        </div>

        <a href="vercursos.php" class="btn">
            Ver Cursos MOOCS
        </a>

    </section>


    <!-- ===== JAVASCRIPT ===== -->

    <script>

        function convertExcelToJson() {

            const fileInput = document.getElementById('fileInput');

            if (!fileInput.files.length) {
                alert('Selecciona un Excel');
                return;
            }

            const file = fileInput.files[0];
            const reader = new FileReader();

            reader.onload = function(event) {

                const data = new Uint8Array(event.target.result);
                const workbook = XLSX.read(data, { type: 'array' });

                let jsonData = [];

                workbook.SheetNames.forEach(sheetName => {

                    const worksheet = workbook.Sheets[sheetName];
                    let json = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

                    if (json.length < 3) return;

                    const fecha = json[0][0] ? json[0][0].toString() : "Sin fecha";
                    const titulo = json[1][0] ? json[1][0].toString() : "Sin titulo";

                    json = json.slice(2).map(row => ({

                        correo: row[0] || "",
                        columna1: row[1] || null,
                        columna2: row[2] || null,
                        desempeño: parseInt(row[3]) || 0,
                        curso_concluido: row[4] === "S" ? "S" : "N",
                        fecha_corte: fecha,
                        titulo_curso: titulo

                    }));

                    jsonData = jsonData.concat(json);

                });

                fetch("http://localhost:3000/guardar-json", {
                    method: "POST",
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(jsonData)
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.mensaje);
                });

            };

            reader.readAsArrayBuffer(file);
        }



        /* ===== CONTROL DEL SERVIDOR ===== */

        let estadoServidor = "desconocido";


        function verificarServidor() {

            fetch("estado_server.php")
            .then(res => res.json())
            .then(data => {

                const status = document.getElementById("serverStatus");
                const boton = document.getElementById("serverButton");

                if (data.estado === "activo") {

                    estadoServidor = "activo";

                    status.innerHTML = "🟢 SERVIDOR ONLINE";
                    status.style.color = "green";

                    boton.innerHTML = "DETENER SERVIDOR";
                    boton.className = "btn btn-stop";
                }

                if (data.estado === "inactivo") {

                    estadoServidor = "inactivo";

                    status.innerHTML = "🔴 SERVIDOR OFFLINE";
                    status.style.color = "red";

                    boton.innerHTML = "INICIAR SERVIDOR";
                    boton.className = "btn btn-server";
                }

            });
        }



        function controlServidor() {

            const boton = document.getElementById("serverButton");
            const status = document.getElementById("serverStatus");


            if (estadoServidor === "inactivo") {

                status.innerHTML = "🟡 INICIANDO SERVIDOR...";
                status.style.color = "orange";

                boton.disabled = true;

                fetch("activar_server.php")
                .then(res => res.json())
                .then(data => {

                    setTimeout(() => {

                        boton.disabled = false;
                        verificarServidor();

                    }, 3000);

                });
            }


            if (estadoServidor === "activo") {

                status.innerHTML = "🔴 DETENIENDO SERVIDOR...";
                status.style.color = "red";

                boton.disabled = true;

                fetch("detener_server.php")
                .then(res => res.json())
                .then(data => {

                    setTimeout(() => {

                        boton.disabled = false;
                        verificarServidor();

                    }, 2000);

                });
            }
        }


        window.onload = verificarServidor;

        setInterval(verificarServidor, 5000);

    </script>

</body>
</html>