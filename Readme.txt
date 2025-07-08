DOCUMENTACIÓN DEL PROYECTO

INTRODUCCIÓN Y DECISIONES DE DISEÑO.

Primero, decidí usar PHP, ya que me siento más cómodo con este lenguaje. A lo largo de mi carrera, PHP ha sido el lenguaje con el que más he trabajado;
por lo tanto, me permitió desarrollar las funcionalidades de forma más eficiente. Además, pude tomar ideas de codigos de otros proyectos que tambien he hecho o incluso reciclar codigo.

También agregué un sistema de login simple, el cual permite diferenciar entre dos tipos de usuarios: administrador y cliente.

- El administrador, luego de iniciar sesión, es redirigido a la vista de gestión de productos, donde puede crear, actualizar y eliminar productos.
- El cliente, por su parte, solo puede visualizar los productos ya ingresados por el administrador y agregarlos al carrito de compras. No tiene acceso a las funciones administrativas.

FUNCIONALIDADES IMPLEMENTADAS.
-----------------------------------------------------------------------------------------------------
Se debe tomar en cuenta que las credenciales ya estan puestas por defecto:
Administrador:
Usuario: Administrador
Contraseña: admin123
Cliente:
Usuario: Cliente
Contraseña: cliente123
-----------------------------------------------------------------------------------------------------
Login básico con distinción entre administrador y cliente.  
Vista exclusiva para el administrador con:
- Crear/Ingesar productos
- Leer/Listar productos
- Actualizar productos
- Eliminar productos

Vista para cliente con:
- Visualización de productos disponibles
- Selección de cantidad según stock
- Agregar productos al carrito de compras
- Eliminar productos del carrito
- Ver el total del carrito actualizado en tiempo real

Diseño implementado con:
- Bootstrap 5 (para diseño responsivo y limpio)
- Bootstrap Icons (para botones de acciones)
- JavaScript (actualización de totales del carrito)


Manual de Instalacion y Ejecucion.
1.Instalar herramineta requerida:
Descargue e instale WampServer. En este caso yo ocupe la version 3.30.0.

2.Configurar WampServer:
1.Acceda al directorio de instalacion de WampServer(por defecto c://wamp) y abra la carpeta www.
2.Copie y pegue la carpeta llamada PracticaRPfarma dentro de ese directorio.

3.Configurar phpMyAdmin:
1.Active WampServer y acceda a phpMyAdmin con las credenciales predeterminadas:
URL:http://localhost/phpmyadmin
Usuario:root
Contraseña:(dejar en blanco)
2.Debe crear primero una base de datos que se llame rpfarmapractica.
3.Importe el archivo de base de datos llamado rpfarmapractica.sql en la base de datos que creo en el paso anterior.

4.Iniciar la página web local:
Acceda a la plataforma utilizando la direccion IP(IPv4) de su equipo en el navegador, de la siguiente forma:

1.Para saber cual es su direccion IP tiene que abrir el símbolo del sistema(CMD) y ejecute el comando: ipconfig.
2.La url del navegador debería verse así: "1xx.xxx.xx.xx/PracticaRPFARMA/Login.php"(Reemplace por su IP real.El sistema parte desde Login.php).

Problemas Encontrados y Soluciones.
Uno de los desafios fue que reutilicé un codigo para el login de mi proyecto de titulo, 
pero aunque ingresara la contraseña correcta, no me dejaba acceder. El problema era que,
la contraseña se encriptaba usando hash, y el codigo seguia intentado validar la contraseña encriptada, mientras que en este caso estaba ingresando contraseñas con texto plano.

La solucion fue modificar la funcion de verificación, eliminando la comparacion y usando una validacion mas simple.

Otro problema que tuve fue que, despues de vaciar el carrito, ya no me dejaba volver a agregar productos. Para arreglarlo,
hice que al vaciar el carrito la página se recargue automáticamnete, asi el carrito vuelve a funcionar sin problemas.




