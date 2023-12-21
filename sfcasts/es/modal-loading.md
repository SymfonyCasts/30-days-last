# Fantástica UX modal con un estado de carga

Sigamos donde lo dejamos ayer. El modal con tecnología Ajax se carga. Intenta enviarlo. Algo ha ido mal. Fue a una página que no tenía `<turbo-frame id="modal">`... lo cual es extraño, porque ahora todas las páginas tienen uno. Eso es porque... la respuesta fue un error. Si miramos abajo en la barra de herramientas de depuración web, había un código de estado 405. Abre eso. Es interesante:

> No se ha encontrado ninguna ruta para `POST` /voyage/

Eso es raro porque estamos enviando el formulario de nuevo viaje... así que la URL debería ser `/voyage/new`.

## Añadir atributos de acción a los formularios

Éste es el problema: cuando generé la basura de la travesía desde MakerBundle, creó formularios que no tienen un atributo `action`. Eso está bien cuando el formulario vive en `/voyage/new` porque sin `action` significa que se devuelve a la URL actual. Pero en cuanto decidimos incrustar nuestros formularios en otras páginas, tenemos que ser responsables y establecer siempre el atributo `action`.

Para ello, abre `src/Controller/VoyageController.php`. En la parte inferior, pegaré un simple método privado. Pulsa Aceptar para añadir la declaración `use`:

[[[ code('bdb5a2a001') ]]]

Podemos pasar un viaje o no... y esto crea el formulario pero establece el `action`. Si el viaje tiene un id, establece la acción en la página de edición, si no, la establece en la página nueva.

Gracias a esto, arriba en la acción `new`, podemos decir `this->createVoyageForm($voyage)`. Copia eso... porque necesitamos la línea exacta abajo en `edit`:

[[[ code('75ecaf0d60') ]]]

Encantador. De vuelta, ni siquiera necesitamos actualizar. Abrimos el modal, guardamos y... Ah, ¡es absolutamente encantador! Se ha enviado y recibimos la respuesta justo dentro del modal. Porque... ¡por supuesto! Ese es el objetivo de un marco Turbo. Mantiene la navegación dentro de sí mismo.

## Cargar el modal al instante

Antes de hablar de lo que ocurre en caso de éxito, quiero perfeccionar esto. Mi segundo requisito para abrir el modal es que debe abrirse inmediatamente. En la acción `new`, añade un `sleep(2)`... para simular que nuestro sitio está siendo asaltado por extraterrestres que planean sus viajes de vacaciones de primavera:

[[[ code('de9b4cd4cb') ]]]

Cuando pulsamos el botón ahora... no pasa nada. No hay respuesta del usuario en absoluto hasta que finaliza la petición Ajax. Eso no es suficiente. En lugar de eso, quiero que el modal se abra inmediatamente con una animación de carga.

En el controlador modal, añade un nuevo objetivo llamado `loadingContent`:

[[[ code('a1cf36ab41') ]]]

Ésta es mi idea: si quieres que se cargue contenido, definirás qué aspecto tiene en Twig y establecerás este objetivo en él. Lo haremos dentro de un momento.

En la parte inferior, crea un nuevo método llamado `showLoading()`. Si `this.dialogTarget.open`, es decir, si el diálogo ya está abierto, no necesitamos mostrar la carga, así que devuelve. Si no, digamos `this.dynamicContentTarget` -para nosotros, ese es el `<turbo-frame>`en el que se cargará finalmente el contenido Ajax- `.innerHTML` es igual a`this.loadingContentTarget.innerHTML`:

[[[ code('dfe496d78b') ]]]

Por último, añade ese objetivo. En `base.html.twig`, después del `dialog`, añadiré un elemento`template`. Sí, mi querido elemento `template`: es perfecto para esta situación porque todo lo que haya dentro no será visible ni estará activo en la página. Es una plantilla que podemos robar. Añadiré un `data-modal-target="loadingContent"`. Pondré algo de contenido dentro:

[[[ code('aceedb2abb') ]]]

Aquí no hay nada especial: sólo algunas clases de Tailwind con una animación de pulso muy chula.

Si probamos esto ahora... ¡no se carga el contenido! Eso es porque nada está llamando al nuevo método `showLoading()`. En `base.html.twig`, busca el fotograma. Lo dividiré en varias líneas. Pensemos: en cuanto `turbo-frame` empiece a cargarse, queremos llamar a `showLoading()`. Afortunadamente, Turbo envía un evento cuando inicia una petición AJAX. Y podemos escucharlo.

Añade un `data-action` para escuchar `turbo:before-fetch-request` -así se llama el evento- y luego `->modal#showLoading`:

[[[ code('90fe9a9a9d') ]]]

Muy bien, ¡comprobemos el efecto! Actualiza la página y... ¡oh, es maravilloso! Se abre al instante, vemos que se carga el contenido... ¡y se sustituye cuando termina el marco!

Me encanta cómo funciona esto. Cuando esto llama a `showLoading()`, ese método pone el contenido en `dynamicContentTarget`. Y... ¿recuerdas lo que ocurre en el momento en que cualquier HTML entra ahí? Nuestro controlador se da cuenta y abre el diálogo. ¡Eso sí que es trabajo en equipo!

## Indicación de carga al enviar el formulario

Ya casi lo tenemos perfecto, ¡pero no estoy satisfecho! Mientras aún tenemos el `sleep`, envía el formulario. ¡No ocurre nada! No hay ninguna indicación mientras se carga.

***TIP
Para conseguir un efecto aún más bonito, también puedes cambiar la opacidad sólo si la carga tarda más de, por ejemplo, 700 ms. Para ello, añade una clase `aria-busy:delay-700`.
***

Por suerte para nosotros, ya hemos recorrido este camino antes con otro marco Turbo. Añade la clase`aria-busy:opacity-50`, y `transition-opacity`:

[[[ code('1d00aaaa7c') ]]]

Recargaré... clic, animación de carga y enviar. ¡Sí! La baja opacidad nos indica que algo está pasando.

Y con eso, eliminaré alegremente nuestro `sleep`:

[[[ code('2688d76016') ]]]

## Estilo Modal Condicional

Vale, un último detalle que quiero aclarar: este relleno extra. Existe porque el contenido de la página `new` tiene un elemento con `m-4` y `p-4`. Así que el modal tiene algo de relleno... y el relleno extra proviene de esa página.

En la página, el margen y el relleno tienen sentido. Viene de aquí, de`new.html.twig`. Así que queremos esto en la página completa... pero no en el modal.

Para ayudarnos a hacerlo, vamos a utilizar un truco de Tailwind. En `tailwind.config.js`, añade una variante más. Llámala `modal`, y actívala siempre que estemos dentro de un elemento `dialog`:

[[[ code('ee00b26eb1') ]]]

Ahora, en `new.html.twig`, mantén el margen y el relleno para la situación normal. Pero si estamos en un modal, utiliza `modal:m-0`, y `modal:p-0`:

[[[ code('97c84dc302') ]]]

En la nueva página, esto no debería cambiar. ¡Se ve bien! Pero en el modal... eso es lo que queremos.

Nuestro sistema modal ahora se abre instantáneamente, carga el contenido con AJAX, podemos enviarlo ¡e incluso se cierra solo si tiene éxito! Observa: rellena un propósito, selecciona un planeta... y... ¡el modal se cerró!

¿Cómo? ¡Es genial! La acción `new` redirige a la página índice. Y como`index.html.twig` amplía el `base.html.twig` normal, sí tiene un marco`modal`... pero es ese vacío de la parte inferior. Eso hace que el`turbo-frame` de la página quede vacío. Y gracias a nuestro controlador modal, nos damos cuenta y cerramos el diálogo.

Lo único que nos falta ahora, si estabas atento, ¡es la notificación del brindis! Mañana hablaremos de cómo manejar el éxito cuando se envía un formulario dentro de un marco... incluyendo hacer cosas chulas como añadir automáticamente la nueva fila a la tabla de esta página. Hasta mañana.
