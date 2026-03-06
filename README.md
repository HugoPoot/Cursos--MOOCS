# Cursos--MOOCS
REPOSITORIO PARA ARCHIVOS DE SERVICIO SOCIAL

#  Guía de Instalación Completa

## 📋 Requisitos Previos

Antes de comenzar, asegúrate de tener instalados los siguientes componentes:

- **XAMPP** (Apache 2.4+, MySQL 5.7+, PHP 7.4+)
- **Navegador web** moderno (Chrome, Firefox, Edge)
- **Git** (opcional, para clonación)

## 🔧 Pasos de Instalación

### Paso 1: Descargar el Proyecto

Tienes dos opciones para obtener el proyecto:

#### Opción A: Clonar con Git
```bash
git clone https://github.com/B4sal/ActualizacionDocente.git
```

#### Opción B: Descargar ZIP
1. Ve al repositorio de GitHub
2. Haz clic en el botón **Code** (verde)
3. Selecciona **Download ZIP**


### Paso 2: Configurar el Entorno

1. **Mover los archivos a XAMPP**
   - Copia/mueve la carpeta del proyecto a: `C:\xampp\htdocs\Servicio\`
   - Asegúrate de que la estructura de carpetas sea correcta

2. **Iniciar servicios de XAMPP**
   - Abre el **XAMPP Control Panel**
   - Haz clic en **Start** para **Apache**
   - Haz clic en **Start** para **MySQL**
   - Espera a que ambos servicios estén en verde

### Paso 3: Configurar la Base de Datos

#### 3.1 Crear la Base de Datos
1. Abre tu navegador web
2. Ve a: `http://localhost/phpmyadmin`
3. Haz clic en **New** (Nueva) en el panel izquierdo
4. Escribe el nombre: `prueba`
5. Selecciona **utf8_general_ci** como collation
6. Haz clic en **Create**


#### 3.2 Importar el Esquema
1. Selecciona la base de datos `datos2` que acabas de crear
2. Haz clic en la pestaña **Import** (Importar)
3. Haz clic en **Choose File** (Elegir archivo)
4. Navega hasta: `proyecto de servicio social/sql/prueba.sql`
5. Selecciona el archivo y haz clic en **Open**
6. Haz clic en **Go** (Continuar) para importar


### Paso 4: Probar la Instalación

1. Abre tu navegador web
2. Ve a: `http://localhost/servicio/inicial.php/`
3. Deberías ver la página principal del sistema

## ✅ Verificación de Instalación

### Checklist de Verificación
- [ ] Apache está ejecutándose (verde en XAMPP)
- [ ] MySQL está ejecutándose (verde en XAMPP)
- [ ] Base de datos `prueba` creada
- [ ] Tablas importadas correctamente
- [ ] Página principal carga sin errores
- [ ] Puedes navegar por las diferentes secciones

### Páginas de Prueba
- **Dashboard**: `http://localhost/servicio/inicial.php`
- **Cursos**: `http://localhost/servicio/vercursos.php`

## 🐛 Solución de Problemas Comunes

### Error: "Could not connect to database"
- **Causa**: MySQL no está ejecutándose o configuración incorrecta
- **Solución**: Verifica que MySQL esté activo en XAMPP y revisa `config/conn.php`

### Error: "Servidor Ofline"
- **Causa**: servidor nodejs no importado correctamente
- **Solución**: Repite el la carga y ajuste del servidor Nodejs

### Error: "Access forbidden"
- **Causa**: Archivos no están en la carpeta correcta
- **Solución**: Verifica que los archivos están en `C:\xampp\htdocs\servicio\`

### Error: "Port 3000 in use"
- **Causa**: Otro servicio está usando el puerto 3000
- **Solución**: Cierra el nodejs y vuelve a darlo de alta


  <p>Ahora puedes comenzar a usar el Sistema de Actualización Docente</p>
</div>
