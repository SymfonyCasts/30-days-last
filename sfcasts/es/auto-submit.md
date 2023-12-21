# Formularios de envío automático

¿Ya es el día 12? Durante los próximos 3 días, vamos a trabajar en un gran objetivo: transformar esta tabla en una rica configuración de tabla de datos, con búsqueda, filtrado de columnas, paginación, todo ello con un bonito AJAX. Esta es una de las partes en las que estoy más emocionado por sumergirme.

Nuestra página de inicio tiene una búsqueda. Y no tiene nada de especial. Pulso intro para enviar el formulario, el parámetro de consulta está en la URL y filtra los resultados. Naturalmente, gracias a Turbo Drive, todo ocurre vía AJAX.

Para nuestro primer truco, mira cómo hacemos que la búsqueda se actualice automáticamente a medida que escribimos. Así que escribimos y, sin pulsar intro, la lista debería actualizarse.

Para hacer esto, vamos a tomar prestado un controlador de un repositorio de [30 Días de Hotwire](https://github.com/ilrock/thirty_days_of_hotwire). Esto viene de un fantástico reto de [30 Días de Hotwire](https://twitter.com/ilrock__/status/1631315562390519809) que hizo alguien de la comunidad Rails. Me encanta esta serie y tiene un montón de cosas buenas. Te recomiendo que le eches un vistazo.

## El controlador de Stimulus de envío automático

De todos modos, voy a tomar prestado este estupendo controlador "auto-submit". Es sencillísimo: nos da una forma de enviar un formulario... con desbordamiento opcional. Si escribo muy rápido, no quiero enviar el formulario cuatro veces. Quiero que espere una ligera pausa... y luego lo envíe. A eso se le llama desbordamiento. Esto espera una pausa de 300 milisegundos.

Así que vamos a ponernos manos a la obra e introducir esto en nuestra aplicación. Crea un nuevo archivo llamado `autosubmit_controller.js`... y pégalo:

[[[ code('b6c0e8c72f') ]]]

Luego dirígete a la página de inicio para utilizarlo. Cerca de la parte superior... aquí está nuestro formulario de búsqueda. En el formulario, añade `data-controller"autosubmit"`:

[[[ code('41bb95feb9') ]]]

Observa que se autocompleta. Eso es gracias a un plugin Stimulus que tengo para PhpStorm.

A continuación, abajo en la entrada, di `data-action` igual a `autosubmit#debouncedSubmit`:

[[[ code('50d8de3da6') ]]]

En el controlador, puedes llamar a `submit` para enviar el formulario inmediatamente o a`debouncedSubmit()` para esperar a la pausa. Y esta vez no necesitamos incluir el nombre del evento, como `input->` para escuchar el evento `input`. Cuando aplicas un `data-action` a un `input`, un `button` o un `link`, Stimulus averigua qué evento quieres escuchar. La mayoría de las veces, la vida será así de sencilla.

## Instalar el paquete que falta

¿Funciona? ¡No! Porque tenemos un error... ¡un error que espero que te resulte familiar!

> Error al resolver el especificador de módulo `debounce`.

¡Esto proviene de nuestro código! Nuestro código copiado utiliza un paquete `debounce`... ¡y no lo tenemos instalado! ¡Genial! Copia `debounce`, gíralo y ejecútalo:

```terminal
php bin/console importmap:require debounce
```

Ahora está en nuestro proyecto... y ahora el error ha desaparecido. ¿Listo para la magia? Eh, ¡funciona! ¡Sólo una petición después de terminar de escribir gracias a debounce!

Lo único malo es que perdemos el foco cuando se recarga toda la página. Como solución provisional -no va a ser nuestra solución definitiva- podemos probar a poner`autofocus`:

[[[ code('631e2f24a3') ]]]

Esto... casi funciona... excepto que perdemos la posición del cursor: nos devuelve al principio. No pasa nada: pronto resolveremos esto de una forma mucho mejor. Y cuando lo hagamos, ni siquiera necesitaremos el autoenfoque.

Mañana vamos a enriquecerlo añadiendo paginación y ordenación por columnas.
