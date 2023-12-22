# ¡Modal AJAX!

Ayer construimos un sistema modal asesino gracias al elemento `dialog`. Con sólo este marcado y un pequeño controlador Stimulus, me siento peligroso.

Así que déjame hablarte del objetivo de hoy, ¡que es grande y audaz! Cuando hago clic en "Nuevo viaje", quiero que el "nuevo formulario modal" se cargue mediante AJAX y aparezca en el modal. ¡Pero más que eso! Cuando envíe el formulario, los errores de validación deben permanecer en el modal, éste debe cerrarse cuando tenga éxito y debemos ver notificaciones de tostadas. Y, quizá lo más importante, quiero que todo este sistema sea reutilizable para que podamos cargar rápidamente cualquier formulario de nuestro sitio en un modal. Vamos a hacerlo, o moriremos en el intento. Espero que lo hagamos... Creo que lo haremos.

## Añadir un Marco modal al Diseño

Para ponerlo en marcha, copia todo el marcado modal. Ya está. Luego entra en`base.html.twig` y, hasta el final, antes de la etiqueta de cierre `body`, pega:

[[[ code('b22f94064a') ]]]

De nuevo en `index.html.twig`, quita la `dialog`... y ya no necesitaremos lo del controlador modal:

[[[ code('49b60af471') ]]]

Esto es ahora un `h1` normal y un botón normal... que no hace nada. En`base.html.twig`, haz lo contrario: quita el `button`, el `h1` y la clase en el div:

[[[ code('fd70f2df79') ]]]

Ahora es un div que contiene un `dialog`... que está cerrado.

Ahora viene el toque mágico: elimina las tripas del `dialog`: conserva sólo estos dos divs: nos ayudan a dar relleno y un buen comportamiento de desplazamiento. Dentro, añade un`<turbo-frame>` con `id="modal"`:

[[[ code('0bcb7e773e') ]]]

Eso, amigos míos, ha sido un movimiento de poder de codificación. Ahora tenemos en cada página un`<turbo-frame id="modal">` en el que podemos cargar contenido dinámicamente Y, ¡vive dentro de un diálogo!

## Cargar contenido en el marco modal

Observa: en la página índice, cambia el botón de nuevo viaje por una etiqueta `a` y establece su`href` en la ruta `app_voyage_new`. Es una etiqueta normal que nos llevaría a esa página. Pero ahora añade `data-turbo-frame="modal"`:

[[[ code('df2cbe6b1b') ]]]

Compruébalo. Actualiza y haz clic. En lugar de cambiar la página, cargó el contenido en el marco `modal`. Pero... no pasó nada.

Vale, hizo una llamada AJAX a la página del nuevo viaje. Pero si la abrimos en una nueva pestaña, no hay ningún marco `modal` en esta página. Bueno, en realidad sí lo hay. Como todas las páginas, en la parte inferior, tiene un marco `modal` vacío. Así que cuando hacemos clic en ese enlace, funciona... pero el resultado es que el marco Turbo se queda vacío. No es muy útil.

Para solucionarlo, en `new.html.twig`, añade un `<turbo-frame id="modal">` alrededor de todo... con una etiqueta de cierre al final:

[[[ code('a85e571f6e') ]]]

Compruébalo. Cuando hacemos clic ahora, ¡sí! Dentro de `<turbo-frame>`, ¡tenemos el formulario! El modal aún no se abre, pero está listo.

## Añadir el diseño base del modal

Ahora, antes de averiguar cómo abrir el modal, tenemos un problema... y una oportunidad. Si fuéramos directamente a la página del nuevo viaje, tendríamos dos elementos`<turbo-frame id="modal">`: el que rodea el formulario y el vacío de la parte inferior. Eso... no es válido.

Además, aunque quiero poder cargar este formulario dentro del modal, también quiero que se comporte de forma normal si vamos a la página directamente. Fíjate: ahora mismo, si relleno esto correctamente y lo guardo, ¡pasan cosas raras! Lo he enviado a `<turbo-frame id="modal">`... y me redirige a la página índice... que tiene ese marco coincidente... pero está vacía.

Eso no es lo que quiero. Si voy a esta página directamente, quiero que actúe como si fuera normal.

Vamos a manejar esto con un truco. En `new.html.twig`, elimina la plantilla `<turbo-frame>`... y extiende una nueva plantilla base llamada `modalBase.html.twig`:

[[[ code('878b84781f') ]]]

Ooh. Copia ese nombre y en el directorio `templates/`, créala: `modalBase.html.twig`. Ésta tendrá una línea: una etiqueta extends que es dinámica. Si`app.request.headers.get('turbo-frame')` es igual a `modal` -por tanto, si se está realizando una petición AJAX a esta página desde el turbo frame `modal`, extiende una nueva`modalFrame.html.twig`. Si no, extiende la normal `base.html.twig`:

[[[ code('7fc5821f78') ]]]

Si vamos a la página normal, se extenderá `base.html.twig`. Aquí no hay turbo marco, es completamente normal, y se enviará de forma normal.

Vamos a crear esa otra plantilla base. Copia su nombre y, en `templates/`, crea `modalFrame.html.twig`. Todo lo que esto necesita es un `<turbo-frame id="modal">`... con `{% block body %}` y `{% endblock %}` dentro:

[[[ code('c714229c01') ]]]

Así, si hacemos una petición a esta página desde el marco `modal`, toda la respuesta será este único marco con el contenido de la página dentro. Eso resuelve nuestro problema. Y significa que si queremos que una página pueda cargar su formulario dentro de un modal... la única línea que debemos cambiar está justo aquí. Lo demostraré el día 23.

## Autoabrir el modal

Pero ahora mismo, volvemos a la situación en la que hacemos clic en este enlace y... si indago en los elementos del modal, se está cargando el formulario en el `turbo-frame`... pero el modal no se abre. ¿Cómo podemos hacerlo?

Bueno, tengo 2 requisitos para abrir el modal. El primero es que quiero que sea superfácil de abrir. Si aparece HTML dentro de este `turbo-frame` -no importa cómo se añada- quiero que el sistema sea lo suficientemente inteligente como para verlo y abrir el modal. Y segundo, quiero que el modal se abra al instante. No quiero hacer clic en este botón... y luego esperar 500 milisegundos antes de ver el modal. Esa no es una buena experiencia de usuario.

Para la primera parte -abrir este modal en cuanto haya contenido en `turbo-frame` - vamos a utilizar un truco dentro de nuestro controlador Stimulus. Permíteme cerrar algunos archivos. En `base.html.twig`, haz que este `turbo-frame` sea un objetivo:`data-modal-target="dynamicContent"`:

[[[ code('ea56292f14') ]]]

Esta es la idea: si un modal tiene este objetivo y el HTML entra dentro de este elemento por cualquier motivo, quiero que nuestro código se dé cuenta y abra el modal. Para ello, en `modal_controller.js`, añade ese objetivo:

[[[ code('7b329d2d06') ]]]

Y luego pegaré el código más complejo que vamos a ver en este tutorial:

[[[ code('97111bc9e5') ]]]

Pero, espera: aunque parezca complejo, lo que está haciendo es sencillo. Si tenemos un objetivo `dynamicContent`, este código vigila ese elemento por si hay algún cambio. Cada vez que hay un cambio, llama a nuestra función. Luego comprobamos si el elemento `dynamicContentTarget` tiene algún HTML. Si lo tiene, ¡ábrelo! Si no, ciérralo. Así de sencillo.

En `disconnect()`, desactivamos ese sistema. Y también, por si acaso, si nuestro elemento controlador modal se elimina alguna vez de la página por cualquier motivo, esto cerrará el diálogo y hará la limpieza.

El resultado de esto es... bastante increíble. Actualiza la página. Vamos a jugar. Voy a editar el `<turbo-frame>` como HTML y escribir: "¿se abrirá?". ¡Boom! Lo hace! Y si vaciamos el contenido... se cierra.

¡Y, lo que es más importante, cuando hacemos clic en el enlace "Nuevo", se abre el formulario! ¡Increíble!

Vale, creo que ya es suficiente por hoy. Mañana, vamos a asegurarnos de que este formulario se envíe. Y como no puedo evitarlo, añadiremos algunas cosas más: como abrir el modal instantáneamente en lugar de esperar a que termine la llamada AJAX.
