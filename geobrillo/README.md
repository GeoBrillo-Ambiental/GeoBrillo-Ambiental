# GeoBrillo Ambiental — 2da Entrega (Programación Full Stack)

Sistema de gestión de residuos urbanos, reestructurado como **3
aplicaciones independientes** (arquitectura de microservicios), tal
como exige la guía oficial de Docker de la cátedra:

```
geobrillo/
├── docker-compose.yml          Orquestador general
├── README.md                   Este archivo
├── database/
│   └── schema.sql               Script completo de la base de datos
├── frontend/                    Landing Page + app de usuarios (HTML+CSS+JS puro, Nginx)
├── api-usuarios/                API REST: autenticación, registro, CRUD de Usuario (PHP+Apache)
├── api-gestion/                 API REST: CRUD de Camión, Contenedor, Centro y Maquinaria (PHP+Apache)
└── tests/                       Tests unitarios y de integración
```

El **frontend nunca accede a la base de datos**: todo pasa por las
APIs vía `fetch`/AJAX, devolviendo JSON. Esta separación permite que
el tribunal levante todo el sistema con un solo comando de Docker, sin
instalar PHP ni MySQL localmente.

## 🔐 Autenticación y roles (regla central del proyecto)

El login se hace por **token** (no por cookies de sesión), porque cada
microservicio corre en un puerto distinto:

1. El usuario inicia sesión en `login.html` → `api-usuarios/login.php`.
2. Si las credenciales son correctas, la API genera un token random,
   lo guarda en la tabla `Sesion` y lo devuelve en el JSON.
3. El frontend guarda el token en `localStorage` y lo reenvía en cada
   petición a `api-usuarios` o `api-gestion` (como parámetro `token`).
4. Cada endpoint protegido valida el token contra la tabla `Sesion`
   (que ambas APIs comparten porque usan la misma base de datos).

**Regla de negocio obligatoria:** un usuario con rol **Ciudadano**
puede iniciar sesión, pero **no tiene acceso a ningún CRUD** (ni
Usuarios, ni Camiones, ni Contenedores, ni Centros/Maquinaria). Sus
únicos permisos son:
- Ver el **Mapa** (`mapa.html`, público).
- **Registrarse** e **iniciar sesión**.
- Ver la información de la empresa/software (sección "Quiénes somos"
  del home).
- Usar el **formulario de contacto**.

Cualquier otro rol (Chofer, Operador, Cuadrilla, Administrador) sí
puede gestionar los 4 CRUD. Esta regla está aplicada **dos veces**:

- **Del lado del cliente** (`frontend/js/auth.js`): oculta las
  opciones de gestión del menú y redirige si alguien entra a mano a
  una URL de gestión sin permisos. Es solo una mejora de experiencia.
- **Del lado del servidor** (`middleware/Auth.php` en ambas APIs):
  esta es la protección real y obligatoria. Cualquier intento de
  llamar a un endpoint de gestión sin sesión responde `401
  Unauthorized`, y si la sesión es de un Ciudadano responde `403
  Forbidden` — sin importar lo que haga el frontend.

Después de iniciar sesión (con cualquier rol), **siempre se vuelve al
Home (`index.html`)**, nunca directo a una pantalla de gestión. El
propio Home arma el menú dinámicamente según el rol devuelto por
`api-usuarios/sesion.php`.

## 🔌 Endpoints (APIs)

### `api-usuarios` (puerto 8081 con Docker)

| Endpoint | Método | Protección | Función |
|---|---|---|---|
| `login.php` | POST (JSON) | Público | Devuelve un token si las credenciales son válidas |
| `logout.php` | POST | Público | Invalida un token |
| `sesion.php` | GET | Público (requiere token) | "Quién soy": usado por el frontend para armar el menú |
| `registro.php` | POST | Público | Alta de usuario (cualquier rol, incluido Ciudadano) |
| `usuarioapi.php` | GET/POST | **Login + rol ≠ Ciudadano** | `listar`, `insertar`, `actualizar`, `eliminar` |

### `api-gestion` (puerto 8082 con Docker)

| Endpoint | Método | Protección | Función |
|---|---|---|---|
| `camionapi.php` | GET/POST | **Login + rol ≠ Ciudadano** | CRUD de Camión |
| `contenedorapi.php` | GET/POST | **Login + rol ≠ Ciudadano** | CRUD de Contenedor |
| `centroapi.php` | GET/POST | **Login + rol ≠ Ciudadano** | CRUD de Centro de Acopio/Vertedero |
| `maquinariaapi.php` | GET/POST | **Login + rol ≠ Ciudadano** | CRUD de Maquinaria básica |

Todos los endpoints de gestión aceptan `?accion=listar|insertar|actualizar|eliminar`
y devuelven siempre `{"exito": true/false, "mensaje": "...", "data": [...]}`.

## 🐳 Puesta en marcha con Docker

Requisito: tener Docker Desktop (o Docker Engine + Compose) instalado.

```bash
git clone <repositorio>
cd geobrillo

# 1. Levantar todo el entorno
docker compose up --build -d

# 2. Verificar que los contenedores estén corriendo
docker compose ps

# 3. Crear el usuario Administrador de prueba (una sola vez)
docker compose exec api-usuarios php database/crear_admin.php
```

Acceso a los servicios:
- **Frontend:** http://localhost:8080
- **API Usuarios:** http://localhost:8081
- **API Gestión:** http://localhost:8082
- **MySQL:** localhost:3306 (usuario `sigeru_user` / clave `sigeru_password_123`)

Usuario administrador de prueba: `admin@eco.com` / `1234`

Para detener el entorno:

```bash
docker compose down
```

## 🧪 Testing

Ver `tests/README.md`. Resumen:

```bash
# Unitarios (no requieren Docker corriendo)
php tests/unit/run_all.php

# De integración (requieren docker compose up)
php tests/test_login.php
php tests/test_permisos_ciudadano.php
```

`test_permisos_ciudadano.php` verifica automáticamente que un
Ciudadano reciba `403 Forbidden` al intentar gestionar cualquier CRUD.

## 🗄️ Base de datos

`database/schema.sql` contiene el modelo completo entregado por la
cátedra, con estos agregados justificados (detallados como comentario
al inicio del propio archivo):

1. `AUTO_INCREMENT` en las claves primarias.
2. `Usuario.Password` ampliada a `varchar(255)` para contraseñas
   cifradas con `password_hash()`.
3. Tabla nueva **`Sesion`**: guarda los tokens de autenticación
   (necesaria por la arquitectura de microservicios sin cookies).
4. Tabla nueva **`Maquinaria`**: pedida explícitamente por la consigna
   de la 2da entrega ("CRUD de centros de acopio y maquinaria
   básica"), asociada a `CAcopioVertedero`.
5. Rol **Administrador** agregado al seed de `Rol`.

## ✅ Checklist de la 2da Entrega cubierto

- [x] Registro e inicio de sesión completo (con token).
- [x] CRUD completo de Usuarios — alta, listado, **edición (modal)** y baja (todos los roles).
- [x] CRUD completo de Contenedores — alta, listado, **edición (modal)** y baja.
- [x] CRUD completo de Camiones (flota) — alta, listado, **edición (modal)** y baja.
- [x] CRUD completo de Centros de Acopio y Maquinaria básica — alta, listado, **edición (modal)** y baja, para ambas entidades.
- [x] Endpoints para los 4 CRUD mencionados.
- [x] Persistencia de datos con base de datos real (MySQL + PDO).
- [x] Seguridad y separación de endpoints por rol (Ciudadano bloqueado).
- [x] Testing de la API mediante código (unitarios + integración).
- [x] Archivos para ejecutar todo con Docker (`docker-compose.yml` +
      3 `Dockerfile`).
- [x] Datos de prueba cargados en la base (`schema.sql`).

## Notas

- La carpeta `frontend/image/` quedó vacía: hay que volver a copiar
  ahí el logo y las fotos usadas en `index.html`, ya que no forman
  parte de este código fuente.
- El mapa (`mapa.html`) usa **Leaflet + OpenStreetMap**, siguiendo la
  guía del curso, y es accesible para cualquier persona (incluido un
  Ciudadano sin loguearse).
- `api-recoleccion` (mencionada en el detalle de implementación del
  proyecto para rutas y logística de flota) queda fuera del alcance
  de esta 2da entrega: por ahora Camión vive dentro de `api-gestion`,
  como indica la guía oficial de Docker para este hito (que solo pide
  `api-usuarios` y `api-gestion`). Puede separarse en una entrega
  posterior siguiendo el mismo patrón.
