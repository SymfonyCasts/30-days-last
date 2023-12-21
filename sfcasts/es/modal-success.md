# Cosas extravagantes en el Éxito del Formulario Modal

Hemos estado ocupados. Hemos creado un sistema modal reutilizable basado en AJAX que me encanta. El envío con errores de validación ya funciona. ¿Y el éxito? Ya casi está. Cuando guardamos... no hay notificación de tostada, pero el modal se cerró.

La razón por la que se cerró es importante. En la acción `new()`, redirigimos a la página índice. Esa página amplía la `base.html.twig` normal... así que sí tiene una`<turbo-frame id="modal">` en ella... pero es esta vacía. Esto significa que el marco modal se vacía, nuestro controlador modal Stimulus se da cuenta y lo cierra.

## Planificación: Cuando los formularios están en marcos

En general, cuando añades un `<turbo-frame>` alrededor de algo -como en la página de inicio con nuestra barra lateral de planetas- tienes que pensar a dónde apuntan los enlaces que hay dentro. Tenemos que asegurarnos de que cada uno vaya a una página que tenga un `<turbo-frame>` correspondiente.

Cuando un formulario está dentro de `<turbo-frame>`, tenemos que pensar en lo que ocurre cuando se envía. El caso de error es fácil: siempre muestra la misma página que tiene el mismo marco con los errores dentro. Pero en caso de éxito, tenemos que pensar a dónde redirige el formulario y preguntarnos: ¿tiene esa página un `<turbo-frame>` que coincida y contiene el contenido correcto?

En el caso de este modal y la página índice, es perfecto: hay un marco coincidente, está vacío y el modal se cierra.

## Renderización de Flashes de Éxito con un Turbo Streams

Vale, ¡volvamos a la notificación de tostada que falta! Esta es una situación en la que necesitamos actualizar el `<turbo-frame>` -para vaciarlo- y también necesitamos actualizar otra área de la página: necesitamos renderizar los mensajes flash de éxito en el contenedor flash.

Esta es una necesidad súper común cuando un formulario se envía dentro de un `<turbo-frame>`. Así que vamos a resolver esto, creo, de una manera genial y global. Cuando redirijamos con éxito, este `<turbo-frame>` se cargará finalmente en la página, lo que hará que se cierre el modal. Dentro de él, añade un `<turbo-stream>` con `action="append"` y`target="flash-container"`:

[[[ code('8437d2a124') ]]]

Cuando añadimos el sistema de tostado, añadimos un elemento con `id="flash-container`:

[[[ code('81c173a849') ]]]

Entonces no lo necesitábamos, pero ahora nos va a venir bien porque podemos apuntar a él para añadirle mensajes flash. 

Dentro del flujo, añade la etiqueta `template`, por supuesto, y luego`{{ include('_flashes.html.twig') }}`:

[[[ code('cbdb9032b9') ]]]

Esto mostrará los mensajes flash... y el flujo los añadirá a ese contenedor.

¡Vamos a probarlo! Rellena un nuevo viaje, envíalo y... no pasa absolutamente nada. El problema... es sutil. Cuando redirigimos a la página índice, Symfony renderiza toda esa página... aunque Turbo sólo utilizará el `<turbo-frame id="modal">`. Esto significa que, justo antes de renderizar este código, nuestro contenedor flash renderiza los mensajes flash... lo que los elimina del sistema flash. Así que los mensajes flash están en el HTML que devolvemos de la llamada Ajax... pero como no están dentro del `<turbo-frame>`, no llegan a la página.

La solución es fácil: asegúrate de que tu contenedor flash está después del modal:

[[[ code('3984d10144') ]]]

Prueba esto. Actualiza... y rellena el formulario. ¡Ya está! El modal se cierra, ¡y el `<turbo-stream>` activa el brindis!

¡Y esto es realmente genial! Cuando redirigimos, el `<turbo-frame>` ahora no está vacío: contiene el flash `<turbo-stream>`. Pero recuerda: en cuanto se activa un `<turbo-stream>`, se ejecuta y luego desaparece. Una vez que eso ocurre, el`<turbo-frame>` queda vacío y el modal se cierra. Eso sí que me gusta.

## Extras del flujo: Anteponer la tabla

Lo que me encanta del sistema modal es que funciona... y no hemos necesitado hacer ningún cambio en nuestro controlador. Pero ahora, tenemos que pensar en cualquier comportamiento extra opcional que podamos desear.

Por ejemplo, ¿podríamos anteponer a la tabla el nuevo viaje? Porque, ahora mismo, no lo vemos hasta después de actualizar. Intentémoslo

En `index.html.twig`, busca el `table`. Tenemos que preagregarlo en el `tbody`. Para ello, en el `table`, añade un `id="voyage-list"`:

[[[ code('6a8ff202e6') ]]]

Pensemos: este es otro caso en el que necesitamos actualizar algo que vive fuera de `<turbo-frame>`. Por tanto, necesitamos un flujo.

Abre `new.html.twig` y después del bloque `body`, añade un nuevo bloque llamado `stream_success`, y después `endblock`. Dentro, añadiremos los Turbo Streams que necesitemos para que el envío brille de verdad. Añade un `<turbo-stream>` `action="prepend"` y luego `targets=""`. La "s" en los objetivos significa que podemos utilizar un selector CSS: `#voyage-list tbody`. Añade el elemento`<template>`... y, por ahora, un `<tr><td>` `{{ voyage.purpose }}` :

[[[ code('f1388b0cca') ]]]

Vale, ya tenemos un nuevo bloque en nuestra plantilla... que nadie está utilizando. De alguna manera, necesitamos coger este flujo Turbo... y, tras la redirección, renderizarlo en la página siguiente en el modal `<turbo-frame>`.

¿Cómo lo hacemos? Tenemos dos opciones -y mostraré la segunda el Día 24-. Pero éste es el sistema que me gusta.

En primer lugar, sólo tenemos que preocuparnos de anteponer la fila de la tabla cuando estamos enviando dentro de un `<turbo-frame>`. Si fuéramos directamente a la página del nuevo viaje -que no tiene marco- y enviáramos, no necesitaríamos ninguna cosa de Turbo Stream. Navegaríamos por la página completa y la renderizaríamos normalmente. Bonito y sencillo.

Así que, en el controlador, empieza con if `$request->headers->has('turbo-frame')`. Si el envío del formulario se produce dentro de `<turbo-frame>`, entonces queremos utilizar nuestro flujo. Renderiza ese bloque con `$stream` y luego con un método de controlador relativamente nuevo: `$this->renderBlockView()` pasando por `voyage/new.html.twig`. En lugar de renderizar toda la plantilla, para renderizar un solo bloque pasa esto, lo has adivinado, `stream_success`. En realidad... Creo que me falta una "s". Mejor.

Pasa a la plantilla una variable `voyage`.

Para pasar la cadena `<turbo-stream>` a la página siguiente añádela a un nuevo flash llamado`stream`:

[[[ code('112be81da0') ]]]

Por último, cuando redirijamos a la página índice y se renderice este `<turbo-frame>`, haz salir de ese flash: `for stream in app.flashes('stream')`, `endfor`con `{{ stream|raw }}` para que renderice los elementos HTML en bruto:

[[[ code('b4d4f9f925') ]]]

¡Creo que ya estamos listos! Actualiza... añade un nuevo viaje y... ¡es increíble! 
La llamada Ajax redirigía a la página índice, donde el marco modal tenía 2 flujos Turbo: uno para renderizar el brindis y otro para preagregar la tabla.

## Añadir contenido real

Último paso, preagregar el contenido real. Lo que queremos es este `tr`. Para obtenerlo desde dentro de `new.html.twig`, tenemos que aislarlo en su propia plantilla. Cópiala, bórrala e incluye `voyage/_row.html.twig`:

[[[ code('c6c1eeea3a') ]]]

Ve a crear esa plantilla... y pégala:

[[[ code('6458dbd91e') ]]]

Fácil.

Copia la declaración `include()` y, en `new.html.twig`, úsala para el flujo:

[[[ code('cd87bdbe3b') ]]]

¡Probemos esto! Crea otro viaje y... ¡precioso! El modal se cierra, la notificación tostada se renderiza y la página se actualiza. Es todo lo que queremos.

Mañana vamos a poner a prueba nuestro nuevo sistema modal abriendo el enlace de edición dentro de un modal. Prometí que sería reutilizable, y mañana lo probaremos... con algunas bolas curvas para hacerlo más realista.
