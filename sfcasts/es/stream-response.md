# Respuestas Turbo Streams

Para el día 24, prepárate para una aventura rápida. Hemos aprendido que los Turbo Streams son elementos HTML personalizados que puedes lanzar a la página en cualquier lugar... ¡y se ejecutan! Pero hay otra forma de utilizar los Streams que en realidad está más comúnmente documentada, aunque yo la esté utilizando un poco menos últimamente.

En `VoyageController`, desplázate hacia arriba para encontrar la acción `new()`. En lugar de redirigir, como hacemos normalmente para el envío de un formulario, la otra opción es devolver una respuesta totalmente llena de flujos Turbo.

## Devolver una respuesta de flujos

Observa: quita el flash y devuelve `$this->renderBlockView()`... excepto que lo cambias por `renderBlock()`. Eso hace lo mismo, pero devuelve un objeto `Response` en lugar de una cadena. El último detalle es `$request->setRequestFormat()``TurboBundle::STREAM_FORMAT` :

[[[ code('d8dd6bfcaa') ]]]

Es un poco técnico, pero esto establecerá una cabecera `Content-Type` en la respuesta que le dirá a Turbo:

> ¡Eh! Ésta no es una respuesta normal de página completa. Sólo devuelvo un conjunto de
> Turbo Streams que quiero que proceses.

Redoble de tambores, por favor. Actualiza, ve a Nuevo Viaje... rellena los campos... y guarda. ¿Qué ha pasado? El modal sigue abierto y no hay notificación Toast. Pero si has estado atento, ¡la fila de la tabla sí se ha añadido!

En las herramientas de red, busca la petición POST. ¡Fíjate! La respuesta no es más que el `<turbo-stream>`: eso es lo único que devolvió nuestra aplicación.

## Devolver todos los flujos necesarios

La conclusión es: como no estamos redirigiendo a otra página, ya no obtenemos el comportamiento normal de `<turbo-frame>`, en el que encuentra el marco de la página siguiente y lo renderiza. En nuestro caso, el `<turbo-frame>` vacío es lo que cerró el modal y renderizó los mensajes flash.

Cuando decides devolver una respuesta de flujo, eres 100% responsable de actualizar todo en la página. Así que, en `new.html.twig`, aquí abajo, ¡necesitamos un par de streams más! Abre `edit.html.twig` y roba el que cierra el modal. Pon eso aquí.... y luego, desde `_frameSuccessStreams.html.twig`, roba el flujo que se anexa al contenedor flash:

[[[ code('79afcfa8f1') ]]]

¡Creo que eso es todo lo que necesitamos! Inténtalo de nuevo. Aquí está por fin nuestra notificación tostada del envío anterior. Crea un nuevo viaje... y... guarda. Ya está! Notificación tostada, modal cerrado, fila añadida.

## Turbo Mercure

Esta idea de devolver sólo un `<turbo-stream>` es similar a cómo funciona la integración de Turbo y Mercure. Si no lo sabes, Mercure es una herramienta que te permite obtener actualizaciones en tiempo real en tu front-end... algo así como web sockets, pero más guay. Y Mercure se empareja muy bien con Turbo. Desde dentro de tu controlador, publicas un `Update` en Mercure... que se enviará a los frontales de todos los navegadores que estén escuchando este tema `chat`.

El contenido de ese `Update` es un conjunto de Turbo Streams. Así que publicamos streams... esos streams se envían al frontend a través de Mercure, y Turbo los procesa.

En el frontend, podría tener este aspecto. Editamos una travesía, añadimos unos cuantos signos de exclamación y le damos a guardar. Por supuesto, nuestra página se actualiza gracias a los mecanismos normales de Turbo de los que hemos hablado. Pero, si estuviéramos utilizando Mercure, podríamos hacer que cualquier otra persona de esta página recibiera una actualización de Stream que también dijera que preañadiera esta fila. Así que añado los signos de exclamación, y de repente tú también los ves en tu pantalla, sin refrescar.

Es superguay y funciona mediante Streams.

Vale, aunque esto funciona muy bien, volvamos a nuestra antigua forma... que también funcionaba muy bien. Quita los nuevos Turbo Streams... y deshaz el código en el controlador.

Mañana pasaremos a una de mis partes favoritas de LAST Stack, y la clave para organizar tu sitio en trozos reutilizables: Los Componentes Twig.
