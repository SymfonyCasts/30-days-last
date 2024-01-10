# Bonus: Más sobre Flowbite

¡Un tema extra! Sí, porque empecé a recibir preguntas -buenas preguntas- sobre Flowbite. El día 5 añadimos Tailwind y presenté Flowbite como un sitio en el que puedes copiar y pegar componentes visuales. Por ejemplo, copias esta marca, la pegas y ¡boom! Tienes un desplegable. Las clases son todas clases estándar de Tailwind.

He mencionado que no necesitas instalar nada. Sin embargo, dependiendo de lo que quieras, esa no es la historia completa... y he confundido a la gente. Así que ¡arreglémoslo!

## Instalar el JavaScript de Flowbite

Más allá de ser una fuente para copiar HTML, Flowbite en sí tiene otras dos características. En primer lugar, tiene una biblioteca JavaScript opcional para potenciar cosas como pestañas y desplegables: un poco de JavaScript para que cuando hagamos clic, esto se abra y se cierre.

En SymfonyCasts no usamos esto... y no funciona bien con Turbo. Al menos no fuera de la caja. Preferimos crear pequeños controladores Stimulus para hacer funcionar cosas como ésta. Pero podemos hacer que funcione el JavaScript de Flowbite.

Coge el código desplegable y pásalo a `templates/base.html.twig`. Justo dentro de `body`, pega

[[[ code('0cc9700c0d') ]]]

Si vamos y refrescamos, puedes ver lo que quiero decir: simplemente funciona. Bueno, visualmente. Pero si hacemos clic, no pasa nada.

Para obtener el JavaScript de Flowbite, busca tu terminal y ejecuta:

```terminal
php bin/console importmap:require flowbite
```

Esto instala `flowbite` y depende de `@popperjs/core`. También coge el archivo CSS de Flowbite... que sólo es necesario si no tienes Tailwind bien instalado. Tenerlo colgado en `importmap.php` es inofensivo, pero vamos a echarlo antes de que me confunda.

Para utilizar el JavaScript, abre `assets/app.js`. Encima `import 'flowbite'`:

[[[ code('6173b39472') ]]]

Vale, actualiza y... ¡funciona!

Pero hay dos... peculiaridades. Echa un vistazo a la consola. Tenemos un montón de errores sobre el modal y el popover. Si utilizas el componente modal de Flowbite, requiere un atributo`data-modal-target` para conectar el botón al objetivo. El problema es que tenemos un controlador modal Stimulus.... y estamos utilizando `data-modal-target`para aprovechar un objetivo Stimulus. Esas dos ideas están chocando. Tendrías que solucionarlo utilizando el sistema modal de Flowbite o cambiando el nombre de tu controlador modal por otro. Lo mismo ocurre con Popover.

## Arreglar Flowbite JS y Turbo

La segunda peculiaridad es que, aunque el JavaScript de Flowbite funciona ahora mismo, en cuanto navegamos, ¡se rompe! Flowbite inicializa el receptor de eventos al cargar la página, pero cuando navegamos y se carga nuevo HTML en la página, no es lo suficientemente inteligente como para reinicializar ese JavaScript. Por eso, en general, escribimos nuestro JavaScript utilizando controladores Stimulus.

Flowbite incluye una versión de sí mismo para Turbo... pero no funciona del todo bien: no se reinicia correctamente al enviar un formulario.

¡No pasa nada! Tenemos los conocimientos necesarios para arreglarlo nosotros mismos. Importa`initFlowbite` desde `flowbite`:

[[[ code('bb9bc1f86c') ]]]

A continuación, en la parte inferior, pegaré dos escuchadores de eventos:

[[[ code('cb91338900') ]]]

Flowbite se encarga de la inicialización en la primera carga de la página. Luego, cada vez que naveguemos con Turbo, se llamará a este método y se reiniciarán los escuchadores. O si hacemos algo dentro de un marco Turbo, se llamará a esto.

Vamos a probarlo. Actualiza. Y... no funciona: Mira: `initFlobite`. ¡Error tipográfico! Arréglalo entonces... ok. Al cargar la página, funciona. Y si navegamos, sigue funcionando.

## El plugin Tailwind de Flowbite

Así que la primera característica instalable de Flowbite es esta biblioteca JavaScript. La segunda es el plugin Tailwind. Añade estilos adicionales si utilizas tooltips, formularios y charts...., así como algunas cosas más. Puedes encontrar el paquete en npmjs.com y navegar por sus archivos para encontrar el plugin: `plugin.js`.

Si utilizas tooltips, añade nuevos estilos, lo mismo para los formularios... y al final, modifica algunos estilos del tema. Esto no es necesariamente algo que necesites, incluso si utilizas parte del JavaScript de Flowbite.

Pero si quieres este plugin, tienes que instalarlo con npm. Hasta ahora, no hemos tenido que hacer nada con npm... ¡y ha sido genial! Pero si necesitas algunas bibliotecas JavaScript, no pasa nada: ese es el trabajo de npm. Lo más importante es que no tenemos un sistema de compilación gigante: simplemente cogemos aquí o allá una biblioteca que necesitamos.

Busca tu terminal y ejecuta `npm init` para crear un archivo `package.json`.

```terminal-silent
npm init
```

Yo ejecutaré `Enter` para todas las preguntas. Luego ejecuta:

```terminal
npm add flowbite
```

Para utilizar esto, abre `tailwind.config.js`... aquí está. Abajo en la sección `plugins`,`require('flowbite/plugin')`:

[[[ code('7bf5c7741a') ]]]

Esto está sacado directamente de su documentación.

Cuando actualizamos, funciona... pero no vemos ninguna diferencia. Como he dicho, no es algo que necesitemos necesariamente. Aunque si abrimos un formulario, eh: ¡nuestras etiquetas de repente son negras! Eso es porque ahora Tailwind piensa que estamos en modo luz... y a mí me daba un poco de pereza estilizar mi sitio para el modo luz.

Por defecto, Tailwind lee de las preferencias de tu sistema operativo si quieres modo claro o modo oscuro. Pero Flowbite anula eso y lo cambia para leer un `class` en tu elemento `body`. Tiene documentación en su sitio sobre cómo puedes utilizar esto e incluso hacer un conmutador de modo oscuro, modo claro.

Pero voy a cambiar esto a la configuración antigua. Digamos `darkMode`, `media`:

[[[ code('9be7e1ca64') ]]]

Compruébalo: actualizar y... ¡volvemos a la normalidad! Así que ese es el plugin Tailwind.

## El Datepicker

Además de estas 2 funciones de Flowbite, también he visto que la gente quiere utilizar su genial plugin datepicker. ¡Así que vamos a ponerlo en marcha!

Este datepicker forma parte de la biblioteca principal de `flowbite`. Pero si quieres importarlo directamente desde JavaScript... entonces, aquí abajo, se supone que debes instalar un paquete diferente. Esto me confundió, la verdad. Pero cópialo, gíralo y ejecútalo:

```terminal
php bin/console importmap:require flowbite-datepicker
```

De vuelta a la parte superior de la documentación, dice que puedes utilizar el datepicker simplemente tomando una entrada y dándole un atributo `datepicker`. Y eso es cierto... excepto que, una vez más, no funcionará con Turbo. Funcionará al principio... pero dejará de hacerlo tras el primer clic.

En lugar de eso, vamos a inicializarlo con un controlador Stimulus, ¡y funcionará de maravilla!

En `assets/controllers/`, crea un nuevo `datepicker_controller.js`. Voy a pegar el contenido:

[[[ code('0e8cf4f002') ]]]

Vamos a adjuntar este controlador a un elemento `input`. En `connect()`, esto inicializa el selector de fecha y pasa a `this.element`. El `format` coincide con el formato por defecto que utiliza el Symfony `DateType`. Y `autohide` hace que el selector de fechas se cierre cuando eliges una fecha, lo cual me gusta.

También voy a cambiar el atributo `type` de `input` por `text` para que no tengamos a la vez el selector de fecha de Flowbite y el selector de fecha nativo del navegador. En `disconnect()`, hacemos algo de limpieza.

Vamos a utilizar esto en el formulario de viaje: para "Salir a las". Abre el tipo de formulario para esto: `VoyageType`. Aquí está el campo. Pasa una opción `attr` con `data-controller`ajustado a `datepicker`:

[[[ code('4978259473') ]]]

¡Vamos a probar esto! Actualiza y... ¡es fantástico!

## Arreglar el Datepicker en un Modal

Aunque... hay un truco. Vuelve atrás y abre este formulario en el modal. Bueno, más o menos funciona. ¿Lo ves? Se esconde detrás del modal. El datepicker funciona añadiendo HTML en la parte inferior de `body`. Pero como no está dentro de `dialog`, aparece correctamente detrás del modal. Es una pena que no funcione mejor con el bonito elemento nativo `dialog`, pero podemos solucionarlo.

En `datepicker_controller.js`, añade una nueva opción llamada contenedor. Esto indica al datepicker en qué elemento debe añadir su HTML personalizado. Digamos`document.querySelector()` y busca un `dialog[open]`. Así que si hay un `dialog`en la página que está abierta, entonces usa ese como contenedor. Si no, utiliza el `body` normal:

[[[ code('ef70262740') ]]]

## Hacer más inteligente el clic modal exterior

¡Y ese pequeño detalle resuelve nuestro problema! Aunque... deja al descubierto otro pequeño problema. ¿Ves cómo el selector de fecha extiende el diálogo verticalmente? Si hacemos clic aquí, técnicamente estamos haciendo clic directamente en el elemento `dialog`... lo que activa nuestra lógica de clic fuera.

Para solucionarlo, hagamos nuestro controlador `modal` un poco más inteligente. En la parte inferior, pegaré un nuevo método privado llamado `isClickInElement()`:

[[[ code('75a3765b3c') ]]]

Si le pasas un evento de clic, mirará las dimensiones físicas de este elemento y verá si el clic fue dentro.

Aquí arriba, en `clickOutside()`, vamos a cambiar las cosas. Copia esto, y si el `event.target` no es el `dialog`, definitivamente no estamos haciendo clic fuera. Así que, vuelve.

Y si no, `this.isClickInElement()` - pasando por `event` y `this.dialogTarget` - así que si no hicimos clic dentro de `dialogTarget` - entonces definitivamente queremos cerrar:

[[[ code('3d4928ab95') ]]]

Un poco más de lógica, pero un poco más inteligente. Pruébalo. Abre el modal y si hacemos clic aquí abajo... el calendario se cierra -lo cual es correcto- pero el modal permanece abierto. ¡Me encanta!

Así que espero que eso explique un poco más Flowbite. Personalmente, no quiero la mayoría de estas cosas, así que voy a eliminarlas. Dentro de `tailwind.config.js`, elimina el plugin:

[[[ code('93948ee692') ]]]

Luego elimina `package.json` y `package-lock.json`.

Tampoco quiero el JavaScript. En `importmap.php`, elimina `flowbite` y`@popperjs/core`:

[[[ code('70ffcbd837') ]]]

Pero ese datepicker mola, así que conservémoslo.

En `app.js`, elimina la importación de `flowbite` y las dos funciones de la parte inferior:

[[[ code('8e991d3b0a') ]]]

Por último, en `base.html.twig`, elimina ese desplegable aleatorio:

[[[ code('91376a3226') ]]]

Ahora... ¡se acabaron los errores de JavaScript! Pero como ese datepicker era muy chulo, lo seguimos teniendo.

Bien, ¡capítulo extra terminado! Ahora a trabajar, ¡hasta luego!
