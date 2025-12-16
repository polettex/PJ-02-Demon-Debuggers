# Demon Deburgers – Gestión de Reservas y Recursos

Aplicación web integral para la gestión de un restaurante, que permite la administración de usuarios, recursos (salas y mesas), y la gestión avanzada de reservas tanto en tiempo real como anticipadas.

---

## 🚀 Funcionalidades Principales

### 1. Gestión de Reservas (Camareros)

- **Reservas Anticipadas:** Creación de reservas para fechas y horas futuras.
- **Ocupación en Tiempo Real:** Marcar mesas como ocupadas/libres al instante.
- **Validación:** Control de aforo y disponibilidad de mesas para evitar conflictos.
- **Historial:** Registro detallado de todas las ocupaciones y reservas pasadas.

### 2. Administración (Gerentes/Admin)

- **CRUD de Usuarios:**
  - Crear, leer, actualizar y eliminar usuarios.
  - Asignación de roles (Camarero, Gerente, Mantenimiento, Admin).
- **CRUD de Recursos:**
  - Gestión de Salas y Mesas.
  - Asignación de imágenes a las salas.
  - Control de capacidad y estado.

### 3. Características Técnicas

- **Base de Datos:** MySQL con estructura relacional optimizada.
- **Backend:** PHP puro con PDO para conexiones seguras.
- **Frontend:** HTML5, CSS3 (Diseño Responsivo), JavaScript.
- **Alertas:** Integración con SweetAlert2 para notificaciones amigables.
- **Seguridad:** Contraseñas encriptadas con BCRYPT.

---

## 📂 Estructura del Proyecto

- `bbdd/`: Scripts SQL para la creación y población de la base de datos.
- `css/`: Estilos CSS para el diseño responsivo y moderno.
- `img/`: Imágenes de recursos y assets gráficos.
- `includes/`: Archivos PHP reutilizables (conexión, cabecera, footer).
- `js/`: Scripts JavaScript para validaciones y lógica cliente.
- `proc/`: Scripts PHP de procesamiento (lógica de negocio).
- `views/`: Vistas de la aplicación (páginas visibles para el usuario).

---

## 🛠️ Instalación y Configuración

1. **Clonar el repositorio:**

   ```bash
   git clone <URL_DEL_REPOSITORIO>
   ```

2. **Configurar la Base de Datos:**

   - Importar el archivo `bbdd/bbdd4.sql` en tu servidor MySQL.
   - Verificar la configuración de conexión en `includes/conexion.php`.

3. **Ejecutar la aplicación:**
   - Colocar el proyecto en el directorio raíz de tu servidor web (ej. `www` o `htdocs`).
   - Acceder desde el navegador a `http://localhost/PROYECTO2/PJ-02-Demon-Debuggers/`.

---

## 👤 Usuarios de Prueba

| Rol               | Usuario          | Contraseña  |
| :---------------- | :--------------- | :---------- |
| **Administrador** | `admin`          | `qazQAZ123` |
| **Gerente**       | `gerente`        | `qazQAZ123` |
| **Mantenimiento** | `mantenimiento1` | `qazQAZ123` |
| **Camarero**      | `camarero1`      | `qazQAZ123` |

---

## 📊 Estructura de la Base de Datos

- **usuarios:** Almacena credenciales y roles.
- **roles:** Define los niveles de acceso.
- **recursos:** Salas y mesas con sus propiedades (capacidad, estado, imagen).
- **recursos_jerarquia:** Relaciona mesas con sus respectivas salas.
- **reservas:** Registro de reservas futuras y ocupaciones pasadas.

---

## 📱 Diseño Responsivo

La aplicación está diseñada para adaptarse a diferentes dispositivos:

- **Escritorio:** Vista completa con paneles laterales y tablas detalladas.
- **Móvil/Tablet:** Diseño adaptado con menús accesibles y tablas con desplazamiento horizontal para facilitar el uso en movimiento por parte de los camareros.
