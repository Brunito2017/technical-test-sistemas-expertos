# Sistema de Gestión de Bodegas — Sistemas Expertos

Sistema web para administrar bodegas: crear, listar, editar y eliminar.

---

## Tecnologías utilizadas

- **PHP 7.0+** — Backend y API REST
- **PostgreSQL** — Base de datos relacional
- **HTML5, CSS3, JavaScript ES6** — Frontend (sin frameworks)
- **Fetch API / ES Modules** — Comunicación con el servidor

---

## Requisitos previos

- PHP 7.0+ con extensiones `pdo` y `pdo_pgsql`
- PostgreSQL 12+
- Servidor web Apache (XAMPP, Laragon, etc.)

---

## Instalación


1. Copia el proyecto en la carpeta del servidor web
2. Crea la base de datos y ejecuta el dump:
  ```bash
  psql -U postgres -c "CREATE DATABASE houseware_db;"
  psql -U postgres -d houseware_db -f schema_tech_dump.sql
  ```
3. (Opcional) Inserta usuarios de ejemplo:
  ```bash
  php seed_users.php
  ```
4. Configura credenciales en `app/config/Database.php`
5. Configura la URL base en `public/js/warehouseApi.js`
6. Accede a `http://localhost/technical-test-sistemas-expertos/public/index.html`

---

## Descripción del código

El proyecto sigue una arquitectura **MVC**:

- `app/api/` — Reciben las peticiones HTTP y devuelven JSON
- `app/controllers/` — Coordinan la lógica entre la API y los servicios
- `app/models/` — Acceso y consultas a la base de datos
- `app/services/` — Lógica de negocio
- `app/validators/` — Validaciones de datos de entrada
- `public/js/` — Frontend dividido en módulos:
  - `warehouseApi.js` — Cliente HTTP reutilizable
  - `warehouseTable.js` — Renderizado y eventos de la tabla
  - `warehouseForm.js` — Lógica del formulario
  - `main.js` — Punto de entrada, inicializa los módulos

---

## Autor

Prueba técnica desarrollada para postulación a Sistemas Expertos — Abril 2026