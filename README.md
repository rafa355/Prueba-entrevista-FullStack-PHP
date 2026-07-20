# Customer Management API

API RESTful para gestión de clientes construida con **Laravel 13.8** y **PHP 8.3+**. Implementa autenticación por token SHA1, CRUD de clientes con jerarquía geográfica (Regiones → Comunas → Clientes), sistema de logs y middlewares de seguridad.

---

## Tabla de contenidos

- [Requerimientos mínimos](#requerimientos-mínimos)
- [Instalación](#instalación)
- [Configuración del .env](#configuración-del-env)
- [Estructura de la base de datos](#estructura-de-la-base-de-datos)
- [Servicios disponibles](#servicios-disponibles)
- [Middlewares](#middlewares)
- [Autenticación](#autenticación)
- [API Tester](#api-tester)
- [Ejemplos de uso](#ejemplos-de-uso)
- [Estructura del proyecto](#estructura-del-proyecto)

---

## Requerimientos mínimos

| Componente | Versión |
|------------|---------|
| PHP | >= 8.3 |
| Composer | >= 2.x |
| MySQL | >= 5.7 (Motor MyISAM) |
| Node.js | >= 18.x |
| npm | >= 9.x |

### Extensiones PHP necesarias

- `php-mysql` / `php-mysqli`
- `php-json`
- `php-mbstring`
- `php-xml`
- `php-curl`
- `php-bcmath`
- `php-hash`

---

## Instalación

```bash
# Clonar el repositorio
git clone https://github.com/tu-usuario/fullstack-test.git
cd fullstack-test

# Instalar dependencias PHP
composer install

# Configurar entorno
cp .env.example .env
php artisan key:generate

# Configurar base de datos (ver sección .env)

# Ejecutar migraciones
php artisan migrate:fresh

# Instalar dependencias JS (opcional, para frontend)
npm install
npm run build

# Iniciar servidor
php artisan serve
```

O usar el script de setup automático:

```bash
composer setup
```

---

## Configuración del .env

```env
# Aplicación
APP_NAME=CustomerAPI
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

# Base de datos (MySQL requerido para MyISAM)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=testing
DB_USERNAME=root
DB_PASSWORD=root
```

> **Nota:** Las tablas del dominio usan el motor **MyISAM**. Asegúrese de que su servidor MySQL soporte este motor.

---

## Estructura de la base de datos

### Tablas del dominio (MyISAM)

#### `regions`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_reg` | INT UNSIGNED AI | PK |
| `description` | VARCHAR(90) | Nombre de la región |
| `status` | ENUM('A','I','trash') | Estado (default: 'A') |

#### `communes`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_com` | INT UNSIGNED AI | PK (junto con id_reg) |
| `id_reg` | INT UNSIGNED | PK (junto con id_com), FK → regions.id_reg |
| `description` | VARCHAR(90) | Nombre de la comuna |
| `status` | ENUM('A','I','trash') | Estado (default: 'A') |

#### `customers`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `dni` | VARCHAR(45) | PK (Documento de identidad, junto con id_reg e id_com) |
| `id_reg` | INT UNSIGNED | PK (junto con dni e id_com), FK → regions.id_reg |
| `id_com` | INT UNSIGNED | PK (junto con dni e id_reg), FK → communes.id_com |
| `email` | VARCHAR(120) | UNIQUE |
| `password` | VARCHAR(255) | Hash bcrypt |
| `name` | VARCHAR(45) | Nombre |
| `last_name` | VARCHAR(45) | Apellido |
| `address` | VARCHAR(255) | Nullable |
| `date_reg` | DATETIME | Fecha de registro |
| `status` | ENUM('A','I','trash') | Estado (default: 'A') |

#### `tokens`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | BIGINT AI | PK |
| `token` | VARCHAR(40) | UNIQUE (SHA1) |
| `email` | VARCHAR(191) | Email del usuario |
| `date_reg` | DATETIME | Fecha de creación |
| `ttl` | SMALLINT UNSIGNED | Minutos de vida (default: 60) |
| `status` | ENUM('A','I','trash') | Estado (default: 'A') |

#### `logs`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | BIGINT AI | PK |
| `method` | VARCHAR(10) | HTTP method |
| `url` | VARCHAR(255) | URL completa |
| `ip` | VARCHAR(45) | IP del cliente |
| `request_body` | TEXT nullable | Body sanitizado (sin password/token) |
| `response_status` | SMALLINT UNSIGNED nullable | HTTP status code |
| `response_body` | TEXT nullable | Respuesta (max 1000 chars) |
| `created_at` | DATETIME | Timestamp del log |

---

## Servicios disponibles

### Rutas API

| Método | Endpoint | Descripción | Autenticación |
|--------|----------|-------------|---------------|
| `POST` | `/api/login` | Login y generación de token | No |
| `GET` | `/api/regions` | Obtener regiones con comunas asociadas | Sí (Bearer token) |
| `GET` | `/api/customers/all` | Obtener todos los clientes activos | Sí (Bearer token) |
| `POST` | `/api/customers` | Registrar cliente | Sí (Bearer token) |
| `GET` | `/api/customers` | Consultar cliente por DNI o email | Sí (Bearer token) |
| `DELETE` | `/api/customers/{dni}` | Eliminar cliente (lógico) | Sí (Bearer token) |

> **Nota:** El endpoint `GET /api/regions` retorna todas las regiones activas con sus comunas asociadas. Esta información es necesaria para obtener los `id_reg` e `id_com` válidos al momento de crear un cliente.

> **Métodos permitidos:** Solo `POST`, `GET` y `DELETE`. Cualquier otro método retorna `405 Method Not Allowed`.

---

## Middlewares

### Globales (todas las rutas API)

| Middleware | Función |
|------------|---------|
| `LoggingMiddleware` | Registra request/response en la tabla `logs`. En producción solo guarda request. |
| `MethodMiddleware` | Bloquea métodos HTTP distintos a GET, POST, DELETE (405). |

### Por ruta

| Middleware | Ruta | Función |
|------------|------|---------|
| `TokenMiddleware` | Todas excepto `/api/login` | Valida token Bearer en header Authorization. |

### Form Requests (validación)

| Form Request | Ruta | Función |
|--------------|------|---------|
| `LoginRequest` | `POST /api/login` | Valida campos email y password. |
| `StoreCustomerRequest` | `POST /api/customers` | Valida campos obligatorios, existencia de region/commune y relación entre ellas. |
| `ShowCustomerRequest` | `GET /api/customers` | Valida que se envíe al menos `dni` o `email` como query param. |

---

## Autenticación

### Flujo

1. El cliente envía `POST /api/login` con `email` y `password`.
2. El servidor valida las credenciales contra la tabla `customers`.
3. Se genera un token SHA1 compuesto por: `email + fecha_hora + random(200..500)`.
4. El token se almacena en la tabla `tokens` con un TTL de 60 minutos.
5. El cliente usa el token en el header `Authorization: Bearer <token>` para acceder a los servicios protegidos.

### Ejemplo

```bash
# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email": "usuario@test.com", "password": "123456"}'

# Respuesta
{
  "success": true,
  "message": "Login exitoso.",
  "data": {
    "token": "a1b2c3d4e5f6...",
    "expires_in": "60 minutos"
  }
}
```

### Token expirado

Cuando un token vence, el `TokenMiddleware` lo marca automáticamente como inactivo (`status: 'I'`) y retorna:

```json
{
  "success": false,
  "message": "Token vencido."
}
```

---

## API Tester

El proyecto incluye una interfaz web para probar los endpoints de la API, similar a Postman o Insomnia. Para acceder:

1. Inicia el servidor de desarrollo:
```bash
php artisan serve
```

2. Abre en el navegador: `http://localhost:8000`

### Características del API Tester

- Panel lateral con todos los endpoints disponibles
- Formulario dinámico según el endpoint seleccionado
- Autenticación automática (el token se obtiene al hacer login)
- Respuestas con syntax highlighting para JSON
- Indicador de status code y tiempo de respuesta
- Diseño dark mode responsive

### Endpoints disponibles en el tester

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `POST` | `/api/login` | Login y obtención de token |
| `GET` | `/api/regions` | Obtener regiones con comunas |
| `GET` | `/api/customers/all` | Obtener todos los clientes activos |
| `POST` | `/api/customers` | Registrar nuevo cliente |
| `GET` | `/api/customers` | Consultar cliente por DNI o email |
| `DELETE` | `/api/customers/{dni}` | Eliminar cliente (lógico) |

> **Tip:** Usa el endpoint `GET /api/regions` primero para obtener los IDs de regiones y comunas válidos antes de crear un cliente.

---

## Ejemplos de uso

### Registrar cliente

```bash
curl -X POST http://localhost:8000/api/customers \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <token>" \
  -d '{
    "dni": "12345678",
    "id_reg": 1,
    "id_com": 1,
    "email": "cliente@test.com",
    "password": "123456",
    "name": "Juan",
    "last_name": "Pérez",
    "address": "Av. Principal 123"
  }'

# Respuesta (201)
{
  "success": true,
  "message": "Cliente registrado exitosamente.",
  "data": {
    "dni": "12345678",
    "email": "cliente@test.com",
    "name": "Juan",
    "last_name": "Pérez"
  }
}
```

### Obtener todos los clientes

```bash
curl -X GET http://localhost:8000/api/customers/all \
  -H "Authorization: Bearer <token>"

# Respuesta
{
  "success": true,
  "data": [
    {
      "dni": "12345678",
      "email": "cliente@test.com",
      "name": "Juan",
      "last_name": "Pérez",
      "address": "Av. Principal 123",
      "region": "Región Metropolitana de Santiago",
      "commune": "Santiago"
    }
  ]
}
```

### Consultar por DNI

```bash
curl -X GET "http://localhost:8000/api/customers?dni=12345678" \
  -H "Authorization: Bearer <token>"

# Respuesta
{
  "success": true,
  "data": {
    "name": "Juan",
    "last_name": "Pérez",
    "address": "Av. Principal 123",
    "region": "Metropolitana",
    "commune": "Santiago"
  }
}
```

### Consultar por email

```bash
curl -X GET "http://localhost:8000/api/customers?email=cliente@test.com" \
  -H "Authorization: Bearer <token>"
```

### Obtener regiones con comunas

```bash
curl -X GET http://localhost:8000/api/regions \
  -H "Authorization: Bearer <token>"

# Respuesta
{
  "success": true,
  "data": [
    {
      "id_reg": 1,
      "description": "Región Metropolitana de Santiago",
      "communes": [
        {"id_com": 1, "description": "Santiago"},
        {"id_com": 2, "description": "Providencia"},
        {"id_com": 3, "description": "Las Condes"}
      ]
    }
  ]
}
```

> **Nota:** Este endpoint es útil para obtener los IDs válidos de regiones y comunas antes de crear un cliente.

### Eliminar cliente (lógico)

```bash
curl -X DELETE http://localhost:8000/api/customers/12345678 \
  -H "Authorization: Bearer <token>"

# Respuesta
{
  "success": true,
  "message": "Cliente eliminado exitosamente."
}

# Si ya tiene status 'trash' o no existe
{
  "success": false,
  "message": "Registro no existe"
}
```

### Error de validación

```bash
curl -X POST http://localhost:8000/api/customers \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <token>" \
  -d '{"dni": "12345678"}'

# Respuesta (422)
{
  "success": false,
  "errors": {
    "id_reg": "El campo id_reg es obligatorio.",
    "id_com": "El campo id_com es obligatorio.",
    "email": "El campo email es obligatorio.",
    "name": "El campo name es obligatorio.",
    "last_name": "El campo last_name es obligatorio.",
    "password": "El campo password es obligatorio."
  }
}
```

---

## Estructura del proyecto

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── CustomerController.php
│   │   │   └── RegionController.php
│   │   ├── Middleware/
│   │   │   ├── LoggingMiddleware.php
│   │   │   ├── MethodMiddleware.php
│   │   │   └── TokenMiddleware.php
│   │   └── Requests/
│   │       ├── LoginRequest.php
│   │       ├── StoreCustomerRequest.php
│   │       └── ShowCustomerRequest.php
│   ├── Models/
│   │   ├── Commune.php
│   │   ├── Customer.php
│   │   ├── Log.php
│   │   ├── Region.php
│   │   └── Token.php
│   └── Services/
│       ├── AuthService.php
│       ├── CustomerService.php
│       └── RegionService.php
├── bootstrap/
│   └── app.php
├── config/
│   └── database.php
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
│       └── CustomerSeeder.php
├── resources/
│   └── views/
│       └── api-tester.blade.php
├── routes/
│   ├── api.php
│   └── web.php
├── .env.example
├── composer.json
└── README.md
```

---

## Licencia

Proyecto abierto bajo licencia MIT.
