
# Proyecto Gasolineras

[![Author][Author]](http://www.antoniobuenosvinos.com)
[![Software License][License]](LICENSE.md)

Este proyecto tiene como objetivo la práctica de diversos componentes de software como el framework Symfony o Docker.

Siéntete libre para realizar las modificaciones que consideres necesarias para tu aprendizaje.

El proyecto consiste en la obtención del listado abierto de datos de las estaciones de servicio de España, así como de los precios de los diferentes tipos de gasolina que ofrecen. Una vez obtenidos ofrecemos este listado junto a una serie de funcionalidades de búsqueda y filtrado mediante Google Maps. 

## Pre-requisitos

[Composer][2] para descargar las dependencias de Symfony.

[Node.js y Yarn][8] para poder compilar los ficheros css y js.

Para la ejecución del proyecto lo puedes hacer con [docker][1], con el servidor interno de PHP gestionado mediante [Symfony][9] o con tu propio entorno de ejecución en local (Ej: mysql, nginx, php-fpm).

## Preparación

Una vez hayas descargado el proyecto en tu ordenador, deberás ejecutar los siguientes comandos.

```bash
$ composer update # Descarga las dependencias de Symfony
$ yarn encore dev # Compila los assets (js y css) necesarios para el proyecto
```

## Configuración

El fichero .env necesita obligatoriamente que declares estos parámetros:

1.- DATABASE_URL. La cadena donde se almacenarán los datos

2.- KEY_GMAPS. La [key de Google Maps][6] para que funcionen los mapas

3.- ADMIN_USER y ADMIN_PASS para la parte de administración

4.- URL_EXTERNAL_DATA. Es la url de donde obtendremos la información de las estaciones de servicio. Este parámetro si que tiene un valor por defecto.

5.- MESSENGER_TRANSPORT_DSN. La cadena donde se almacenarán los mensajes del Bus que establezcas como asincronos.

6.- MAILER_DSN. La cadena de conexión para el servicio de envío de mails.

La [recomendación de Symfony][5] es que crees un fichero .env.local con estos datos 

## Ejecución

### Docker

1.- Ejecuta el comando:

```bash
$ docker-compose -f ./docker/docker-compose.yml up -d --build
```

La primera vez tarda algunos minutos por una carga inicial de datos para que el proyecto ya sea funcional desde el inicio

2.- Accede a la aplicación en tu navegador en http://0.0.0.0:8080/

### Web Server de Symfony

1.- Ejecuta el comando

```bash
$ php bin/console server:run
```

2.- Accede a la aplicación en tu navegador en http://127.0.0.1:8000

### Software local

1.- El primer paso será crear la base de datos

```bash
$ php bin/console doctrine:database:create
$ bin/console doctrine:schema:update --force
```

2.- El siguiente configurar tu ordenador para poder ejecutar la aplicación.

Hay diferentes formas de poder ejecutar el proyecto en tu sistema operativo, la que yo te recomiendo es con mysql, nginx y php-fpm.

Hay mucha información en internet sobre la instalación de estos productos, por ejemplo puedes seguir esta [guía][3].

Una recomendación es que añadas a tu fichero de hosts la siguiente cadena:

```bash
127.0.0.1	gasolineras.local
```

En consecuencia será mejor crear un fichero de configuración de nginx especifico que te permita trabajar con ese dominio. Te dejo aquí el que yo he usado.

```bash
server {
    listen 443;
    listen 80;

    server_name gasolineras.local;
    root /media/antonio/Datos/PROYECTOS/www/gasolineras/public; # Aquí debes poner el path a la carpeta public donde hayas instalado el proyecto

    location / {
        # try to serve file directly, fallback to index.php
        try_files $uri /index.php$is_args$args;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php7.2-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_read_timeout 1200;

        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;

        internal;
    }

    location ~ \.php$ {
        return 404;
    }

    error_log /var/log/nginx/gasolineras_error.log;
    access_log /var/log/nginx/gasolineras_access.log;
}
```

La ubicación ideal de este fichero es en `/etc/nginx/sites-local/`

Con estos cambios separas de una manera clara este proyecto del resto de proyectos que puedas tener en tu ordenador.

3.- Accede a la aplicación en tu navegador en http://gasolineras.local

## Ejecución de tareas programadas

Existen tres tareas que son necesarias para que el proyecto sea operativo

1.- Obtención del excel de datos

Obtiene el Excel que contiene la información de la fuente externa indicada en el fichero .env.

     php bin/console app:download-excel

2.- Procesamiento del excel de datos

Una vez descargado el Excel, lo procesa para guardar todos los datos de las estaciones de servicio en base de datos.

     php bin/console app:process-excel

3.- Limpieza de datos (opcional)

Dado que la cantidad de datos que se almacena cada vez que se solicitan almacenan los registros de las estaciones de servicio es grande, esta tarea deja únicamente las últimas 10 versiones de datos. 

     php bin/console app:clean-history

## Consideraciones para hosting compartido

Dado que este proyecto puede ejecutarse en un hosting compartido, y ello implica una serie de limitaciones, ten en consideración los siguientes puntos.

### Fichero public/.htaccess

Contiene las reglas que el proyecto necesita para trabajar con urls bonitas en Apache.

### Controllers de tipo Bridge

La actualización de datos de las gasolineras se hace a través de Commands. Estos Commands se deben ejecutar periódicamente y la manera más sencilla de hacerlo es usando el crontab de un sistema Linux.

Pero muchos hosting no tienen esta posibilidad, por lo que para poder ejecutar los Command he creado tres Controller que una vez ejecutados, llaman a su vez a los Command.

Para ejecutar estos Command deberás utilizar un servicio de tipo https://cron-job.org/ para programar la ejecución de los comandos.

Adicionalmente es interesante que sepas que dado que estos procesos pueden ser costosos de ejecutar he añadido algunas líneas para aumentar memoria y timeout en estos procesos.

    ini_set('memory_limit','3072M');
    set_time_limit ( 1200 );

### Obtención del Excel

Personalmente he tenido problemas para descargar el Excel desde el hosting que tengo contratado. Aunque no he podido determinar el problema, el resultado es que no podía descargar el Excel, seguramente porque algún conflicto entre el ssl de la url destino y mi hosting.

Si tú también tienes este problema, la solución que he aplicado es buscar otro servicio que actúe de proxy y obtenga el Excel.

## Otras consideraciones

### Aplicación Android

He creado un proyecto [Android][7] que complementa a este proyecto.

El proyecto Android tiene como objetivo principal poder utilizar esta web desde una aplicación nativa.

Además se encarga de obtener la posición del usuario utilizando las capacidades del teléfono.

### Funcionalidad "Mi posición"

La aparición del botón "Mi posición" está condicionado a que el proyecto se ejecute sobre https o que se haya cargado a través de la aplicación Android mencionada en el punto anterior.

En el primer caso es necesario porque la funcionalidad de obtener la posición de un usuario en un navegador web solo funciona sobre https.

En el segundo caso, al obtener la posición desde la aplicación Android no es necesario que la web se ejecute sobre https. 

La aparición del botón "Mi posición" está condicionado a que el proyecto se ejecute sobre https o que se haya cargado a través de la aplicación Android mencionada en el punto anterior.

## Uso de librerias

Como el objetivo de este proyecto es la práctica a continuación indico algunas librerías que se han utilizado para enriquecer la aplicación.

### Uso de Bus para Command, Query y Event

Utilizando el componente symfony/messenger se ha establecido un Bus para la implementación de parte del patrón CQRS

La parte interesante con la que puedes trabajar es con la gestión de estos paquetes de manera asincrona.

A continuación te dejo un trozo de código que puedes encontrar en config/packages/dev/messenger.yaml que te permitiría que el comando App\Application\Command\GetDataCommand se ejecutara de manera asincrona.

    framework:
        messenger:
            routing:
                # Route your messages to the transports
                #'App\Application\Command\GetDataCommand': async

Puedes encontrar toda la información en la [página oficial][10] del componente

### Uso de Mailer

Para el envío de mails se ha utilizado el componente symfony/mailer.

Este componente se usa para notificar los eventos de descarga y procesamiento del Excel. Así podemos estar informados que los procesos clave de este proyecto se están ejecutando correctamente.

### Gestión de usuarios

La primera versión utilizaba la validación http_basic y aún queda el código en el fichero secutiry.yaml y las variables ADMIN_USER y ADMIN_PASS contienen las credenciales para esta validación.

        alternative:
            anonymous: true
            provider: admin_users
            http_basic:
                realm: Secured Area

Pero actualmente estamos utilizando el componente symfony/security y otros componentes de form y validation para la gestión de usuarios.

Para la creación de usuarios tienes dos métodos:

- Por comando: `bin/console app:add-user --help`
- Por url `/register`

El primer caso te permite crear de manera opcional usuarios administrador, el segundo solo usuarios regulares.

Para el login puedes usar la url `/login`

## Posibles mejoras

* Usar los componentes Form y Validation de Symfony para la validación de administrador

* Mejorar visualmente la parte de administración

* Integrar las analíticas de Firebase al proyecto

* Crear un efecto loading cada vez que se haga una petición de datos, para que el usuario perciba la sensación de carga 

* Hacer que el ngnix de Docker permita conexiones ssl

* Crear algún tipo de proyecto relativo a los cambios de precios de las estaciones de gasolina, por ejemplo, el precio medio por provincias

* Usar Open Street Maps e implantar un sistema para poder usar cualquier de los sistemas de mapas implantados

* En cuanto a la gestión de usuarios queda pendiente:
    - Login y registro de usuarios mediante Facebook
    - Login y registro de usuarios mediante Google
    - Reset de contraseña
    - Hacer que el login te lleve a la página que estabas antes de pedirte el login

## Contacto

Si tienes cualquier sugerencia o problema, por favor, házmelo saber a través de este [formulario de contacto][4].

Espero te sea útil.


[1]: https://www.docker.com/
[2]: https://getcomposer.org/
[3]: https://www.howtoforge.com/tutorial/installing-nginx-with-php7-fpm-and-mysql-on-ubuntu-16.04-lts-lemp/ 
[4]: http://www.antoniobuenosvinos.com/hablamos/
[5]: https://symfony.com/blog/new-in-symfony-4-2-define-env-vars-per-environment
[6]: https://developers.google.com/maps/documentation/javascript/get-api-key
[7]: https://github.com/abuenosvinos/gasolineras-android
[8]: https://symfony.com/doc/current/frontend/encore/installation.html
[9]: https://symfony.com/doc/current/setup/built_in_web_server.html
[10]: https://symfony.com/doc/current/messenger.html

[Author]: http://img.shields.io/badge/author-@abuenosvinos-blue.svg?style=flat-square
[License]: https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square
