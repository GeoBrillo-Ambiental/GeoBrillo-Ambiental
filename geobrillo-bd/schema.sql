DROP DATABASE IF EXISTS geobrillo;
CREATE DATABASE geobrillo CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci;
USE geobrillo;

CREATE TABLE Rol (
  Id_Rol       INT AUTO_INCREMENT PRIMARY KEY,
  Nombre_Rol   VARCHAR(20)  NOT NULL,
  Descripcion  VARCHAR(120)
);

CREATE TABLE Zona (
  Id_Zona  INT AUTO_INCREMENT PRIMARY KEY,
  NomZona  VARCHAR(20) NOT NULL
);

CREATE TABLE CAcopioVertedero (
  Id_Centro    INT AUTO_INCREMENT PRIMARY KEY,
  NombreC      VARCHAR(50) NOT NULL,
  DireccionC   VARCHAR(60) NOT NULL,
  TipoCentro   VARCHAR(20) NOT NULL,
  CapTotal     INT NOT NULL,
  CapActual    INT NOT NULL,
  Latitud      DECIMAL(9,6) NULL,
  Longitud     DECIMAL(9,6) NULL
);

CREATE TABLE Maquinaria (
  Id_Maquinaria  INT AUTO_INCREMENT PRIMARY KEY,
  NombreMaq      VARCHAR(30) NOT NULL,
  TipoMaq        VARCHAR(30) NOT NULL,
  EstadoMaq      VARCHAR(20) NOT NULL,
  Id_Centro      INT NOT NULL,

  CONSTRAINT FK_Maquinaria_Centro
    FOREIGN KEY (Id_Centro) REFERENCES CAcopioVertedero (Id_Centro)
);

CREATE TABLE Camion (
  Id_Camion  INT AUTO_INCREMENT PRIMARY KEY,
  EstadoCam  VARCHAR(20) NOT NULL,
  Modelo     VARCHAR(20),
  C_Carga    INT NOT NULL
);

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

CREATE TABLE Contenedor (
  Id_Contenedor  INT AUTO_INCREMENT PRIMARY KEY,
  EstadoCont     VARCHAR(20) NOT NULL,
  CapContenedor  VARCHAR(20) NOT NULL,
  TipoReciduo    VARCHAR(20) NOT NULL,
  Ubicacion      VARCHAR(100) NULL,
  Latitud        DECIMAL(9,6) NULL,
  Longitud       DECIMAL(9,6) NULL,
  OrdenRuta      INT NULL,
  Id_Ruta        INT NULL,

  CONSTRAINT FK_Contenedor_Ruta
    FOREIGN KEY (Id_Ruta) REFERENCES Ruta (Id_Ruta)
);

CREATE TABLE Usuario (
  Id_Usuario    INT AUTO_INCREMENT PRIMARY KEY,
  Pri_Nom       VARCHAR(30)  NOT NULL,
  Pri_Ape       VARCHAR(30)  NOT NULL,
  Nom_Usuario   VARCHAR(30)  NOT NULL,
  Password      VARCHAR(255) NOT NULL,
  Email         VARCHAR(100),
  Estado        VARCHAR(20)  NOT NULL DEFAULT 'Activo',
  Id_Rol        INT NOT NULL,
  Id_Cuadrilla  INT,

  CONSTRAINT FK_Usuario_Rol
    FOREIGN KEY (Id_Rol) REFERENCES Rol (Id_Rol),
  CONSTRAINT FK_Usuario_Cuadrilla
    FOREIGN KEY (Id_Cuadrilla) REFERENCES Cuadrilla (Id_Cuadrilla)
);

CREATE TABLE Sesion (
  Id_Sesion         INT AUTO_INCREMENT PRIMARY KEY,
  Token             VARCHAR(64) NOT NULL UNIQUE,
  Id_Usuario        INT NOT NULL,
  Fecha_Creacion    DATETIME NOT NULL,
  Fecha_Expiracion  DATETIME NOT NULL,

  CONSTRAINT FK_Sesion_Usuario
    FOREIGN KEY (Id_Usuario) REFERENCES Usuario (Id_Usuario)
);

CREATE TABLE Incidencia (
  Id_Incidencia  INT AUTO_INCREMENT PRIMARY KEY,
  EstadoInc      VARCHAR(20) NOT NULL,
  Descripcion    VARCHAR(120),
  TipoProblema   VARCHAR(20) NULL,
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

CREATE TABLE Notificacion (
  Id_Notificacion  INT AUTO_INCREMENT PRIMARY KEY,
  Tipo             VARCHAR(20)  NOT NULL,
  Mensaje          VARCHAR(200) NOT NULL,
  Fecha            DATETIME     NOT NULL,
  Leida            TINYINT(1)   NOT NULL DEFAULT 0,
  Id_Incidencia    INT NULL,
  Id_Contenedor    INT NULL,

  CONSTRAINT FK_Notificacion_Incidencia
    FOREIGN KEY (Id_Incidencia) REFERENCES Incidencia (Id_Incidencia) ON DELETE SET NULL,
  CONSTRAINT FK_Notificacion_Contenedor
    FOREIGN KEY (Id_Contenedor) REFERENCES Contenedor (Id_Contenedor) ON DELETE SET NULL
);

CREATE TABLE Recoleccion (
  Id_Recoleccion  INT AUTO_INCREMENT PRIMARY KEY,
  Fecha           DATE NOT NULL,
  KgRecolectados  INT  NOT NULL,
  Observaciones   VARCHAR(100),
  Id_Ruta         INT NOT NULL,
  Id_Contenedor   INT NOT NULL,

  CONSTRAINT FK_Recoleccion_Ruta
    FOREIGN KEY (Id_Ruta) REFERENCES Ruta (Id_Ruta),
  CONSTRAINT FK_Recoleccion_Contenedor
    FOREIGN KEY (Id_Contenedor) REFERENCES Contenedor (Id_Contenedor)
);

CREATE TABLE Contacto (
  Id_Contacto  INT AUTO_INCREMENT PRIMARY KEY,
  Nombre       VARCHAR(60)  NOT NULL,
  Email        VARCHAR(100) NOT NULL,
  Telefono     VARCHAR(20)  NULL,
  Mensaje      VARCHAR(500) NOT NULL,
  Fecha        DATETIME     NOT NULL,
  Leido        TINYINT(1)   NOT NULL DEFAULT 0
);

INSERT INTO Rol (Nombre_Rol, Descripcion) VALUES
 ('Ciudadano',     'Usuario que reporta incidencias y consulta el servicio. Sin acceso a los CRUD de gestión.'),
 ('Chofer',        'Conduce el camión asignado a una cuadrilla'),
 ('Operador',      'Opera en el centro de acopio o vertedero'),
 ('Cuadrilla',     'Integrante de una cuadrilla de recolección'),
 ('Administrador', 'Acceso total al sistema (Backoffice)');

INSERT INTO Zona (NomZona) VALUES
 ('Centro'), ('Pocitos'), ('Buceo'), ('Cordón');

INSERT INTO CAcopioVertedero (NombreC, DireccionC, TipoCentro, CapTotal, CapActual, Latitud, Longitud) VALUES
 ('Planta Felipe Cardoso', 'Camino Felipe Cardoso s/n', 'Vertedero', 10000, 6500, -34.810000, -56.250000),
 ('Centro de Acopio Buceo', 'Av. Italia 3800', 'Acopio', 2000, 800, -34.888000, -56.130000);

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

INSERT INTO Contenedor (EstadoCont, CapContenedor, TipoReciduo, Ubicacion, Latitud, Longitud, OrdenRuta, Id_Ruta) VALUES
 ('Funcional',  '2', 'Reciclable', 'Plaza Independencia',      -34.906500, -56.201500, 1, 1),
 ('Desbordado', '3', 'Mezclados',  'Av. Brasil y Benito Blanco', -34.915000, -56.158000, 1, 2),
 ('Funcional',  '2', 'Orgánico',   '18 de Julio y Ejido',     -34.905000, -56.186000, 2, 1),
 ('Roto',       '2', 'Reciclable', 'Rambla de Pocitos',       -34.913000, -56.150000, 2, 2),
 ('Funcional',  '3', 'Mezclados',  'Av. Italia y Bvar. Artigas', -34.896000, -56.150000, 3, 2),
 ('Desbordado', '2', 'Orgánico',   'Mercado Agrícola',        -34.893000, -56.181000, 3, 1),
 ('Repuesto',   '2', 'Reciclable', 'Depósito municipal',       NULL, NULL, NULL, NULL),
 ('Repuesto',   '3', 'Mezclados',  'Depósito municipal',       NULL, NULL, NULL, NULL);

INSERT INTO Usuario (Pri_Nom, Pri_Ape, Nom_Usuario, Password, Email, Id_Rol, Id_Cuadrilla) VALUES
 ('Carla', 'Ciudadana', 'carla', '$2y$10$j/YzvGDxYktCCm0elzL2T.hM//h.k1YsCturvw17czQmppPhxAwSq', 'ciudadano@eco.com', 1, NULL),
 ('Carlos', 'Chofer', 'carlos', '$2y$10$j/YzvGDxYktCCm0elzL2T.hM//h.k1YsCturvw17czQmppPhxAwSq', 'chofer@eco.com', 2, 1);

INSERT INTO Usuario (Pri_Nom, Pri_Ape, Nom_Usuario, Password, Email, Id_Rol, Id_Cuadrilla) VALUES
 ('Admin', 'General', 'AdminGeneral', '$2y$10$j/YzvGDxYktCCm0elzL2T.hM//h.k1YsCturvw17czQmppPhxAwSq', 'admin@eco.com', 5, NULL);

INSERT INTO Incidencia (EstadoInc, Descripcion, TipoProblema, FchaReportado, FchaResuelto, Id_Cuadrilla, Id_Usuario, Id_Contenedor) VALUES
 ('Abierta',    'Desborda y hay bolsas en la vereda',  'Desbordado', CURDATE(), NULL, NULL, 1, 2),
 ('En curso',   'Tapa rota, no cierra',                'Roto',       CURDATE(), NULL, 2,    1, 4),
 ('Abierta',    'Lleno desde hace tres días',          'Desbordado', CURDATE(), NULL, NULL, 1, 6),
 ('Resuelta',   'Se desbordó por el fin de semana',    'Desbordado', DATE_SUB(CURDATE(), INTERVAL 3 DAY), CURDATE(), 1, 1, 2);

INSERT INTO Notificacion (Tipo, Mensaje, Fecha, Leida, Id_Incidencia, Id_Contenedor) VALUES
 ('Incidencia', 'Nueva incidencia: contenedor 2 Desbordado.', NOW(), 0, 1, 2),
 ('Incidencia', 'Nueva incidencia: contenedor 6 Desbordado.', NOW(), 0, 3, 6);

INSERT INTO Recoleccion (Fecha, KgRecolectados, Observaciones, Id_Ruta, Id_Contenedor) VALUES
 (CURDATE(), 180, 'Recolección normal', 1, 1),
 (CURDATE(), 240, 'Contenedor lleno',   2, 2),
 (DATE_SUB(CURDATE(), INTERVAL 2 DAY), 150, NULL, 1, 3);

