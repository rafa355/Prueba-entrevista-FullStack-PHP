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
- [Ejemplos de uso](#ejemplos-de-uso)

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
| `description` | VARCHAR(255) | Nombre de la región |
| `status` | ENUM('A','I','trash') | Estado (default: 'A') |

#### `communes`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_com` | INT UNSIGNED AI | PK |
| `id_reg` | INT UNSIGNED | FK → regions.id_reg |
| `description` | VARCHAR(255) | Nombre de la comuna |
| `status` | ENUM('A','I','trash') | Estado (default: 'A') |

#### `customers`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `dni` | VARCHAR(20) | PK (Documento de identidad) |
| `id_reg` | INT UNSIGNED | FK → regions.id_reg |
| `id_com` | INT UNSIGNED | FK → communes.id_com |
| `email` | VARCHAR(191) | UNIQUE |
| `password` | VARCHAR(255) | Hash bcrypt |
| `name` | VARCHAR(255) | Nombre |
| `last_name` | VARCHAR(255) | Apellido |
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
| `request_body` | TEXT | Body sanitizado (sin password/token) |
| `response_status` | SMALLINT | HTTP status code |
| `response_body` | TEXT | Respuesta (max 1000 chars) |
| `created_at` | DATETIME | Timestamp del log |

---

## Servicios disponibles

### Rutas API

| Método | Endpoint | Descripción | Autenticación |
|--------|----------|-------------|---------------|
| `POST` | `/api/login` | Login y generación de token | No |
| `POST` | `/api/customers` | Registrar cliente | Sí (Bearer token) |
| `GET` | `/api/customers` | Consultar cliente | Sí (Bearer token) |
| `DELETE` | `/api/customers/{dni}` | Eliminar cliente (lógico) | Sí (Bearer token) |

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
| `ValidateCustomerStoreMiddleware` | `POST /api/customers` | Valida campos obligatorios, existencia de region/commune y relación entre ellas. |
| `ValidateCustomerQueryMiddleware` | `GET /api/customers` | Valida que se envíe al menos `dni` o `email` como query param. |
| `ValidateCustomerDeleteMiddleware` | `DELETE /api/customers/{dni}` | Valida que el parámetro `dni` esté presente. |

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
│   │   │   └── CustomerController.php
│   │   └── Middleware/
│   │       ├── LoggingMiddleware.php
│   │       ├── MethodMiddleware.php
│   │       ├── TokenMiddleware.php
│   │       ├── ValidateCustomerStoreMiddleware.php
│   │       ├── ValidateCustomerQueryMiddleware.php
│   │       └── ValidateCustomerDeleteMiddleware.php
│   └── Models/
│       ├── Commune.php
│       ├── Customer.php
│       ├── Log.php
│       ├── Region.php
│       └── Token.php
├── bootstrap/
│   └── app.php
├── config/
│   └── database.php
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
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
