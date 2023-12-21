# Tostadas más elegantes: Auto-cierre y Desvanecimiento

Ayer preparamos un bonito sistema de notificaciones Toast que funciona completamente con CSS y el sistema flash normal de Symfony. Vale, y sólo un poquito de JavaScript para, boop, cerrarlo.

Hoy vamos a llevar esto al siguiente nivel. Quiero que estos brindis sean increíbles.

## Añadir cierre automático

La primera función que vamos a añadir es el cierre automático: un clásico en el mundo de las tostadas, en el que el mensaje aparece en nuestra pantalla y se cierra automáticamente al cabo de unos segundos. Pero también quiero que nuestro controlador de cierre sea reutilizable. Puede haber otras partes del sitio en las que queramos poder cerrar algo... pero no que se cierre automáticamente.

Así que necesitamos una forma de activar el cierre automático caso por caso. La forma de pasar información a un controlador es mediante valores. Añade iguales a `static values`... e inventaré uno nuevo llamado `autoClose`, que será un `Number`:

[[[ code('bbcf9c83d0') ]]]

A continuación, añade un método `connect()`. La idea es que si tenemos `this.autoCloseValue` -así es como se hace referencia a eso-, entonces... ¡en realidad es perfecto! Utilizaremos`setTimeout` para cerrar después de esa cantidad de milisegundos:

[[[ code('db78dbfca0') ]]]

Para terminar, vamos a donde utilizamos este controlador - `_flashes.html.twig` - para pasar el nuevo valor `autoClose`. Lo hacemos en el mismo elemento que el `data-controller`. Añadimos `data-closeable-auto-close-value` igual y utilizamos 5.000 para 5 segundos:

[[[ code('5df4526859') ]]]

El formato es `data-` el nombre del controlador, `auto-close` - que es el nombre del valor `autoClose`... pero como estamos en un atributo HTML, utilizamos el "dash case" - luego la palabra `value` equals y finalmente lo que queremos pasar. Este formato es más difícil de recordar que sólo `data-controller`. Pero como has visto, si tienes este plugin Stimulus para PhpStorm, lo autocompleta, lo que ayuda mucho.

¡Vamos a hacerlo! Edita este registro, guarda y 1, 2, 3, 4, 5... ¡zas! Desaparece.

## Cerrar automáticamente la barra del temporizador

¿Qué es lo siguiente en nuestra búsqueda de la grandeza de las tostadas? ¿Qué tal una barra temporizadora que muestre cuándo se cerrará la tostada? Una pequeña barra que se vaya haciendo cada vez más pequeña y que finalmente desaparezca justo cuando la tostada se cierra automáticamente.

¡Suena genial! Este es el plan: vamos a añadir un elemento aquí abajo y luego animaremos su anchura del 100% al 0% durante esos 5 segundos. Para poder encontrar ese elemento, dentro del controlador, vamos a utilizar un objetivo. Añade`static targets = ['timerbar']`:

[[[ code('b64b15424c') ]]]

Luego abajo en `connect()`, comprueba que: si `this.hasTimerbarTarget`, entonces`this.timerbarTarget.style.width = 0`:

[[[ code('d7c70f75e6') ]]]

Suponiendo que hemos añadido una transición CSS a este elemento, eso debería animar el cambio de ancho completo a 0. Ah, pero otro detalle: añade un `setTimeout` y pon esto dentro con un retardo de 10 milisegundos:

[[[ code('5ebd443a54') ]]]

Esto permitirá que el elemento se establezca en la página con una anchura completa del 100%, antes de cambiarlo a 0. Se trata de un truco de transición CSS. Si añades o desocultas un elemento e inmediatamente cambias su anchura a 0... la transición CSS no funcionará. Tienes que dejar que el elemento esté en la página con una anchura del 100% durante 1 fotograma de animación, y luego cambiarlo.

Muy bien, con el escenario preparado, es hora de añadir la barra del temporizador. En la parte inferior de`_flashes.html.twig`, la pegaré:

[[[ code('bff9bcf2d7') ]]]

Esto tiene una posición absoluta en la parte inferior, a la izquierda del padre, con una altura y un fondo verde. También tiene una anchura explícita: es `w-full`. Esto es importante para la transición.

Para hacer de esto un objetivo, añade `data-closeable-target="timerbar"`:

[[[ code('cd17fc3623') ]]]

¡Vale! Veamos qué aspecto tiene. Pulsa editar, guardar, y se abre... pero sin animación. ¡Hagamos un poco de depuración! No hay errores en mi consola. Ah... aquí está el problema: debería haber hecho caso a mi editor: `timerbarTarget`.

Cerremos esto. Guarda y... ¡eso es lo que quiero ver! Y justo cuando llega a 0, boop, se cierra.

Vale, me encanta cómo queda esto. Pero nuestro brindis se merece un último detalle: un elegante fundido de salida... en lugar de esta salida abrupta.

## Transición CSS al cerrar

Desvanecer las cosas es un poco complicado. Puedes utilizar transiciones CSS -y lo haremos- para pasar de la opacidad 100 a la 0. Pero entonces también necesitas algo de JavaScript para esperar a que termine esa transición CSS y poder eliminar finalmente el elemento de la página o, al menos, establecer su visualización en none.

Para ayudarnos con esto, vamos a utilizar una biblioteca llamada `stimulus-use`. Los componentes de Stimulus -como vimos antes- son una lista de controladores de Stimulus reutilizables.`stimulus-use` es un grupo de comportamientos que puedes añadir a tus controladores de Stimulus. Y aquí hay un montón de herramientas interesantes.

La que vamos a utilizar se llama `useTransition`. Así que primer paso, vamos a instalarlo. Ejecuta:

```terminal
php bin/console importmap:require stimulus-use
```

¡Genial! Luego, en el controlador, impórtalo con`import { useTransition } from 'stimulus-use'`:

[[[ code('c3e59674a9') ]]]

Para activar un comportamiento, lo llamas desde `connect()`: `useTransition(this)`
y le pasas las opciones que necesites. Voy a pegar unas cuantas:

[[[ code('ca0d28c8d8') ]]]

Esto es lo que significa Mientras este elemento esté "saliendo" u ocultándose, la biblioteca añadirá estas tres clases. Esto establece que, en caso de que cambie alguna propiedad CSS en este elemento, queremos tener una transición de 200 milisegundos. El `leaveFrom` significa que, en el momento en que empiece a ocultarse, la biblioteca le dará esta clase: establecer su opacidad en 100. Después, un milisegundo más tarde, eliminará esta clase y añadirá `opacity-0`. Ese cambio desencadenará la transición de 200 milisegundos. Por último, `transitioned` true es una forma de decirle a la biblioteca que empezamos en estado visible... porque también puedes utilizar esta biblioteca para empezar oculto y luego hacer la transición para que tu elemento sea visible.

Ahora que hemos inicializado el comportamiento, nuestro controlador tiene mágicamente dos nuevos métodos: `leave()` y `enter()`. Aquí abajo, en `close()`, en lugar de eliminar el elemento nosotros mismos, digamos `this.leave()`:

[[[ code('eea4e4cc06') ]]]

¡Probemos esto! Gira, actualiza y guarda. Observa. Ah, ha sido rápido, ¡pero es exactamente lo que queríamos! Nuestra notificación de tostada está pulida y lista.

La aventura de mañana: sumergirnos en la tercera y última parte de Turbo: Los flujos. Éstos son la navaja suiza de Turbo, y nos permitirán resolver toda una nueva serie de problemas.
