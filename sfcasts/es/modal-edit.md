# ¡Más con Modales divertidos! Editar y borrar

Bienvenido al día 23, el gran final de nuestra saga de sistemas modales. Aunque volveremos sobre ello dentro de unos días, cuando hablemos de los componentes Twig.

Así que si nuestro nuevo sistema modal es tan reutilizable como he prometido, también deberíamos poder abrir fácilmente el formulario de edición en un modal, ¿verdad?

## Abrir el formulario de edición en un modal

Para optar por el sistema modal, lo único que tenemos que cambiar -en`edit.html.twig` - es ampliar `modalBase.html.twig`. Y ya que estamos aquí, quita el relleno extra con `modal:m-0` y `modal:p-0`:

[[[ code('1a40b34384') ]]]

A continuación, haz que el enlace de edición se dirija al marco `modal`. Esto está en `_row.html.twig`. Lo dividiré en varias líneas: .... y luego añadiré `data-turbo-frame="modal"`:

[[[ code('aafce9baf2') ]]]

Momento de la verdad. Actualiza. Y... ¡maldita sea! ¡Simplemente funciona! Incluso si guardamos con éxito, funciona. Obtenemos la tostada, el modal se cierra, ¡madre mía!

Esto funciona porque, en `VoyageController`, la acción `edit`, al igual que `new`, redirige a la página `index`:

[[[ code('654d1ad580') ]]]

Que tiene un marco modal vacío, por lo que el modal se cierra.

## Cuando el modal no se cierra

Pero... Quiero ser delicado. El formulario de edición aparece ahora en dos contextos, el modal, pero también en su página independiente. ¿Qué pasa si, cuando estamos en esta página, al tener éxito, queremos redirigirnos de nuevo aquí para poder seguir editando?

¡Es fácil! Cambia la ruta a `app_voyage_edit` y pon `id` a `$voyage->getId()`:

[[[ code('98da2b7f5a') ]]]

Genial. Ahora, cuando guardemos, ¡funcionará! Pero... ¿cómo ha afectado eso al formulario del modal? Cuando editamos y guardamos... no pasa nada. El modal sigue aquí y no hay notificación de tostado.

## Renderizar los "flujos de fotogramas" en todos los fotogramas

Trabajemos primero en la notificación de brindis que falta. En `base.html.twig`, dentro del marco `modal`, renderizamos los mensajes flash en un `<turbo-stream>`. El problema es que... cuando redirigimos a la página de edición, como ésta extiende`modalBase.html.twig`, el marco que se devuelve es éste. Y este`<turbo-frame>` no renderiza estos flujos.

Resulta que, en realidad, estas líneas deberían vivir dentro de cualquier `<turbo-frame>` que pudiera renderizarse tras el envío de un formulario.

Para ello, copia esto y, dentro del directorio `templates/`, crea un nuevo archivo llamado `_frameSuccessStreams.html.twig`. Pégalo dentro:

[[[ code('54d4205c3b') ]]]

Pero antes de usarlo, quiero añadir otro detalle:`if app.request.headers.get('turbo-frame')` es igual a una nueva variable `frame`, entonces renderiza esto, si no, no hagas nada:

[[[ code('05d675e2ca') ]]]

Estoy codificando para un caso extremo, así que deja que me explique. Imagina que tenemos dos elementos`<turbo-frame>` en la misma página: `id="login"` y`id="registration"`. Y ambos incluyen este parcial. En este caso, el `<turbo-frame id="login">` siempre renderizaría los mensajes flash... sin dejar nada para el pobre marco `registration`. Y así, cuando estamos enviando dentro del marco `registration` Turbo Frame... no veríamos las notificaciones del brindis.

Para solucionarlo, cuando utilicemos este parcial - `include('_frameSuccessStreams.html.twig')` - pasa el nombre del marco dentro del que estás: `modal`:

[[[ code('67764ef71f') ]]]

De esta forma, si el marco actual es otro, esto no se comerá los mensajes flash.

Copia esto, y en `modalFrame.html.twig`, pégalo aquí también:

[[[ code('df8b8a61fa') ]]]

¡Hagámoslo! Actualiza, Edita... y guarda. El modal sigue abierto, pero mira ahí detrás: ¡vemos la tostada!

## Cerrar el modal cuando quiere permanecer abierto

Ahora: ¿cómo podemos cerrar este molesto modal? Cuando ponemos un formulario dentro de un marco, puede que nuestro controlador Symfony no necesite cambiar. Los mensajes Flash funcionarán y, dependiendo de dónde redirijas, el modal podría incluso cerrarse.

Pero tienes que preguntarte: ¿dónde están todos los lugares en los que se utilizará mi formulario? y: ¿estoy devolviendo la respuesta adecuada para cada situación? Ahora mismo, en la situación del modal, nuestra respuesta no es la que queremos: no hace que el modal se cierre.

¡Y no pasa nada! Recuerda: además de dejar que el marco Turbo se actualice con el contenido tras la redirección, también podemos utilizar streams para hacer cualquier cosa extra.

En `new.html.twig`, roba el `stream_success` de la parte inferior. En `edit.html.twig`, pega. Esta vez, queremos actualizar el elemento `<turbo-frame id="modal">` para vaciar su contenido y que el modal se cierre. Hazlo con `action="update"`,`target="modal"`, y pon el `<template>` a nada:

[[[ code('956f47d38f') ]]]

En el controlador, para añadir la "cosa extra", copia la sentencia if de`new`... pégala aquí abajo, cambia la plantilla a `edit.html.twig` y... ¡ya deberíamos estar bien!

[[[ code('171686798f') ]]]

Vale, dale a "Editar" y guarda. Hmm, he visto el brindis, pero el modal no se ha cerrado. Déjame ver el flujo para asegurarme de que lo tengo todo. ¡Ah! Con `targets`, utilizas un selector CSS. Pero con `target`, es sólo el id:

[[[ code('7cb25f38dd') ]]]

Así que el Turbo Stream se estaba ejecutando... pero no coincidía con nada.

Intentémoslo de nuevo. Cuando le demos a guardar, eso nos redirigirá de nuevo a la página de edición, y eso va a tener un `<turbo-frame id="modal">` con contenido: no estará vacío. Pero entonces, nuestro flujo debería vaciarlo y el modal debería cerrarse.

Y... ¡precioso!

## Actualizar la fila en Editar

¿Puedo añadir un último detalle para pulir la edición? Sería genial que, cuando cambiáramos un viaje, actualizara la fila al instante. Este es otro "extra", y... va a ser fácil.

Primero, para apuntar esto, en `_row.html.twig`, añade un `id`,`voyage-list-item-`, `{{ voyage.id }}`:

[[[ code('fad88fa0e9') ]]]

Copia eso, dirígete a `edit.html.twig` y añade un Turbo Stream más:`action="replace"` y `target="voyage-list-item-"` `voyage.id` . Añade el`<template>` y luego incluye `voyage/_row.html.twig`:

[[[ code('61f7070fc4') ]]]

Aquí es donde las cosas realmente empiezan a brillar. Edita, elimina esos signos de exclamación y... la página se actualiza al instante. Nuestro modal de edición -incluso con todas las complicaciones que le he echado- ¡está hecho!

## Cómo eliminar

Con nuestros últimos 3 minutos, asegurémonos de que el botón "eliminar" funciona. Oh... ¡funciona! ¡El modal se cierra y aparece la tostada! Como las otras acciones, después de borrar, redirige a la página `index` y el marco vacío `modal` cierra el modal. ¡Es genial!

Excepto... que la fila que he borrado sigue ahí hasta que actualizamos.

Pero espera. El botón de borrar es un formulario que se envía. Y la única razón por la que se envía a `<turbo-frame>` es porque está dentro del marco modal.

Pero la acción de eliminar no necesita enviarse a un marco. Nunca vamos a hacer clic en "Eliminar" y luego querremos mostrar algo en el modal. Una navegación por toda la página estaría bien.

Para ello, en `_delete_form.html.twig`, en el marco, añade `data-turbo-frame="_top"`:

[[[ code('6ea3edc122') ]]]

La redirección provoca una navegación a página completa, lo que está bien.

## Supresión extrafantástica

Aunque, sí, podría ser más suave. Desplázate un poco hacia abajo... y borra uno. La página vuelve al principio.

Como con todo, si esto es importante para nosotros, podemos mejorarlo. Elimina el `data-turbo-frame="_top"`:

[[[ code('a6b9d7bd31') ]]]

Cuando un formulario -incluso nuestro formulario de eliminación- existe dentro de un `<turbo-frame>`, tenemos que preguntarnos: ¿dónde se está utilizando esto y qué tengo que actualizar para que la página sea perfecta después del éxito? En este caso, necesitamos eliminar la fila. Así que tenemos que hacer algo extra, fuera del marco. ¡Y ya sabemos cómo hacerlo!

En `edit.html.twig`, roba el bloque `stream_success`. Luego crea una nueva plantilla llamada `delete.html.twig`. Eliminar no suele tener su propia plantilla... y vamos a utilizarla sólo para `stream_success`. Utiliza ésta, cambia `action` por `remove` y `target` `voyage-list-item-` pero sólo utiliza una variable `id`. Y para eliminar, no necesitamos el `<template>` en absoluto:

[[[ code('bed5f9675d') ]]]

En `VoyageController`, desplázate hacia arriba, roba la declaración if.... y abajo en eliminar, pega eso. Cambia la plantilla a `delete.html.twig` y pasa una variable `id` establecida en `$id`. No podemos usar `$voyage->getId()` porque ya estará vacía desde que la borramos. En su lugar, pasa `$id`... y antes de borrar, establece que:`$id = $voyage->getId()`:

[[[ code('40c4ffb0a8') ]]]

¡Hagámoslo! Desplázate hasta aquí y borra el ID 22. Observa. Bum. La fila desaparece, recibimos la notificación de tostado y la página no se mueve.

Vale, los últimos días han sido... vaya. Mañana nos lo tomaremos con más calma y aprenderemos otra forma de utilizar Turbo Streams. ¡Hasta entonces!
