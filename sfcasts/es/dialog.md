# Diálogo HTML para Módulos

Bienvenido al día 19. Hoy tenemos la suerte de jugar con un elemento HTML poco conocido que es absolutamente genial cuando se trata de construir modales. El elemento `<dialog>`. Si tienes prisa por la magnificencia de los modales, puedes saltar más adelante para engancharte al marcado final y al controlador Stimulus. Pero te prometo que el viaje de hoy va a ser divertido.

Abre `templates/voyage/index.html.twig`. En `h1`, voy a pegar algo de contenido nuevo:

[[[ code('57e3c054e3') ]]]

Esto añade un botón "Nuevo viaje".

En la parte inferior, eliminaré el botón antiguo. Este nuevo código no tiene nada de especial: es sólo... un botón. Y cuando vayamos a la página correcta... ¡ahí está! Pero todavía no hace nada.

## Hola `<dialog>`

De vuelta en la plantilla, justo después del botón, añade un elemento `<dialog>`. Dentro proclamaré "Soy un diálogo". Añade también un atributo `open`:

[[[ code('6065f2c526') ]]]

Pulsa actualizar y contempla el elemento `dialog`. Es... interesante. El `dialog` está absolutamente posicionado en la página, centrado horizontalmente y cerca, pero no arriba verticalmente. Eso es porque el elemento `<dialog>` está diseñado para modales... o realmente para cualquier diálogo, como una alerta desechable o cualquier subventana. Es un elemento HTML normal, pero con un montón de superpoderes que vamos a experimentar.

## Hacer un diálogo bonito

Pero primero, tenemos que hacerlo más bonito. De vuelta a la plantilla, pegaré encima ese diálogo:

[[[ code('5466d3a737') ]]]

Esto es una adaptación de Flowbite con algo de ayuda de la IA. Y un diseñador podría crear esto sin problemas. Porque, no hay nada especial: seguimos teniendo un `dialog`, sigue siendo `open`... e incluso las clases Tailwind son bastante aburridas. Establezco una anchura... y redondeo las esquinas. Pero la mayoría de los detalles de posicionamiento ya están incorporados en el elemento. Y la mayor parte del código es contenido modal ficticio para empezar.

El resultado... es impresionante. Aunque... ¡el botón de cerrar aún no hace su trabajo! No te preocupes: ¡ésta es una gran oportunidad para mostrar uno de los superpoderes de diálogo!

Busca el botón de cerrar. A su alrededor, añade un `<form method="dialog">`:

[[[ code('2c2bfb5a3b') ]]]

Este es un botón normal: naturalmente enviará el formulario cuando lo pulsemos, pero el botón no tiene nada especial.

Pero ahora, cuando hagamos clic en X... ¡se cerrará!

## Abrir con un Controlador de Stimulus modal

Para hacer brillar realmente el elemento `<dialog>`, necesitamos un poco de JavaScript. 
Dirígete a `assets/controllers/` y crea un nuevo archivo llamado `modal_controller.js`. Haré trampas, robaré algo de contenido de otro controlador... y lo limpiaré. Este controlador será sencillo. Empieza añadiendo un `static targets = ['dialog']`para que podamos encontrar rápidamente el elemento `<dialog>`. A continuación, añade un método `open`. Aquí, digamos `this.dialogTarget.show()`:

[[[ code('bf34ee43f4') ]]]

Éste es otro superpoder del elemento `<dialog>`: ¡tiene un método `show()`! Integrada en el elemento `<dialog>` está esta idea central de mostrar y ocultar.

Para utilizar el nuevo controlador, en `index.html.twig`, busca el `div` que contiene el `button` y el `dialog` y añade `data-controller="modal"`. Luego, en el botón, di `data-action="modal#open"`:

[[[ code('8ffa6f2ed2') ]]]

Por último, tenemos que establecer el `<dialog>` como objetivo. Elimina el atributo `open` para que empiece cerrado y sustitúyelo por `data-modal-target="dialog"`:

[[[ code('eb521d9e03') ]]]

¡Me gusta! Aquí empieza cerrado. Y cuando hagamos clic, ¡abre! Cerrar, abrir, ¡cerrar!

## Abrir como modal

Un elemento `<dialog>` tiene dos modos: el modo normal que hemos estado utilizando y un modo modal... que es mucho más útil. Para utilizar el modo modal, en lugar de `show()`, utiliza `showModal()`:

[[[ code('5e680c4cf5') ]]]

Ahora, cuando hacemos clic, se sigue abriendo, pero hay algunas diferencias sutiles. La primera es que podemos cerrarlo pulsando `Esc`. ¡Genial! La segunda es que tiene un fondo. Observa: cuando haga clic, la pantalla se oscurecerá un poco. ¿Lo has visto? Esto también me impide interactuar con el resto de la página. Y esto nos sale gratis gracias a `<dialog>`. Eso es enorme.

## Estilizar el telón de fondo

Inspecciona y busca el elemento `<dialog>` - ahí está. El telón de fondo se añade a través de un pseudoelemento llamado `backdrop`. Así que se encarga de añadirlo por nosotros... pero es un elemento real al que se le puede aplicar estilo. ¡Y quiero darle estilo!

De vuelta a la plantilla, busca el elemento `dialog`. Gracias a Tailwind, podemos aplicar estilo directamente al pseudoelemento telón de fondo. Añade `backdrop:bg-slate-600` y`backdrop:opacity-80`:

[[[ code('e881c041d7') ]]]

Observa el efecto. Esto empieza a ser muy, muy suave.

## Eliminar el desplazamiento de página de fondo

Una cosa que el elemento `dialog` no maneja automáticamente es que... la página del fondo sigue desplazándose. No afecta a nada... pero no es el comportamiento que esperamos.

Para solucionarlo, en el método `open()`, di `document.body` para obtener el elemento body, `.classList.add('overflow-hidden')`:

[[[ code('a5df7ec1bf') ]]]

Y ahora... ¡eso es lo que queremos!

## Limpieza al cerrar

Aunque... si cerramos, ¡todavía no puedo desplazarme! Tenemos que eliminar esa clase.

Para ello, copia el método `open()`, pégalo y llámalo `close()`. Para cerrar el diálogo, llama a `close()`... y luego elimina `overflow-hidden`:

[[[ code('f41a52c94b') ]]]

¡Me gusta! Sólo hay un pequeño problema: ¡no estamos llamando al método `close()`! Si pulsamos X o Esc, el diálogo se cierra, sí, pero sigo sin poder desplazarme porque nada llama a este método `close()` en nuestro controlador.

Afortunadamente, el elemento `dialog` nos cubre las espaldas. Cada vez que un elemento `dialog` se cierra -por cualquier motivo-, envía un evento llamado `close`. Podemos escucharlo.

En el elemento `<dialog>`, añade un conjunto `data-action` a `close->modal#close`:

[[[ code('b5f516bd65') ]]]

Así, independientemente de cómo se cierre `dialog` -presionaré Escape-, ahora podemos desplazarnos porque se ha llamado al método `close()` de nuestro controlador.

## Difuminar el fondo

***TIP
Gracias a la ayuda de Rob Meijer, puedes hacer esto en CSS puro. En el elemento `<dialog>` utiliza `backdrop:bg-opacity-80` en lugar de `backdrop:opacity-80` y luego añade `backdrop:backdrop-blur-sm`. ¡No necesitas JS!
***

Vale, estoy emocionado. ¿Qué más podemos hacer? ¿Qué tal difuminar el fondo? Puedes intentar hacerlo difuminando el fondo. Yo lo he intentado... pero no he conseguido que funcione. No pasa nada. Lo que podemos desenfocar es el cuerpo. Añade una clase más: `blur-sm` y elimina la `blur-sm` en `close()`:

[[[ code('317bb81836') ]]]

Veamos cómo queda. ¡Esto sí que mola!

## Cerrar al hacer clic fuera

Pero si intento hacer clic fuera del modal, no se cierra. Esa es otra cosa que el elemento `dialog` no maneja. Afortunadamente, hay una solución rápida.

Arriba, en el elemento raíz de nuestro controlador... En realidad, podemos ponerlo aquí abajo, en el elemento `dialog`. Añade una nueva acción: `click->modal#clickOutside`:

[[[ code('325c02d4fe') ]]]

Apuesto a que parece raro -se llamará cada vez que hagamos clic en cualquier parte del diálogo-, así que vamos a escribir ese método. Digamos `clickOutside()`, dale un argumento `event`, luego si `event.target === this.dialogTarget`, `this.dialogTarget.close()`:

[[[ code('df205cb52c') ]]]

***TIP
Para que el "clic fuera" funcione perfectamente, en lugar de añadir relleno directamente a `dialog`, añade un elemento dentro y dale el relleno. Ya lo hemos hecho, pero es un detalle importante.
***

`event.target` será el elemento real que ha recibido el clic. Resulta que la única forma de hacer clic exactamente en el propio elemento `dialog` es si haces clic en el fondo. Si haces clic en cualquier otro lugar del interior, `event.target` será uno de estos elementos. Así que son tres ingeniosas líneas de código, pero el resultado es perfecto. Haz clic aquí, sin problemas. Haz clic ahí, cerrado.

## Animación CSS para el fundido de entrada

Llegados a este punto, ¡estoy contento! Pero este tutorial no trata de hacer cosas buenas, sino cosas geniales. Siguiente paso: Quiero que el elemento `dialog` se desvanezca. Podríamos hacerlo con una transición CSS. Pero otra opción es una animación CSS. Lo sé, transiciones, animaciones... CSS tiene muchas.

Una animación es algo que aplicas a un elemento y... simplemente... hará esa animación para siempre. O puedes hacer que se anime sólo una vez. Por ejemplo, podemos hacer que este botón se anime arriba y abajo para siempre. Una de las cosas buenas de las animaciones es que puedes hacer que una animación sólo ocurra una vez... y no empezará hasta que el elemento se haga visible en la página. Por ejemplo, podríamos crear una animación de opacidad 0 a opacidad 100, que se ejecutaría en cuanto nuestro `dialog` se hiciera visible.

Tailwind tiene algunas animaciones incorporadas, pero no una para el desvanecimiento. Así que la añadiremos. Abajo, en `tailwind.config.js`, pegaré sobre la tecla `theme`:

[[[ code('502c1a6c08') ]]]

Esto es principalmente material de animación CSS: añade una nueva llamada `fade-in` que pasará de opacidad 0 a 100 en 1/2 segundo.

Para utilizarlo, busca el elemento `dialog` y añade `animate-fade-in`:

[[[ code('21f4765878') ]]]

Pruébalo. ¡Precioso! ¿Podríamos desvanecerlo? Claro, pero en realidad me gusta que se cierre inmediatamente. Así que voy a omitirlo.

## Módulos y caché de página turbo

Vale, tengo un último detalle antes de despedirte por hoy. Cuando añadimos las transiciones de vista, en `app.js`, desactivamos una función de Turbo llamada caché de página... porque aparentemente no siempre funciona bien con las transiciones de vista. Cuando las transiciones de vista sean estándar en Turbo 8, supongo que esto no será un problema.

De todos modos, cuando la caché está activada

[[[ code('72f086c743') ]]]

en el momento en que haces clic fuera de una página, Turbo toma una instantánea de la página antes de navegar fuera. Cuando volvemos a hacer clic, es instantáneo: ¡boom! En lugar de hacer una petición a la red, utiliza la versión en caché de esta página. Hay más cosas, pero captas la idea.

Con el almacenamiento en caché activado, una cosa de la que tenemos que preocuparnos es de eliminar cualquier elemento temporal de la página antes de que se tome la instantánea, como mensajes tostados o modales. Porque, cuando hagas clic en "Atrás", no querrás que haya una notificación tostada aquí arriba.

La forma en que solemos resolver esto, por ejemplo en `_flashes.html.twig`, es añadir un atributo `data-turbo-temporary`:

[[[ code('071fafff5c') ]]]

Que le dice a Turbo que elimine este elemento antes de tomar la instantánea.

Probemos a añadir esto a nuestro `dialog` para que no aparezca en la instantánea. Para ver qué ocurre, abre el modal y haz clic atrás. Eso acaba de tomar una instantánea de la página anterior. Ahora haz clic hacia adelante. Vaya. Estamos en un estado extraño. Parece que el diálogo ha desaparecido... pero no podemos desplazarnos y la página está borrosa.

Eso es porque necesitamos hacer algo más que ocultar el `dialog`: necesitamos eliminar estas clases del cuerpo. Básicamente, antes de que Turbo tome la instantánea, ¡necesitamos algo que llame al método `close()`!

¡Y podemos hacerlo! En `index.html.twig`, en el elemento controlador raíz -aunque esto podría ir en cualquier sitio- añade un `data-action=""`. Justo antes de que Turbo tome su instantánea, envía un evento llamado `turbo:before-cache`. Podemos escucharlo y luego llamar a `modal#close`. El único detalle es que el evento `turbo:before-cache` no se envía a un elemento específico. Así que escucharlo en este elemento no funcionará. Se envía por encima de nosotros, a la ventana. Es un evento global.

Afortunadamente, Turbo nos proporciona una forma sencilla de escuchar los eventos globales añadiendo`@window`:

[[[ code('67c7f4b82f') ]]]

Es un poco técnico, pero con este único arreglo, podemos abrir el modal, retroceder, avanzar, y la página queda preciosa.

¡Guau! Hoy ha sido un día enorme, ¡pero mira lo que hemos conseguido! Un bonito sistema modal sobre el que tenemos un control total. Mañana va a ser igual de grande, ya que daremos vida a este modal con contenido y formularios dinámicos reales. Hasta entonces.
