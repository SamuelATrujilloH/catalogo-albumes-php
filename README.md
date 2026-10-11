# 🎵 Sistema de Catálogo y Gestión de Álbumes Musicales

Plataforma web dinámica desarrollada en PHP para la administración, consulta y control de un catálogo discográfico. El sistema cuenta con autenticación segura de usuarios, un panel administrativo completo (CRUD) y una vista pública optimizada para la consulta de álbumes musicales.

---

## 🚀 Funcionalidades Clave

* **Módulo de Autenticación:** Sistema de inicio (`login.php`) y cierre de sesión (`logout.php`) con control de roles (`usuarios`).
* **Panel Administrativo (CRUD):** 
  * Registro y alta de nuevos álbumes y artistas (`registro.php` / `procesar_album.php`).
  * Edición y actualización de registros existentes (`editar_album.php` / `actualizar_album.php`).
  * Eliminación lógica/física de elementos obsoletos (`eliminar_album.php`).
* **Vista Pública / Catálogo:** Búsqueda, filtrado y visualización detallada del inventario musical (`catalogo.php` / `ver_catalogo.php`).

---

## 🛠️ Tecnologías Utilizadas

* **Backend:** PHP
* **Base de Datos:** Microsoft SQL Server (Base de datos: `discografica3`)
* **Conexión:** Script dedicado con credenciales de servidor (`conexion.php`)
* **Frontend:** HTML5, CSS3 (`/css`)
* **Servidor Local:** Apache (XAMPP)

---

## 📁 Estructura del Proyecto

```text
├── css/                     # Hojas de estilo y diseño visual
├── actualizar_album.php     # Lógica de guardado para modificaciones
├── catalogo.php             # Vista general e interactiva del catálogo
├── conexion.php             # Enlace de conexión con el servidor de base de datos
├── editar_album.php         # Interfaz y formulario de edición
├── eliminar_album.php       # Script de baja de registros
├── login.php                # Pantalla de acceso al sistema
├── logout.php               # Destrucción de sesión de usuario
├── manage_catalogs.php      # Panel central de control administrativo
├── procesar_album.php       # Procesador backend de altas y formularios
├── registro.php             # Formulario de alta de álbumes
└── ver_catalogo.php         # Detalle y visualización específica de elementos
