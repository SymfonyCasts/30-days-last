# Notificaciones Toast

Una parte importante de cualquier sitio bonito y funcional es un sistema de notificaciones. En Symfony, a menudo pensamos en mensajes flash: mensajes que mostramos cerca de la parte superior de la página, por ejemplo, después de que el usuario envíe un formulario. Y sí, a eso me refiero. Pero no nos basta con mostrarlos en la parte superior de la página. En lugar de eso, quiero mostrarlas como notificaciones ricas de estilo tostado en la parte superior derecha, que desaparezcan automáticamente con transiciones CSS y que puedan atarme los zapatos por mí.

## Representación de mensajes Flash

En nuestros controladores CRUD, ya estoy configurando un mensaje flash `success`... pero no lo estoy renderizando en ninguna parte. En el directorio `templates/`, crea un nuevo `_flashes.html.twig`. Para empezar, simplemente haz un bucle sobre los mensajes de éxito con `for message in``app.flashes('success')` ... y `endfor`:

[[[ code('fadbbb1e82') ]]]

Dentro, pegaré un mensaje flash muy sencillo, que empezará a fijarse en la parte inferior de la página:

[[[ code('e70f4d936d') ]]]

A continuación, en `base.html.twig`, en lugar de poner los flashes en algún lugar cerca de la parte superior del cuerpo, ponlos en la parte inferior. Digamos `<div id="flash-container">` y luego`{{ include('_flashes.html.twig') }}`:

[[[ code('4e0a07ea03') ]]]

El `id="flash-container"` no es importante todavía, pero será útil más adelante cuando hablemos de los flujos Turbo.

Veamos si funciona Pulsa "Guardar" y... ¡ya está! Está en un sitio raro, pero aparece.

## Hacer bonita la notificación

Para que quede más bonito, vamos a Flowbite. Busca "tostada". Ah, esto tiene algunos ejemplos geniales de diferentes estilos de notificaciones tostadas. ¡Esto me hace sentir peligroso!

***TIP
También recomiendo añadir un atributo `data-turbo-temporary` a la raíz `<div>`. Esto eliminará el mensaje flash antes de que Turbo tome su "instantánea" para el almacenamiento en caché, Esto significa que si el usuario hace clic en "Volver" a una página, el brindis no seguirá siendo visible.
***

De vuelta en `_flashes.html.twig`, pegaré algo de contenido:

[[[ code('39f4c48711') ]]]

Esto está muy inspirado en los ejemplos de Flowbite. Pero en realidad no ha cambiado nada: seguimos haciendo bucles sobre la misma colección y seguimos volcando el mensaje. Sólo tenemos un bonito marcado alrededor de esto.

¡Y no puedo querer verlo! Voy a editar y a "Guardar". Qué maravilla! En la parte superior derecha, donde yo quiero, y todo hecho con CSS.

## Hacer que la tostada se pueda cerrar

Aunque todavía no se cierra automáticamente. Demonios, ¡no se cierra en absoluto! Como "cerrar" cosas será un problema común, vamos a crear un controlador Stimulus reutilizable que pueda hacerlo.

En `assets/controller/`, añade un nuevo `closeable_controller.js`. Haré trampas y cogeré el código de otro controlador... límpialo... y luego añade un método `close()`. Cuando se llame a éste, eliminará todo el elemento al que está unido el controlador:

[[[ code('d7906bc655') ]]]

Para utilizar esto, en `_flashes.html.twig`, adjunta el controlador al elemento de nivel superior, porque eso es lo que debe eliminarse al cerrarse. A continuación, en el botón`data-action="closeable#close"`:

[[[ code('035447cbfc') ]]]

No necesitamos el `click` porque esto es un `button`, así que Stimulus ya sabe que queremos que esto se active en el evento `click`.

¡Vamos a probarlo! Pulsa editar y Guardar. Ya está... ha desaparecido.

En sólo unos minutos de trabajo, ¡hemos creado un bonito y funcional sistema de notificación de tostadas! Pero, ¡maldita sea, esto no es lo suficientemente guay para nuestra misión 30 Días de LAST Stack! Así que mañana lo mejoraremos con autocierre, transiciones CSS y una barra de temporizador animada.
