# Ver Transiciones

Día 15 Ya hemos llegado a la mitad de nuestra aventura. Y a partir de ahora la cosa no hará más que enfriarse.

Para celebrarlo, hoy trabajaremos en algo nuevo y brillante: la API de Transiciones de Vista. Esta ingeniosa novedad es compatible con la mayoría de los navegadores y nos permite realizar transiciones suaves cada vez que cambia el HTML de nuestra página.

***TIP
En realidad, a fecha de diciembre de 2023, las transiciones de vista sólo están soportadas en Chrome, aunque se dice que está prevista su compatibilidad con Firefox y Safari.
***

Y si tu usuario tiene un navegador que no lo soporta, ¡no pasa nada! La transición simplemente se salta, pero todo sigue funcionando. No pasa nada.

Ah, y las Transiciones de Vista vendrán de serie en Turbo 8 para la navegación por toda la página, aunque las llevaremos un poco más lejos.

## Marcianos malvados y demostración de las transiciones de vista

Para utilizar las Transiciones de Vista, no necesitas ninguna biblioteca externa. Pero vamos a utilizar una llamada "turbo view transitions". Esto surgió de una serie de blogs en los que el autor construyó un estupendo proyecto llamado [Turbo Music Drive](https://github.com/palkan/turbo-music-drive). En dos entradas del blog Evil Martians, hablan de morphing y de hacer locuras con él en Turbo. ¡Incluso crearon una demostración en directo!

En su forma más simple, las transiciones de vista añaden un fundido cruzado mientras navegas. Pero también puedes ser más específico y conectar elementos entre páginas para darles una transición especial. Mira: cuando hago clic en este álbum, se mueve hacia arriba a la izquierda. También hay un bonito fundido cruzado para el resto de la página.

## Instalación de turbo-view-transitions

¡Vamos a probarlo! Primer paso: consigue la biblioteca `turbo-view-transitions`. En tu terminal, ejecuta:

```terminal
php bin/console importmap:require turbo-view-transitions
```

¡Encantador! Para activar las transiciones, tenemos que hacer dos cosas. Primero, en `base.html.twig`, añade una etiqueta `meta` con `name="view-transition"`:

[[[ code('387e69260a') ]]]

¡Así es como le dices a tu navegador que las quieres!

En segundo lugar, en Turbo 7, tenemos que activar las transiciones en JavaScript. Dirígete a `app.js`. Ésta será una rara ocasión en la que escribamos JavaScript que no necesite vivir en un controlador Stimulus. Copia de su ejemplo, pega... y mueve el `import` a la parte superior:

[[[ code('8471773079') ]]]

¡Listo! ¡Eso debería bastar para que las navegaciones de Turbo Drive utilicen transiciones de vista! Esto aprovecha un evento de Turbo llamado `turbo:before-render`. La función`shouldPerformTransition()` comprueba si el navegador del usuario admite transiciones. Si no lo hace, es un comportamiento normal. Pero si lo hace, entonces`performTransition()` hará la transición entre el cuerpo antiguo y el nuevo. También hay un poco de código aquí abajo para evitar la caché de la página turbo. Creo que es algo que funcionará mejor en Turbo 8 cuando esto sea oficial.

¡Es hora de la gran revelación! Pulsa actualizar y mira. Oooooh. ¡Una suave transición cruzada! Así que no sólo hemos eliminado las recargas completas de página, sino que ahora tenemos una transición entre nuestras páginas. Cuidado Powerpoint, ¡vamos a por ti!

## Transición Turbo Frames

¿Pero qué pasa con los marcos Turbo? Cuando hacemos clic, sigue ocurriendo instantáneamente. Activamos las transiciones para las navegaciones de página completa, pero no para los marcos. ¿Podemos? ¡Claro!

Copia esta escucha, y pégala debajo. Esta vez, escucha`turbo:before-frame-render`... y ajusta esta parte. En lugar de `document.body`, utiliza `event.target`. Ese será el `<turbo-frame>`. Y entonces el nuevo elemento será `event.detail.newFrame`:

[[[ code('190dd54dc5') ]]]

¡Listo! Actualiza y haz clic en .... . Transición, ¡comprobada!

## Depuración de transiciones

Y si la transición no es lo suficientemente obvia, puedes abrir las herramientas de tu navegador, hacer clic en el pequeño "...", ir a "más herramientas" y luego a Animaciones. Parece que ya lo tenía abierto. Aquí puedes cambiar la velocidad de tus animaciones.

Ahora... es super obvio. Incluso puedes ver cómo funciona. Si te desplazas durante la transición, puedes ver cómo toma una captura de pantalla del HTML antiguo y lo mezcla con el nuevo. Este efecto raro no suele ser un problema porque ocurre muy rápido, pero es chulo de ver.

## Caso extremo: Marcos que avanzan

Para mostrar un problema, vamos a eliminar la transición CSS de la tabla que acabamos de añadir. Así que gira hasta esa plantilla... y quita el `class`:

[[[ code('d376ad67d2') ]]]

Actualizar... y pruébalo. No pasa nada. No pasa nada. Es decir, funciona... pero no hay transición de vista. ¿Por qué?

Esto es sutil. La transición se rompe cuando tienes un fotograma que hace avanzar la URL. El problema es que, en esta situación, Turbo llama a `turbo:before-frame-render`... y también llama a `turbo:before-render` justo después. Estas dos, más o menos, chocan.

Pero tiene fácil solución. Crea una variable: `let skipNextRenderTransition` y ajústala a `false`. A continuación, si `shouldPerformTransition()` y no `skipNextRenderTransition`, entonces hazlo:

[[[ code('5af621268f') ]]]

Por último, cuando nuestro fotograma inicie su transición, establece esta variable en true. Incluye también una `setTimeout()`, ponla de nuevo a `false` y retrasa esto 100 milisegundos:

[[[ code('2a1d9cf2c7') ]]]

Es un poco raro. Pero, ¡eso es programar! Establecemos esto en true, Turbo activa el otro oyente casi inmediatamente... y luego a los 100 milisegundos lo reiniciamos. Probablemente también podríamos sustituir el `setTimeout()` configurando `skipNextRenderTransition = false`en el oyente `turbo:before-render`.

Lo más importante es que... ¡ahora tenemos una transición! Volvamos a ponerlo a toda velocidad. ¡Me gusta!

## Desactivar transiciones

Prueba con el marco emergente del planeta. ¡Vaya! Eso ha sido raro. Para ser totalmente sincero, no sé qué está pasando aquí. Por alguna razón, la transición de la vista hace que desaparezca el popover... lo cual no es... digamos... lo ideal. Probablemente haya una forma de arreglarlo, pero no he podido descifrarla.

No pasa nada... y creo que esta rareza está aislada del comportamiento del popover. En su lugar, añadiremos una forma de desactivar las transiciones en un marco.

En la página de inicio, busca `turbo-frame`. Aquí está. Añade un nuevo atributo llamado`data-skip-transition`:

[[[ code('b95a074791') ]]]

Me lo he inventado. Sobre un `app.js`, podemos buscarlo. Si es`shouldPerformTransition()` y no `event.target.hasAttribute('data-skip-transition')`, entonces haz la transición:

[[[ code('86481e7399') ]]]

Ahora... ¡arreglado! Y tenemos transiciones en... prácticamente todos los tipos de navegación de nuestro sitio. ¡Y en sólo unos 10 minutos! ¡Es una locura!

Ahora pasamos a la misión de mañana: crear un deslumbrante sistema de notificaciones tostadas que sea fácil de activar, independientemente de dónde y cómo tengamos que añadirlas. ¡Hasta entonces!
