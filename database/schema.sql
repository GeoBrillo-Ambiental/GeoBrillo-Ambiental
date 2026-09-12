-- =====================================================================
-- GeoBrillo Ambiental - Esquema de Base de Datos (2da Entrega)
-- Basado en el modelo entregado por la cátedra (Proyecto_ESI_2026)
-- =====================================================================
-- Cambios respecto al DDL original entregado (todos justificados):
--
--   1) AUTO_INCREMENT en las claves primarias, para que los CRUD puedan
--      insertar sin calcular el próximo ID a mano.
--
--   2) Usuario.Password ampliada a varchar(255): las contraseñas se
--      guardan cifradas con password_hash() (bcrypt = 60 caracteres).
--
--   3) NUEVA tabla Sesion: como el sistema ahora está separado en
--      microservicios (frontend estático + api-usuarios + api-gestion,
--      cada uno en su propio contenedor Docker), el login ya no puede
--      usar sesiones de PHP tradicionales (cookies), porque cada API
--      corre en un dominio/puerto distinto. En su lugar se usa
--      autenticación por TOKEN: al iniciar sesión se genera un token
--      random que se guarda en esta tabla y el frontend lo reenvía en
--      cada petición a las APIs protegidas (tal como explica la guía
--      "Testing de APIs y Backoffice": "muchas APIs devuelven un token
--      de sesión que debe enviarse en las pruebas posteriores").
--
--   4) NUEVA tabla Maquinaria: la 2da entrega pide explícitamente
--      "CRUD de centros de acopio y maquinaria básica". El modelo
--      entregado no incluía una tabla para maquinaria, así que se
--      agregó una mínima, asociada a un Centro de Acopio/Vertedero.
--
--   5) Se agregó el rol "Administrador" al seed de Rol (además de los
--      4 roles que ya pedía el formulario de registro), porque el
--      Backoffice de Administración (6.6) requiere un rol con acceso
--      total al sistema.
--
-- Todo lo demás (nombres de tablas, columnas y relaciones) respeta
-- exactamente el modelo entregado.
-- =====================================================================

DROP DATABASE IF EXISTS geobrillo;
CREATE DATABASE geobrillo CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci;
USE geobrillo;

-- ---------------------------------------------------------------------
-- Rol
-- ---------------------------------------------------------------------
CREATE TABLE Rol (
  Id_Rol       INT AUTO_INCREMENT PRIMARY KEY,
  Nombre_Rol   VARCHAR(20)  NOT NULL,
  Descripcion  VARCHAR(120)
);

-- ---------------------------------------------------------------------
-- Zona
-- ---------------------------------------------------------------------
CREATE TABLE Zona (
  Id_Zona  INT AUTO_INCREMENT PRIMARY KEY,
  NomZona  VARCHAR(20) NOT NULL
);

-- ---------------------------------------------------------------------
-- Centro de Acopio / Vertedero
-- ---------------------------------------------------------------------
CREATE TABLE CAcopioVertedero (
  Id_Centro    INT AUTO_INCREMENT PRIMARY KEY,
  NombreC      VARCHAR(50) NOT NULL,
  DireccionC   VARCHAR(60) NOT NULL,
  TipoCentro   VARCHAR(20) NOT NULL,
  CapTotal     INT NOT NULL,
  CapActual    INT NOT NULL
);

-- ---------------------------------------------------------------------
-- Maquinaria (NUEVA - ver nota 4 al inicio del archivo)
-- ---------------------------------------------------------------------
CREATE TABLE Maquinaria (
  Id_Maquinaria  INT AUTO_INCREMENT PRIMARY KEY,
  NombreMaq      VARCHAR(30) NOT NULL,
  TipoMaq        VARCHAR(30) NOT NULL,
  EstadoMaq      VARCHAR(20) NOT NULL,
  Id_Centro      INT NOT NULL,

  CONSTRAINT FK_Maquinaria_Centro
    FOREIGN KEY (Id_Centro) REFERENCES CAcopioVertedero (Id_Centro)
);

-- ---------------------------------------------------------------------
-- Camion
-- ---------------------------------------------------------------------
CREATE TABLE Camion (
  Id_Camion  INT AUTO_INCREMENT PRIMARY KEY,
  EstadoCam  VARCHAR(20) NOT NULL,
  Modelo     VARCHAR(20),
  C_Carga    INT NOT NULL
);

-- ---------------------------------------------------------------------
-- Cuadrilla (depende de Rol y Camion)
-- ---------------------------------------------------------------------
CREATE TABLE Cuadrilla (
  Id_Cuadrilla  INT AUTO_INCREMENT PRIMARY KEY,
  Dia           VARCHAR(20),
  Horario       VARCHAR(20),
  Id_Rol        INT NOT NULL,
  Id_Rol2       INT NOT NULL,
  Id_Camion     INT NOT NULL,

  CONSTRAINT FK_Cuadrilla_Rol
    FOREIGN KEY (Id_Rol) REFERENCES Rol (Id_Rol),
  CONSTRAINT FK_Cuadrilla_Rol2
    FOREIGN KEY (Id_Rol2) REFERENCES Rol (Id_Rol),
  CONSTRAINT FK_Cuadrilla_Camion
    FOREIGN KEY (Id_Camion) REFERENCES Camion (Id_Camion)
);

-- ---------------------------------------------------------------------
-- Ruta (depende de Zona, CAcopioVertedero y Cuadrilla)
-- ---------------------------------------------------------------------
CREATE TABLE Ruta (
  Id_Ruta       INT AUTO_INCREMENT PRIMARY KEY,
  DiaHora       VARCHAR(40) NOT NULL,
  Id_Zona       INT NOT NULL,
  Id_Centro     INT NOT NULL,
  Id_Cuadrilla  INT NOT NULL,

  CONSTRAINT FK_Ruta_Zona
    FOREIGN KEY (Id_Zona) REFERENCES Zona (Id_Zona),
  CONSTRAINT FK_Ruta_CAcopioVertedero
    FOREIGN KEY (Id_Centro) REFERENCES CAcopioVertedero (Id_Centro),
  CONSTRAINT FK_Ruta_Cuadrilla
    FOREIGN KEY (Id_Cuadrilla) REFERENCES Cuadrilla (Id_Cuadrilla)
);

-- ---------------------------------------------------------------------
-- Contenedor (depende de Ruta)
-- ---------------------------------------------------------------------
CREATE TABLE Contenedor (
  Id_Contenedor  INT AUTO_INCREMENT PRIMARY KEY,
  EstadoCont     VARCHAR(20) NOT NULL,
  CapContenedor  VARCHAR(20) NOT NULL,
  TipoReciduo    VARCHAR(20) NOT NULL,
  Id_Ruta        INT NOT NULL,

  CONSTRAINT FK_Contenedor_Ruta
    FOREIGN KEY (Id_Ruta) REFERENCES Ruta (Id_Ruta)
);

-- ---------------------------------------------------------------------
-- Usuario (depende de Rol y Cuadrilla)
-- ---------------------------------------------------------------------
CREATE TABLE Usuario (
  Id_Usuario    INT AUTO_INCREMENT PRIMARY KEY,
  Pri_Nom       VARCHAR(20)  NOT NULL,
  Pri_Ape       VARCHAR(20)  NOT NULL,
  Nom_Usuario   VARCHAR(20)  NOT NULL,
  Password      VARCHAR(255) NOT NULL, -- ver nota 2 al inicio del archivo
  Email         VARCHAR(20),
  Id_Rol        INT NOT NULL,
  Id_Cuadrilla  INT,

  CONSTRAINT FK_Usuario_Rol
    FOREIGN KEY (Id_Rol) REFERENCES Rol (Id_Rol),
  CONSTRAINT FK_Usuario_Cuadrilla
    FOREIGN KEY (Id_Cuadrilla) REFERENCES Cuadrilla (Id_Cuadrilla)
);

-- ---------------------------------------------------------------------
-- Sesion (NUEVA - ver nota 3 al inicio del archivo)
-- ---------------------------------------------------------------------
CREATE TABLE Sesion (
  Id_Sesion         INT AUTO_INCREMENT PRIMARY KEY,
  Token             VARCHAR(64) NOT NULL UNIQUE,
  Id_Usuario        INT NOT NULL,
  Fecha_Creacion    DATETIME NOT NULL,
  Fecha_Expiracion  DATETIME NOT NULL,

  CONSTRAINT FK_Sesion_Usuario
    FOREIGN KEY (Id_Usuario) REFERENCES Usuario (Id_Usuario)
);

-- ---------------------------------------------------------------------
-- Incidencia (depende de Cuadrilla, Usuario y Contenedor)
-- ---------------------------------------------------------------------
CREATE TABLE Incidencia (
  Id_Incidencia  INT AUTO_INCREMENT PRIMARY KEY,
  EstadoInc      VARCHAR(20) NOT NULL,
  Descripcion    VARCHAR(120),
  FchaReportado  DATE NOT NULL,
  FchaResuelto   DATE,
  Id_Cuadrilla   INT NULL,
  Id_Usuario     INT NOT NULL,
  Id_Contenedor  INT NOT NULL,

  CONSTRAINT FK_Incidencia_Cuadrilla
    FOREIGN KEY (Id_Cuadrilla) REFERENCES Cuadrilla (Id_Cuadrilla),
  CONSTRAINT FK_Incidencia_Usuario
    FOREIGN KEY (Id_Usuario) REFERENCES Usuario (Id_Usuario),
  CONSTRAINT FK_Incidencia_Contenedor
    FOREIGN KEY (Id_Contenedor) REFERENCES Contenedor (Id_Contenedor)
);

-- =====================================================================
-- DATOS DE PRUEBA (seed)
-- =====================================================================

INSERT INTO Rol (Nombre_Rol, Descripcion) VALUES
 ('Ciudadano',     'Usuario que reporta incidencias y consulta el servicio. Sin acceso a los CRUD de gestión.'),
 ('Chofer',        'Conduce el camión asignado a una cuadrilla'),
 ('Operador',      'Opera en el centro de acopio o vertedero'),
 ('Cuadrilla',     'Integrante de una cuadrilla de recolección'),
 ('Administrador', 'Acceso total al sistema (Backoffice)');

INSERT INTO Zona (NomZona) VALUES
 ('Centro'), ('Pocitos'), ('Buceo'), ('Cordón');

INSERT INTO CAcopioVertedero (NombreC, DireccionC, TipoCentro, CapTotal, CapActual) VALUES
 ('Planta Felipe Cardoso', 'Camino Felipe Cardoso s/n', 'Vertedero', 10000, 6500),
 ('Centro de Acopio Buceo', 'Av. Italia 3800', 'Acopio', 2000, 800);

INSERT INTO Maquinaria (NombreMaq, TipoMaq, EstadoMaq, Id_Centro) VALUES
 ('Pala cargadora frontal', 'Pala mecánica', 'Operativa', 1),
 ('Compactadora de residuos', 'Compactadora', 'En mantenimiento', 1),
 ('Balanza industrial', 'Balanza', 'Operativa', 2);

INSERT INTO Camion (EstadoCam, Modelo, C_Carga) VALUES
 ('Disponible', 'Frontal', 10),
 ('En Reparación', 'Lateral', 8);

INSERT INTO Cuadrilla (Dia, Horario, Id_Rol, Id_Rol2, Id_Camion) VALUES
 ('Lunes', '08:00-14:00', 2, 4, 1),
 ('Martes', '14:00-20:00', 2, 4, 2);

INSERT INTO Ruta (DiaHora, Id_Zona, Id_Centro, Id_Cuadrilla) VALUES
 ('Lunes 08:00', 1, 1, 1),
 ('Martes 14:00', 2, 2, 2);

INSERT INTO Contenedor (EstadoCont, CapContenedor, TipoReciduo, Id_Ruta) VALUES
 ('Funcional', '2', 'Reciclable', 1),
 ('Desbordado', '3', 'Mezclados', 2);

-- El usuario Administrador de prueba se crea desde
-- api-usuarios/database/crear_admin.php (la contraseña se cifra con
-- password_hash, no se puede insertar en texto plano de forma segura
-- desde este script SQL). Datos: admin@eco.com / 1234
