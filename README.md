# Practica PHP 2 - Sistema de tickets

Aplicacion realizada con Lumen, PHP, MySQL y Vue 3 para practicar autenticacion de usuarios y administracion de tickets.

## Funcionalidades

- Registro de usuarios con nombre, correo, contrasena y rol.
- Inicio de sesion con JWT.
- Roles `client` y `admin`.
- Proteccion de rutas mediante middleware JWT.
- Vista de login y registro con Vue 3.
- Vista de tickets para clientes.
- Creacion de tickets desde la vista del cliente.
- Seleccion de prioridad: baja, media o alta.
- Estado inicial automatico: abierta.
- Los clientes solo consultan sus propios tickets.
- Los administradores pueden consultar todos los tickets.

## Tecnologias

- PHP 8.1 o superior.
- Lumen 10.
- MySQL.
- Vue 3 mediante CDN.
- Firebase PHP-JWT.
- HTML y CSS.

## Instalacion

1. Instalar las dependencias de PHP:

   ```powershell
   composer install
   ```

2. Crear el archivo `.env` a partir de `.env.example`.

3. Configurar en `.env` la base de datos y el secreto JWT:

   ```env
   DB_DATABASE=ticket_db
   DB_USERNAME=root
   DB_PASSWORD=
   JWT_SECRET=un-secreto-largo-y-seguro
   JWT_TTL=3600
   ```

4. Crear las tablas:

   ```powershell
   php artisan migrate
   ```

5. Iniciar el servidor:

   ```powershell
   php artisan serve
   ```

## Rutas principales

| Metodo | Ruta | Descripcion |
|---|---|---|
| GET | `/` | Redirige al login |
| GET | `/login` | Muestra la vista de inicio de sesion |
| POST | `/login` | Autentica al usuario |
| GET | `/register` | Muestra la vista de registro |
| POST | `/register` | Registra un usuario |
| GET | `/client/tickets` | Muestra los tickets del cliente |
| GET | `/tickets` | Lista tickets, requiere JWT |
| POST | `/tickets` | Crea un ticket, requiere JWT |

Las rutas protegidas requieren el encabezado:

```http
Authorization: Bearer TU_TOKEN
```

## Flujo de uso

1. Entrar a `http://localhost:8000/login`.
2. Crear una cuenta desde `/register`.
3. Iniciar sesion.
4. Si el usuario es cliente, se redirige a `/client/tickets`.
5. Desde esa vista puede consultar sus tickets y crear nuevos.

El proyecto usa Vue dentro de las vistas PHP. Vue controla los formularios y realiza las peticiones AJAX con `fetch`, mientras Lumen valida los datos, aplica el middleware JWT y se comunica con MySQL.
