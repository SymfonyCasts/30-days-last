# paquetes JavaScript de terceros

¡Bienvenido al fabuloso día 4! Ya estamos creando módulos JavaScript... un término elegante que significa que estamos escribiendo declaraciones import y declaraciones export. Y lo estamos haciendo completamente sin un sistema de compilación. ¡Es hora de bailar felices!

¿Pero qué pasa con los paquetes de terceros? Dirígete a https://npmjs.com y busca un paquete muy importante llamado `js-confetti`. Este paquete trata sobre la celebración, que... ¡es exactamente lo que estamos haciendo durante estos 30 días! En el README dice que utilices Yarn para instalarlo. No vamos a hacerlo. En lugar de eso, ve directamente al ejemplo de uso. Cópialo, dirígete a nuestro `app.js`... y pégalo:

[[[ code('eaf6e189f7') ]]]

Nota al margen: las declaraciones `import` siempre van al principio de tu archivo. Si no lo haces -si haces algo raro como esto-, bueno, puedes hacerlo, pero tu navegador lo subirá al principio cuando ejecute el código de todos modos. Así que evitaremos dar problemas.

## Error de módulo JavaScript ausente

Vale: ¿funcionará esto? Probablemente no, porque no hemos instalado nada. ¡Pero vivamos imprudentemente e intentémoslo de todos modos! ¡Error! Un error muy importante:

> Error al resolver el especificador de módulo `js-confetti`. Las referencias relativas deben empezar
> con `/`, `./` o `../`.

Lo que esto está diciendo es que tu navegador encontró una declaración `import`... y no tiene ni idea de cómo cargar ese archivo. Si una sentencia import empieza por `./` o `../`, tu navegador sabe cómo manejarlo: busca un archivo relativo a este archivo. Fácil.

Pero si no hay `./` o `../`, se llama módulo desnudo. En ese caso, tu navegador busca una coincidencia en el mapa de importación. Ahora mismo, nuestro mapa de importación tiene el mismo aspecto que antes. En particular, no tenemos una clave `js-confetti`. Y por eso obtenemos este error.

Éste es uno de los errores más importantes que verás al codificar con módulos. Y tendrá un aspecto un poco diferente según el navegador que estés utilizando. Firefox, por ejemplo, enuncia este error de forma diferente.

Pero independientemente de la redacción, este error casi siempre significa lo mismo: estás intentando utilizar un paquete de terceros, pero no está instalado.

## Instalar paquetes con `importmap:require`

¿Cómo lo instalamos? ¡Me alegro de que preguntes! Copia el nombre del paquete, gíralo y ejecútalo:

```terminal
php bin/console importmap:require js-confetti
```

¡Ya está! Vuelve a girar y... ¡celebración! ¡Funciona! ¡Refrescante locura!

¿Cómo funciona? ¿Karma? Bueno, como es lógico, si ves el código fuente de la página, tenemos una nueva entrada dentro de nuestro `importmap` con una clave `js-confetti`. Y apunta a un archivo en un directorio `assets/vendor/`. Interesante.

Cuando ejecutamos ese comando, en realidad sólo hizo una cosa. Actualizó nuestro archivo`importmap.php`. Añadió esta entrada de aquí:

[[[ code('43339f60ec') ]]]

Entre bastidores, buscó la última versión y la puso aquí. Y como tenemos un elemento `js-confetti` en `importmap.php`, significa que vamos a tener una clave `js-confetti` correspondiente dentro del mapa de importación de la página.

## El directorio assets/vendor/

¿Dónde vive realmente ese archivo? Aquí arriba, en un nuevo directorio `assets/vendor/`. Si indagas, aquí está el archivo real que se está cargando.

Dos detalles jugosos sobre este directorio `vendor/`. El primero es: se ignora desde Git: puedes ver `/assets/vendor/`:

[[[ code('15dbffb3fe') ]]]

Al igual que el directorio `vendor/` de Composer, no es algo que debas confirmar en tu repositorio.

La segunda es más bien una pregunta: ¿cómo obtengo estos archivos si clono o actualizo un proyecto y faltan algunos o todos los archivos?

Para averiguarlo, vuélvete loco y destruye ese directorio. Muwahahaha. Y ahora, para aumentar nuestra racha temeraria, intenta actualizar la página. ¡Error! ¡Impresionante error!

> Falta el activo de vendedor `js-confetti`: prueba a ejecutar el comando `importmap:install`.

¡Buena idea! Gira e inténtalo:

```terminal
php bin/console importmap:install
```

Igual que `composer install`, que descarga lo que necesites en`assets/vendor/`... y ahora simplemente funciona.

¡Ya está! Al cuarto día, ¡ya hemos resuelto los paquetes de terceros! ¡Ni siquiera necesitamos un directorio `node_modules/` gigante! Voy a tener que encontrar otra forma de llenar mi disco duro. ¿Tal vez, más contenedores Docker?

Vale, para la aventura de mañana, vamos a animar nuestro sitio con algo de CSS. ¡Permanece atento!
