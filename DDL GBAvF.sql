CREATE TABLE Rol (
  Id_Rol int PRIMARY KEY NOT NULL,
  Nombre_Rol varchar (20) NOT NULL,
  Descripcion varchar (120)
  );
   
CREATE TABLE Zona (
    Id_Zona int PRIMARY KEY NOT NULL,
    NomZona varchar (20) NOT NULL
    );
    
CREATE TABLE CAcopioVertedero (
   	Id_Centro int PRIMARY KEY NOT NULL,
   	NombreC varchar (20) not NULL,
   	DireccionC varchar (40) not NULL,
   	TipoCentro varchar (20) NOT NULL,
   	CapTotal int not NULL,
   	CapActual int NOT NULL
    );
    
CREATE TABLE Ruta (
    Id_Ruta int PRIMARY KEY NOT NULL,
    DiaHora varchar (40) NOT NULL,
    Id_Zona int NOT NULL,	
    Id_Centro int NOT NULL,
  	Id_Cuadrilla int Not NULL,
  
  		CONSTRAINT FK_Ruta_Zona
    	FOREIGN KEY (Id_Zona)
    	REFERENCES Zona (Id_Zona),
    	CONSTRAINT FK_Ruta_CAcopioVertedero
    	FOREIGN KEY (Id_Centro)
    	REFERENCES CAcopioVertedero (Id_Centro), 
  		CONSTRAINT FK_Ruta_Cuadrilla
  		FOREIGN KEY (Id_Cuadrilla)
  		REFERENCES Cuadrilla (Id_Cuadrilla)
    );
    
  CREATE Table Contenedor (
  	Id_Contenedor int NOT NULL,
  	EstadoCont varchar (20) NOT NULL,
  	CapContenedor varchar (20) Not NULL,
  	TipoReciduo  varchar (20) NOT NULL,
  	Id_Ruta int NOT NULL,
  		CONSTRAINT FK_Contenedor_Ruta
  		FOREIGN KEY (Id_Ruta)
  		REFERENCES Ruta (Id_Ruta)
  );

  CREATE TABLE Camion (
    Id_Camion int PRIMARY KEY NOT NULL,
    EstadoCam varchar (20) NOT NULL,
    Modelo varchar (20),
    C_Carga int NOT NULL
   );
   
  CREATE TABLE Cuadrilla (
    Id_Cuadrilla int PRIMARY KEY NOT NULL,
    Dia Varchar (20),
    Horario varchar (20),
    Id_Rol int NOT NULL,
    Id_Rol2 int NOT NULL,
    Id_Camion int NOT NULL,
    	CONSTRAINT FK_Cuadrilla_Rol
    	FOREIGN KEY (Id_Rol)
    	References Rol (Id_Rol),
    	CONSTRAINT FK_Cuadrilla_Rol2
    	FOREIGN KEY (Id_Rol2)
    	References Rol (Id_Rol),
    	CONSTRAINT FK_Cuadrilla_Camion
    	FOREIGN KEY (Id_Camion)
    	REFERENCES Camion (Id_Camion)
   );
   
   CREATE TABLE Usuario (
  Id_Usuario int PRIMARY KEY NOT NULL,
  Pri_Nom varchar (20) NOT NULL,
  Pri_Ape Varchar (20) NOT NULL,
  Nom_Usuario varchar (20) not NULL,
  Password Varchar (20)NOT NULL,
  Email Varchar (20),
  Id_Rol int NOT NULL,
  Id_Cuadrilla int,
     
    CONSTRAINT FK_Usuario_Rol  
  	FOREIGN KEY (Id_Rol) 
  	REFERENCES Rol (Id_Rol),
  	CONSTRAINT FK_Usuario_Cuadrilla
  	FOREIGN KEY (Id_Cuadrilla)
  	REFERENCES Cuadrilla (Id_Cuadrilla)
);
   
 CREATE TABLE Incidencia (
    Id_Incidencia int PRIMARY KEY NOT NULL,
    EstadoInc varchar (20) NOT NULL,
    Descripcion varchar (120),
    FchaReportado date NOT NULL,
    FchaResuelto date,
    Id_Cuadrilla int NOT NULL,	
    Id_Usuario int NOT NULL,
    Id_Contenedor int NOT NULL,
   
   		CONSTRAINT FK_Incidencia_Cuadrilla 
    	FOREIGN KEY (Id_Cuadrilla) 
    	REFERENCES Cuadrilla (Id_Cuadrilla),
    	CONSTRAINT FK_Incidencia_Usuario
    	FOREIGN KEY (Id_Usuario)
    	REFERENCES Usuario (Id_Usuario),
    	CONSTRAINT FK_Incidencia_Contenedor
    	FOREIGN KEY (Id_Contenedor)
    	REFERENCES Contenedor (Id_Contenedor)
  );

    
      

      
     