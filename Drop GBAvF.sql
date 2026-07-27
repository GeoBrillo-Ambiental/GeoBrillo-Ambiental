insert into Rol (Id_Rol,Nombre_Rol, Descripcion) 
values (1, 'Transeunte', 'Usuario ciudadano que reporta las incidencias y visualisa 
        contenedores' );
insert into Rol values (2, 'Chofer', 'Es el usuario que maneja el camion y el principal 
                        usuario del mapa interactivo');
INSERT INTO Rol VALUES (3, 'Operador de planta','Es el usuario que trabaja en los centros 
                        de acopio o vertederos');
INSERT INTO Rol VALUES (4, 'Cuadrilla','Es el integrante de la cuadrilla de recolectores');

INSERT into Zona (Id_Zona, NomZona) 
values (0001,'Ciudad vieja');
INSERT Into Zona VALUES (0002, 'Buceo');
INSERT Into Zona VALUES (0003, 'Tres cruces');
INSERT Into Zona VALUES (0004, 'Pocitos');
INSERT Into Zona VALUES (0005, 'Malvin Norte');
INSERT Into Zona VALUES (0006, 'Malvin');
INSERT Into Zona VALUES (0007, 'Punta Carretas');
INSERT Into Zona VALUES (0008, 'Punta Gorda');
INSERT Into Zona VALUES (0009, 'Carrasco Norte');
INSERT Into Zona VALUES (0010, 'Carrasco');
INSERT Into Zona VALUES (0011, 'Prado');
INSERT Into Zona VALUES (0012, 'Piedras Blancas');
INSERT Into Zona VALUES (0013, 'Maroñas');
INSERT Into Zona VALUES (0014, 'Union');
INSERT Into Zona VALUES (0015, 'La Blanqueada');
INSERT Into Zona VALUES (0016, 'Parque Rodo');

INSERT into CAcopioVertedero (Id_Centro, NombreC, DireccionC, TipoCentro, CapTotal, CapActual)
values (01,'Felipe Cardoso', 'Cno. Felipe Cardoso 3220', 'Vertedero', 50000 , 28000 );
INSERT into CAcopioVertedero values (02, 'Ecocentro Buceo', 'Av. Tomás Basañez 1212',
                                     'Ecocentro', 500, 120);
INSERT into CAcopioVertedero values (03, 'Ecocentro Prado', 'Lucas Obes y J. Suárez',
                                     'Ecocentro', 500, 95);
INSERT into CAcopioVertedero values (04, 'Planta Géminis', 'Camino Géminis S/N', 'Planta',
                                     2000, 450);

INSERT INTO Ruta (Id_Ruta, DiaHora, Id_Zona, Id_Centro, Id_Cuadrilla) 
values (0001, 'Lunes y Jueves de 8hs a 12hs',0001, 01, 0001);
INSERT INTO Ruta values (0002,'Lunes y Jueves de 8hs a 12hs',0002, 01, 0002);
INSERT INTO Ruta values (0003,'Lunes y Jueves de 8hs a 12hs',0003, 01, 0003);

INSERT INTO Ruta values (0004,'Sabado y Miercoles de 8hs a 12hs',0004, 01, 0004);
INSERT INTO Ruta values (0005,'Sabado y Miercoles de 8hs a 12hs',0005, 01, 0005);
INSERT INTO Ruta values (0006,'Sabado y Miercoles de 8hs a 12hs',0006, 01, 0006);

INSERT INTO Ruta values (0007,'Viernes y Martes de 8hs a 12hs',0007, 01, 0007);
INSERT INTO Ruta values (0008,'Viernes y Martes de 8hs a 12hs',0008, 01, 0008);
INSERT INTO Ruta values (0009,'Viernes y Martes de 8hs a 12hs',0009, 01, 0009);

INSERT INTO Ruta values (0010,'Lunes y jueves de 13hs a 17hs',0010, 01, 0010);
INSERT INTO Ruta values (0011,'Lunes y jueves de 13hs a 17hs',0011, 01, 0011);
INSERT INTO Ruta values (0012,'Lunes y jueves de 13hs a 17hs',0012, 01, 0012);

INSERT INTO Ruta values (0013,'Sabado y Miercoles de 13hs a 17hs',0013, 01, 0013);
INSERT INTO Ruta values (0014,'Sabado y Miercoles de 13hs a 17hs',0014, 01, 0014 );
INSERT INTO Ruta values (0015,'Sabado y Miercoles de 13hs a 17hs',0015, 01, 0015);
INSERT INTO Ruta values (0016, 'Sabado y Miercoles de 13hs a 17hs',0016, 01, 0016);

INSERT INTO Contenedor (Id_Contenedor, EstadoCont, CapContenedor, TipoReciduo, Id_Ruta)
VALUES (000001, 'Funcional', 3600, 'Mezclado', 0001);
insert INTO Contenedor VALUES (000002, 'Funcional', 2400, 'Mezclado', 0001);

insert INTO Contenedor VALUES (000003, 'Funcional', 3600, 'Mezclado', 0002);
insert INTO Contenedor VALUES (000004, 'Funcional', 2400, 'Mezclado', 0002);

insert INTO Contenedor VALUES (000005, 'Funcional', 3600, 'Mezclado', 0003);
insert INTO Contenedor VALUES (000006, 'Funcional', 2400, 'Mezclado', 0003);

insert INTO Contenedor VALUES (000007, 'Funcional', 3600, 'Mezclado', 0004);
insert INTO Contenedor VALUES (000008, 'Funcional', 2400, 'Mezclado', 0004);

insert INTO Contenedor VALUES (000009, 'Funcional', 3600, 'Mezclado', 0005);
insert INTO Contenedor VALUES (000010, 'Funcional', 2400, 'Mezclado', 0005);

insert INTO Contenedor VALUES (000011, 'Funcional', 3600, 'Mezclado', 0006);
insert INTO Contenedor VALUES (000012, 'Funcional', 2400, 'Mezclado', 0006);

insert INTO Contenedor VALUES (000013, 'Funcional', 3600, 'Mezclado', 0007);
insert INTO Contenedor VALUES (000014, 'Funcional', 2400, 'Mezclado', 0007);

insert INTO Contenedor VALUES (000015, 'Funcional', 3600, 'Mezclado', 0008);
insert INTO Contenedor VALUES (000016, 'Funcional', 2400, 'Mezclado', 0008);

insert INTO Contenedor VALUES (000017, 'Funcional', 3600, 'Mezclado', 0009);
insert INTO Contenedor VALUES (000018, 'Funcional', 2400, 'Mezclado', 0009);

insert INTO Contenedor VALUES (000019, 'Funcional', 3600, 'Mezclado', 0010);
insert INTO Contenedor VALUES (000020, 'Funcional', 2400, 'Mezclado', 0010);

insert INTO Contenedor VALUES (000021, 'Funcional', 3600, 'Mezclado', 0011);
insert INTO Contenedor VALUES (000022, 'Funcional', 2400, 'Mezclado', 0011); 

insert INTO Contenedor VALUES (000023, 'Funcional', 3600, 'Mezclado', 0012);
insert INTO Contenedor VALUES (000024, 'Funcional', 2400, 'Mezclado', 0012); 

insert INTO Contenedor VALUES (000025, 'Funcional', 3600, 'Mezclado', 0013);
insert INTO Contenedor VALUES (000026, 'Funcional', 2400, 'Mezclado', 0013); 

insert INTO Contenedor VALUES (000027, 'Funcional', 3600, 'Mezclado', 0014);
insert INTO Contenedor VALUES (000028, 'Funcional', 2400, 'Mezclado', 0014); 

insert INTO Contenedor VALUES (000029, 'Funcional', 3600, 'Mezclado', 0015);
insert INTO Contenedor VALUES (000030, 'Funcional', 2400, 'Mezclado', 0015);

insert INTO Contenedor VALUES (000031, 'Funcional', 3600, 'Mezclado', 0016);
insert INTO Contenedor VALUES (000032, 'Funcional', 2400, 'Mezclado', 0016); 

Insert into Camion (Id_Camion, EstadoCam, Modelo, C_Carga)
values (0001, 'Disponible', 'Carga frontal', 14);
INSERT INTO Camion VALUES (0002, 'Disponible', 'Carga lateral', 12);
INSERT INTO Camion values (0003, 'Disponible', 'Carga frontal', 14);
INSERT INTO Camion VALUES (0004, 'Disponible', 'Carga lateral', 12);
INSERT INTO Camion values (0005, 'Disponible', 'Carga frontal', 14);
INSERT INTO Camion VALUES (0006, 'Disponible', 'Carga lateral', 12);
INSERT INTO Camion values (0007, 'Disponible', 'Carga frontal', 14);
INSERT INTO Camion VALUES (0008, 'Disponible', 'Carga lateral', 12);
INSERT INTO Camion values (0009, 'Disponible', 'Carga frontal', 14);
INSERT INTO Camion VALUES (0010, 'Disponible', 'Carga lateral', 12);
INSERT INTO Camion values (0011, 'Disponible', 'Carga frontal', 14);
INSERT INTO Camion VALUES (0012, 'Disponible', 'Carga lateral', 12);
INSERT INTO Camion values (0013, 'Disponible', 'Carga frontal', 14);
INSERT INTO Camion VALUES (0014, 'Disponible', 'Carga lateral', 12);

INSERT INTO Camion values (0015, 'Disponible', 'Carga frontal', 14);
INSERT INTO Camion VALUES (0016, 'Disponible', 'Carga lateral', 12);
INSERT INTO Camion values (0017, 'Disponible', 'Carga frontal', 14);
INSERT INTO Camion VALUES (0018, 'Disponible', 'Carga lateral', 12);
INSERT INTO Camion values (0019, 'Disponible', 'Carga frontal', 14);
INSERT INTO Camion VALUES (0020, 'Disponible', 'Carga lateral', 12);

INSERT INTO Cuadrilla (Id_Cuadrilla, Dia, Horario, Id_Rol, id_Rol2, Id_Camion) 
VALUES (0001, 'Lunes y Jueves',' de 8hs a 12hs', 2, 4, 0001);
INSERT INTO Cuadrilla VALUES (0002, 'Lunes y Jueves',' de 8hs a 12hs', 2, 4, 0002);
INSERT INTO Cuadrilla VALUES (0003, 'Lunes y Jueves',' de 8hs a 12hs', 2, 4, 0003);

INSERT INTO Cuadrilla VALUES (0004, 'Sabado y Miercoles',' de 8hs a 12hs', 2, 4, 0004);
INSERT INTO Cuadrilla VALUES (0005, 'Sabado y Miercoles',' de 8hs a 12hs', 2, 4, 0005);
INSERT INTO Cuadrilla VALUES (0006, 'Sabado y Miercoles',' de 8hs a 12hs', 2, 4, 0006);

INSERT INTO Cuadrilla VALUES (0007, 'Viernes y Martes',' de 8hs a 12hs', 2, 4, 0007);
INSERT INTO Cuadrilla VALUES (0008, 'Viernes y Martes',' de 8hs a 12hs', 2, 4, 0008);
INSERT INTO Cuadrilla VALUES (0009, 'Viernes y Martes',' de 8hs a 12hs', 2, 4, 0009);

INSERT INTO Cuadrilla VALUES (0010, 'Lunes y Jueves',' 13hs a 17hs', 2, 4, 0010);
INSERT INTO Cuadrilla VALUES (0011, 'Lunes y Jueves',' 13hs a 17hs', 2, 4, 0011);
INSERT INTO Cuadrilla VALUES (0012, 'Lunes y Jueves',' 13hs a 17hs', 2, 4, 0012);

INSERT INTO Cuadrilla VALUES (0013, 'Sabado y Miercoles',' 13hs a 17hs', 2, 4, 0013);
INSERT INTO Cuadrilla VALUES (0014, 'Sabado y Miercoles',' 13hs a 17hs', 2, 4, 0014);
INSERT INTO Cuadrilla VALUES (0015, 'Sabado y Miercoles',' 13hs a 17hs', 2, 4, 0015);
INSERT INTO Cuadrilla VALUES (0016, 'Sabado y Miercoles',' 13hs a 17hs', 2, 4, 0016);

INSERT INTO Usuario (Id_Usuario, Pri_Nom, Pri_Ape, Nom_Usuario, Password, Email, Id_Rol, Id_Cuadrilla)
VALUES (0001,'Katsuki','Bakugo','Dynamight','dekulover','bkdk4ever@gmail.com', 1, NULL);

INSERT INTO Usuario VALUES (0002,'aa','aa','aa','aa','aa@gmail.com', 1, NULL);
INSERT INTO Usuario VALUES (0003,'aaa','aaa','aaa','aaa','aaa@gmail.com', 3, NULL);

INSERT INTO Usuario VALUES (0004,'bb','bb','bb','bb','bb@gmail.com', 2, 0001);
INSERT INTO Usuario VALUES (0005,'bbbb','bbbb','bbbb','bbbb','bbbb@gmail.com', 4,0001);

INSERT INTO Usuario VALUES (0006,'cc','cc','cc','cc','cc@gmail.com', 2, 0002);
INSERT INTO Usuario VALUES (0007,'cccc','cccc','cccc','cccc','cccc@gmail.com', 4,0002);

INSERT INTO Usuario VALUES (0008,'dd','dd','dd','dd','dd@gmail.com', 2, 0003);
INSERT INTO Usuario VALUES (0009,'dddd','dddd','dddd','dddd','dddd@gmail.com', 4,0003);

INSERT INTO Usuario VALUES (0010,'ee','ee','ee','ee','ee@gmail.com', 2, 0004);
INSERT INTO Usuario VALUES (0011,'eeee','eeee','eeee','eeee','eeee@gmail.com', 4,0004);

INSERT INTO Usuario VALUES (0012,'ff','ff','ff','ff','ff@gmail.com', 2, 0005);
INSERT INTO Usuario VALUES (0013,'ffff','ffff','ffff','ffff','ffff@gmail.com', 4,0005);

INSERT INTO Usuario VALUES (0014,'gg','gg','gg','gg','gg@gmail.com', 2, 0006);
INSERT INTO Usuario VALUES (0015,'gggg','gggg','gggg','gggg','gggg@gmail.com', 4,0006);

INSERT INTO Usuario VALUES (0016,'hh','hh','hh','hh','hh@gmail.com', 2, 0007);
INSERT INTO Usuario VALUES (0017,'hhhh','hhhh','hhhh','hhhh','hhhh@gmail.com', 4,0007);

INSERT INTO Usuario VALUES (0018,'ii','ii','ii','ii','ii@gmail.com', 2, 0008);
INSERT INTO Usuario VALUES (0019,'iiii','iiii','iiii','iiii','iiii@gmail.com', 4,0008);

INSERT INTO Usuario VALUES (0020,'jj','jj','jj','jj','jj@gmail.com', 2, 0009);
INSERT INTO Usuario VALUES (0021,'jjjj','jjjj','jjjj','jjjj','jjjj@gmail.com', 4,0009);

INSERT INTO Usuario VALUES (0022,'kk','kk','kk','kk','kk@gmail.com', 2, 0010);
INSERT INTO Usuario VALUES (0023,'kkkk','kkkk','kkkk','kkkk','kkkk@gmail.com', 4,0010);

INSERT INTO Usuario VALUES (0024,'ll','ll','ll','ll','ll@gmail.com', 2, 0011);
INSERT INTO Usuario VALUES (0025,'lll','llll','llll','llll','llll@gmail.com', 4,0011);

INSERT INTO Usuario VALUES (0026,'mm','mm','mm','mm','mm@gmail.com', 2, 0012);
INSERT INTO Usuario VALUES (0027,'mmmm','mmmm','mmmm','mmmm','mmmm@gmail.com', 4,0012);

INSERT INTO Usuario VALUES (0028,'nn','nn','nn','nn','nn@gmail.com', 2, 0013);
INSERT INTO Usuario VALUES (0029,'nnnn','nnnn','nnnn','nnnn','nnnn@gmail.com', 4,0013);

INSERT INTO Usuario VALUES (0030,'oo','oo','oo','oo','oo@gmail.com', 2, 0014);
INSERT INTO Usuario VALUES (0031,'oooo','oooo','oooo','oooo','oooo@gmail.com', 4,0014);

INSERT INTO Usuario VALUES (0032,'pp','pp','pp','pp','pp@gmail.com', 2, 0015);
INSERT INTO Usuario VALUES (0033,'pppp','pppp','pppp','pppp','pppp@gmail.com', 4,0015);

INSERT INTO Usuario VALUES (0034,'qq','qq','qq','qq','qq@gmail.com', 2, 0016);
INSERT INTO Usuario VALUES (0035,'qqqq','qqqq','qqqq','qqqq','qqqq@gmail.com', 4,0016);


