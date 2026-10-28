# GeoBrillo Ambiental — Base de datos (MySQL 8)

Esta carpeta es la **base de datos del proyecto**, independiente de las aplicaciones (`geobrillo/`). Según la letra (sección 8), MySQL 8 va instalado en el **servidor de base de datos** y las aplicaciones, en contenedores Docker, se conectan a él por red.

## Archivos

| Archivo | Para qué sirve |
|---|---|
| `schema.sql` | DDL completo (14 tablas, claves foráneas) + datos de prueba. Los cambios respecto al modelo de la cátedra están justificados en su encabezado. |
| `setup_servidor.sql` | Crea el usuario `geobrillo_app` con permisos mínimos sobre la base `geobrillo`. |
| `docker-compose.yml` | Opcional: levanta un MySQL 8 de pruebas para simular el servidor de base de datos en una PC. |

## Instalación en el servidor de base de datos

```bash
mysql -u root -p < setup_servidor.sql
mysql -u root -p < schema.sql
```

- `schema.sql` empieza con `DROP DATABASE IF EXISTS geobrillo;`: recrea la base desde cero.
- Cambiar la clave `cambiar_esta_clave` de `setup_servidor.sql` antes de usarlo en producción.
- En `mysqld.cnf` poner `bind-address = 0.0.0.0` (o la IP de la red interna) y en el firewall abrir el puerto 3306/tcp solo para el servidor de aplicaciones.

## Servidor de pruebas en una PC (opcional)

```bash
docker compose up -d
```

Queda MySQL escuchando en el puerto 3306 con la base cargada (usuario `sigeru_user`, clave `sigeru_password_123`). En el proyecto `geobrillo/`, el archivo `.env` debe tener `DB_HOST=host.docker.internal`, `DB_USER=sigeru_user` y `DB_PASS=sigeru_password_123`.

Para volver a empezar de cero: `docker compose down -v` y `docker compose up -d`.

## Usuarios de prueba (clave `1234`)

`admin@eco.com` (Administrador), `ciudadano@eco.com` (Ciudadano), `chofer@eco.com` (Chofer).

## Dump y respaldo

```bash
mysqldump -h <servidor> -u root -p geobrillo > dump_geobrillo.sql
```

## Tablas

`Rol, Zona, CAcopioVertedero, Maquinaria, Camion, Cuadrilla, Ruta, Contenedor, Usuario, Sesion, Incidencia, Notificacion, Recoleccion, Contacto`.
