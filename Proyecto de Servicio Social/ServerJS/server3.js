const express = require('express');
const mysql = require('mysql2');
const cors = require('cors');

const app = express();
app.use(cors());
app.use(express.json({ limit: '50mb' })); // Límite generoso para grandes volúmenes de datos

// Conexión a MySQL
const connection = mysql.createConnection({
    host: 'localhost',
    user: 'root',         // Ajusta si tienes otro usuario
    password: '',         // Ajusta si tienes contraseña
    database: 'prueba'    // Asegúrate de que esta base de datos existe
});

connection.connect(err => {
    if (err) {
        console.error('❌ Error conectando a MySQL:', err.message);
        return;
    }
    console.log('✅ Conectado a MySQL');
});

// Ruta para guardar el JSON en bloques
app.post('/guardar-json', (req, res) => {
    const datos = req.body;

    if (!Array.isArray(datos) || datos.length === 0) {
        return res.status(400).json({ error: 'El JSON debe ser un array con datos válidos.' });
    }

    const valores = datos.map(row => [
        row.correo ? row.correo.toString().trim() : "",
        row.columna1 ? row.columna1.toString().trim() : null,
        row.columna2 ? row.columna2.toString().trim() : null,
        isNaN(parseInt(row.desempeño)) ? 0 : parseInt(row.desempeño),
        row.curso_concluido && row.curso_concluido.toUpperCase() === "S" ? "S" : "N",
        row.fecha_corte ? row.fecha_corte.toString().trim() : "Sin fecha",
        row.titulo_curso ? row.titulo_curso.toString().trim() : "Sin título"
    ]);

    const sql = 'INSERT INTO datos2 (correo, columna1, columna2, desempeño, curso_concluido, fecha_corte, titulo_curso) VALUES ?';

    // Dividir en bloques de 500
    const chunkSize = 500;
    const chunks = [];
    for (let i = 0; i < valores.length; i += chunkSize) {
        chunks.push(valores.slice(i, i + chunkSize));
    }

    let insertadas = 0;

    function insertarChunk(index) {
        if (index >= chunks.length) {
            return res.json({ mensaje: 'Datos insertados correctamente por bloques.', filas_insertadas: insertadas });
        }

        connection.query(sql, [chunks[index]], (err, result) => {
            if (err) {
                console.error('❌ Error al insertar en bloque:', err.sqlMessage);
                return res.status(500).json({ error: 'Error al insertar datos', detalle: err.sqlMessage });
            }

            insertadas += result.affectedRows;
            insertarChunk(index + 1);
        });
    }

    insertarChunk(0);
});

// Iniciar servidor
app.listen(3000, () => {
    console.log('🚀 Servidor corriendo en http://localhost:3000');
});
