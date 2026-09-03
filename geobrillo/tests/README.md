# Tests — GeoBrillo Ambiental

Siguiendo la guía "Testing de APIs y Backoffice", el proyecto tiene dos
tipos de pruebas:

## 1. Tests unitarios (no requieren base de datos ni Docker)

Prueban los **Controladores** de forma aislada, inyectándoles un modelo
falso en memoria en lugar del modelo real que usa PDO. Así se verifica
la lógica de negocio (validaciones, reglas) sin depender de MySQL.

```bash
php tests/unit/UsuarioControladorTest.php
php tests/unit/CamionControladorTest.php

# o todos juntos:
php tests/unit/run_all.php
```

## 2. Tests de integración (requieren el sistema corriendo)

Prueban las APIs reales, tal como las consume el frontend: hacen una
petición HTTP de verdad y verifican el código de estado (200/401/403)
y el JSON de respuesta.

Primero levantar el entorno:

```bash
docker compose up --build -d
docker compose exec api-usuarios php database/crear_admin.php
```

Luego correr:

```bash
php tests/test_login.php
php tests/test_permisos_ciudadano.php
```

`test_permisos_ciudadano.php` es el más importante del proyecto: es el
que verifica automáticamente la regla de negocio central pedida por la
cátedra ("un Ciudadano no puede tener control en ningún CRUD"),
comprobando que la API responde `403 Forbidden` cuando un Ciudadano
intenta listar usuarios o insertar un camión, `401 Unauthorized` cuando
no hay token, y `200 OK` cuando el que pide la acción es un
Administrador.
