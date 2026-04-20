<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

# GIF API Challenge

API REST desarrollada en **Laravel 11** como solucion al challenge tecnico.
La aplicacion implementa:

* Autenticacion por token con expiracion
* Integracion con Giphy
* Gestion de GIFs favoritos
* Auditoria de todas las interacciones

La arquitectura sigue el enfoque **Hexagonal (Ports & Adapters)**, separando dominio, aplicacion e infraestructura.

---

## Stack

* PHP 8.3+
* Laravel 11
* MySQL 8.4
* Docker (Laravel Sail)
* PHPUnit
* Giphy API

---

## Arquitectura

La aplicacion esta organizada por modulos:

```text
app/
+-- Auth/
+-- Gif/
+-- Shared/
```

Cada modulo sigue la estructura:

```text
Application/
Domain/
Infrastructure/
```

### Principios aplicados

* **Domain**: no depende de Laravel, HTTP ni infraestructura
* **Application**: orquesta casos de uso
* **Infrastructure**: implementa detalles (DB, HTTP, Giphy, middleware)
* **Controllers**: solo adaptan HTTP -> UseCase
* **Dependencias invertidas**: la aplicacion depende de interfaces (ports)

---

## Setup

### 1. Clonar proyecto

```bash
git clone <repo>
cd gif-api
```

### 2. Configurar entorno

```bash
cp .env.example .env
```

Configurar:

```env
GIPHY_API_KEY=your_api_key
```

---

### 3. Instalar dependencias

Instalar dependencias con Composer:

```bash
composer install
```

> El proyecto usa Laravel Sail. El `compose.yaml` construye el servicio PHP usando el Dockerfile oficial de Sail ubicado en `vendor/laravel/sail/runtimes/8.5/Dockerfile`.

---

### 4. Levantar Docker

```bash
docker compose up -d
```

---

### 5. Generar key

```bash
docker compose exec laravel.test php artisan key:generate
```

---

### 6. Migrar base de datos

```bash
docker compose exec laravel.test php artisan migrate --seed
```

---

### Nota sobre Sail

La carpeta `vendor/` debe existir antes de levantar Docker, porque Laravel Sail guarda ahi el Dockerfile que usa `compose.yaml`.

---

## Usuario de prueba

```text
email: test@example.com
password: password
```

---

## Autenticacion

El endpoint:

```http
POST /api/login
```

Genera un token aleatorio que:

* se guarda hasheado (`sha256`) en base de datos
* se devuelve **solo una vez**
* expira a los **30 minutos**

### Uso

```http
Authorization: Bearer TOKEN
```

---

## Endpoints

### Login

```http
POST /api/login
```

```json
{
  "email": "test@example.com",
  "password": "password"
}
```

---

### Buscar GIFs

```http
GET /api/gifs/search?query=cat&limit=5&offset=0
Authorization: Bearer TOKEN
```

---

### Obtener GIF por ID

```http
GET /api/gifs/{id}
Authorization: Bearer TOKEN
```

---

### Guardar favorito

```http
POST /api/gifs/favorites
Authorization: Bearer TOKEN
```

```json
{
  "gif_id": "901mxGLGQN2PyCQpoc",
  "alias": "My favorite GIF"
}
```

> El usuario se obtiene del token autenticado.

---

## Auditoria

Todas las requests pasan por un middleware que registra en `audit_logs`:

* endpoint (`path`)
* metodo HTTP
* request body (sanitizado)
* response body
* status code
* IP
* usuario autenticado

Caracteristicas:

* Implementado como **middleware transversal**
* No ensucia controllers ni casos de uso
* No rompe la request si falla el logging

---

## Manejo de errores

Las excepciones del dominio extienden `DomainException` y definen su propio status HTTP.

Un `ApiExceptionMapper` central traduce errores a respuestas JSON.

Ejemplos:

* `401` -> credenciales invalidas / no autenticado
* `404` -> recurso no encontrado
* `502` -> error con proveedor externo
* `422` -> validaciones

---

## Tests

Ejecutar todos:

```bash
docker compose exec laravel.test php artisan test
```

Ejecutar subset:

```bash
docker compose exec laravel.test php artisan test --filter=Login
docker compose exec laravel.test php artisan test --filter=Gif
docker compose exec laravel.test php artisan test --filter=AuditMiddleware
```

Incluye:

* Tests de UseCases
* Tests de endpoints HTTP

---

## Postman

Importar:

```text
postman/gif-api.postman_collection.json
postman/gif-api.postman_environment.json
```

Ejecutar primero:

```text
Auth / Login
```

El token se guarda automaticamente en el environment.

---

## Diagramas

Los diagramas solicitados para la entrega estan en:

```text
docs/use-cases.md
docs/sequence.md
docs/erd.md
```

Incluyen:

* Diagrama de casos de uso
* Diagramas de secuencia de los casos principales
* Diagrama de datos / DER

---

## Decisiones de diseno

* No se utiliza Passport ni Sanctum -> se implementa token simple controlado
* Tokens se almacenan hasheados por seguridad
* Integracion con Giphy desacoplada mediante `GifProviderInterface`
* Persistencia desacoplada mediante repositorios
* Auditoria implementada como middleware (cross-cutting concern)

---

## Estado

La API cubre:

* autenticacion
* proteccion de endpoints
* integracion externa
* persistencia
* auditoria
* tests

---

## Notas finales

El foco de la solucion esta en:

* separacion de responsabilidades
* bajo acoplamiento
* claridad en la arquitectura

---
