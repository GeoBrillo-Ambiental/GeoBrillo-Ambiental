# GeoBrillo Ambiental — Programación Full Stack (3ra entrega)

Sistema de gestión de residuos urbanos de Montevideo, con arquitectura de microservicios. Cada aplicación es una base de código independiente:

```
geobrillo/                      Aplicaciones (corren en contenedores Docker)
├── docker-compose.yml
├── .env.example                Datos de conexión a la base de datos
├── frontend/                   Landing page y aplicación de usuarios (HTML + CSS + JS, Nginx)
├── api-usuarios/               API REST: registro con aprobación, login, usuarios y roles
├── api-gestion/                API REST: contenedores, centros, maquinaria, incidencias, alertas, estadísticas, mapa, contacto
├── api-recoleccion/            API REST: camiones, cuadrillas, rutas y recolecciones
└── tests/                      Tests unitarios y de integración

geobrillo-bd/                   Base de datos MySQL 8 (se instala en el servidor de base de datos)
├── schema.sql                  DDL + datos de prueba
├── setup_servidor.sql          Usuario de las APIs
└── docker-compose.yml          Servidor MySQL de pruebas (opcional)
```

Cada API sigue MVC (modelos, controladores y endpoints), usa PHP 8 con PDO y consultas preparadas, y responde JSON. El frontend nunca accede a la base de datos: solo consume las APIs con `fetch`.

## Despliegue

La letra (sección 8) pide MySQL 8 en el servidor de base de datos y cada aplicación en contenedores Docker. Por eso la base **no** forma parte del `docker-compose.yml` de las aplicaciones:

```
Servidor de aplicaciones (Docker)                    Servidor de base de datos
┌──────────────────────────────────┐                ┌──────────────────────┐
│ frontend         :8080           │                │  MySQL 8             │
│ api-usuarios     :8081  ──PDO────┼──── 3306 ─────►│  base "geobrillo"    │
│ api-gestion      :8082           │                │                      │
│ api-recoleccion  :8083           │                └──────────────────────┘
└──────────────────────────────────┘
```

1. **Base de datos** (ver `geobrillo-bd/README.md`):
   ```bash
   mysql -u root -p < setup_servidor.sql
   mysql -u root -p < schema.sql
   ```
2. **Aplicaciones**:
   ```bash
   cp .env.example .env      # completar DB_HOST, DB_USER y DB_PASS
   docker compose up --build -d
   docker compose ps
   ```

Para probar sin el servidor de la Escuela: `docker compose up -d` dentro de `geobrillo-bd/` levanta un MySQL de pruebas, y en `.env` se usa `DB_HOST=host.docker.internal`.

Servicios: frontend http://localhost:8080 · api-usuarios :8081 · api-gestion :8082 · api-recoleccion :8083.

Usuarios de prueba (clave `1234`): `admin@eco.com` (Administrador), `ciudadano@eco.com` (Ciudadano), `chofer@eco.com` (Chofer).

## Roles y seguridad

- Login por **token**: `api-usuarios/login.php` lo genera y lo guarda en la tabla `Sesion`; el frontend lo envía en cada petición y cada API lo valida contra esa tabla.
- **Registro con aprobación**: el registro público como **Ciudadano** queda activo al instante; los roles de personal (Chofer, Operador, Cuadrilla) quedan `Pendiente` hasta que un **Administrador** los apruebe o rechace (en Usuarios). El registro público nunca permite el rol Administrador.
- **Ciudadano**: ve el mapa, consulta en solo lectura contenedores, camiones y centros, y reporta incidencias. No puede insertar, editar ni eliminar en ningún CRUD (la API responde `403`).
- **Chofer, Operador, Cuadrilla**: gestionan los CRUD, incidencias, rutas, panel e informes.
- **Administrador**: además crea, edita y elimina cuentas Administrador y resuelve solicitudes de registro.
- Sin token la API responde `401`. El frontend solo oculta opciones: la protección real está en el servidor.

## Endpoints

### api-usuarios

| Endpoint | Protección | Función |
|---|---|---|
| `login.php` | Público | Devuelve un token (solo usuarios `Activo`) |
| `logout.php`, `sesion.php` | Token | Cerrar sesión / datos de quién soy |
| `registro.php` | Público | Registro público (Ciudadano activo; personal `Pendiente`) |
| `usuarioapi.php` | Gestión | `listar`, `insertar`, `actualizar`, `eliminar` · `aprobar` y `rechazar` solo Administrador |

### api-gestion

| Endpoint | Protección | Función |
|---|---|---|
| `contenedorapi.php`, `centroapi.php`, `maquinariaapi.php` | `listar`: login · resto: gestión | CRUD (los contenedores y centros llevan coordenadas) |
| `incidenciaapi.php` | `reportar` y `listar`: login · resto: gestión | Reportar, asignar cuadrilla y cerrar |
| `notificacionapi.php` | Gestión | Alertas (`listar`, `marcarLeida`, `marcarTodas`) |
| `estadisticaapi.php` | Gestión | `panel` e `informe&tipo=abiertas\|cerradas` |
| `contactoapi.php` | `enviar`: público · resto: gestión | Formulario de contacto |
| `mapaapi.php` | Público | Contenedores, incidencias abiertas, centros y rutas |

### api-recoleccion

| Endpoint | Protección | Función |
|---|---|---|
| `camionapi.php`, `cuadrillaapi.php`, `recoleccionapi.php` | `listar`: login · resto: gestión | CRUD |
| `rutaapi.php` | `listar`: login · resto: gestión | CRUD + `asignarContenedores` (arma la ruta eligiendo contenedores y orden) |

Todas aceptan `?accion=...` y responden `{"exito": true/false, "mensaje": "...", "data": [...]}`.

## Funcionalidades (letra, 9.4)

- **API de recolección** en un servicio propio.
- **Incidencias** (estados Abierta / En curso / Resuelta): el ciudadano reporta; el contenedor pasa a Roto o Desbordado; gestión asigna cuadrilla y cierra, y el contenedor vuelve a Funcional.
- **Mapa interactivo** con contenedores por estado, incidencias abiertas, centros de acopio y rutas. La ubicación se elige con un clic en el mapa al crear o editar.
- **Editor de rutas**: se eligen a mano los contenedores de cada ruta, en orden, sobre el mapa.
- **Informes**: incidencias abiertas y cerradas, con exportación a CSV e impresión.
- **Panel de control**: top de contenedores desbordados del mes, zonas críticas, tiempo medio de resolución, frecuencia de recolección, kg por zona, estado de contenedores y contenedores de repuesto (inventario).
- **Alertas** por cada incidencia o mensaje de contacto.
- **Formulario de contacto** funcional, guardado en la base.

## Base de datos

`geobrillo-bd/schema.sql` respeta el modelo de la cátedra. Los agregados están justificados en su encabezado: autoincrementales, `Password` de 255, tablas `Sesion`, `Maquinaria`, `Notificacion`, `Recoleccion` y `Contacto`, coordenadas en contenedores y centros, `Incidencia.TipoProblema`, `Contenedor.OrdenRuta` y `Usuario.Estado`.

Para ver la base: `mysql -h <DB_HOST> -u <usuario> -p geobrillo`, o Adminer (`docker compose --profile herramientas up -d adminer`, http://localhost:8084).

## Tests

Ver `tests/README.md`.

```bash
php tests/unit/run_all.php          # unitarios, no requieren Docker ni base
php tests/test_entrega3.php         # integración, requieren las APIs levantadas
```

## Notas

- Imágenes del home en `frontend/image/` (falta `centros.jpg`).
- Cada carpeta de aplicación puede convertirse en su propio repositorio de la organización de GitHub.
