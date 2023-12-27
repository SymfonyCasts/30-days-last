# Componente Twig modal

Hoy es un buen día. Hoy vamos a combinar nuestro sistema modal con componentes Twig para conseguir un objetivo Quiero poder añadir rápidamente un modal en cualquier parte de nuestra app.

## Crear el componente modal

Comienza en `base.html.twig`. En la parte inferior, copia el marcado modal. Ya ves... es bastante: no es algo que queramos copiar y pegar en otro sitio:

[[[ code('e567dc63b8') ]]]

Cópialo y luego bórralo. Vamos a crear un componente `Modal`, esta vez a mano. Crea un nuevo archivo en `templates/components/` llamado `Modal.html.twig`, y pégalo:

[[[ code('b7bc621ebb') ]]]

Como dije con el `Button`, no necesitamos una clase PHP para un componente. Como no tenemos ninguna, lo llamaremos "componente anónimo".

Encima, renderiza `attributes`... luego añade `.defaults()` para que podamos mover estos dos atributos a eso. Pega... luego cada uno de ellos necesita un cambio de imagen para encajar como claves y valores Twig en lugar de atributos HTML:

[[[ code('104a39aa15') ]]]

¡Me gusta! Sobre `base.html.twig`, renderiza esto con `<twig:Modal>`. ¡Qué fácil!

## Añadir bloques al componente

Sin embargo, fíjate bien en `Modal.html.twig`: hay algunas cosas que no deberían estar aquí. Por ejemplo, ¡el `<turbo-frame>`! No todos los modales necesitan un marco. Muchas veces, renderizaremos un modal con contenido simple y codificado dentro.

Copia esto y sustitúyelo, por supuesto, por `{% block content %}` y `{% endblock %}`:

[[[ code('3f7c683163') ]]]

En `base.html.twig`, pega el marco... y añade una etiqueta de cierre:

[[[ code('f809fc4c16') ]]]

¡Sigamos! ¿La plantilla de carga de aquí abajo? Sí, también es algo específico de este modal. Si nuestro modal es un mensaje hardcoded, ¡no necesitará esto en absoluto!

Copia el `div` de carga , bórralo, luego alrededor del `<template>` añade: if`block('loading_template')`:

[[[ code('54eed16182') ]]]

Así que si pasamos el bloque, renderízalo dentro de `<template>`.

De vuelta en `base.html.twig`, en cualquier lugar, define ese bloque. Pero en lugar de la etiqueta normal`{% block %}` -que funcionaría-, cuando estás dentro de un componente Twig, puedes utilizar una sintaxis especial `<twig:block>` con `name="loading_template"`. Luego, pega:

[[[ code('ff5f0d21e5') ]]]

Acabamos de mover un montón de cosas. Y aún así... ¡el modal existente sigue funcionando! Y ahora, tenemos un componente modal más esbelto y eficaz que podemos reutilizar en otros lugares.

## Eliminar el modal con contenido personalizado

Hagamos exactamente eso. Quiero añadir un enlace de borrado en cada fila que, al hacer clic, abra un modal con una confirmación. Abre `_row.html.twig`. Copia el enlace de edición, pégalo y llámalo eliminar:

[[[ code('0afe531eed') ]]]

Para que esto funcione, una opción es crear una nueva página independiente de confirmación de eliminación, apuntar a ella y... ¡listo! El `data-turbo-frame="modal"`cargaría esa página en el modal.

Pero como ya hemos hecho eso antes, vamos a probar algo diferente. Elimina el `href`, cámbialo por un `button`, elimina el atributo `data-turbo-frame`... y cambia los colores amarillos por rojos:

[[[ code('6f6eee904c') ]]]

Vamos a comprobar el aspecto. ¡Qué bonito!

De vuelta, pegaré un modal:

[[[ code('0cdedf2ec9') ]]]

Aquí no hay nada especial. La gran diferencia es que, en lugar de un `<turbo-frame>`, el contenido que necesitamos está justo aquí. Cuando actualizamos, cada fila tiene ahora un cuadro de diálogo de borrado en su interior. ¡Pero no pasa nada! No está abierto, así que es invisible.

## Abrir el modal

Ahora viene la parte complicada. Cuando hagamos clic en este botón, tendremos que abrir este modal. Para que esto ocurra, necesitamos que el botón viva físicamente dentro del elemento`data-controller="modal"` para que pueda llamar a la acción `open` en el controlador modal Stimulus.

Para permitirlo, dentro de la plantilla modal, añade un nuevo bloque llamado `trigger`,`endblock`:

[[[ code('aac022e763') ]]]

Ahora, si tienes un botón que activa la apertura del modal, ¡puedes ponerlo aquí! En `_row.html.twig`, copia el botón, quítalo, pon`<twig:block name="trigger">` y pégalo.

Y como estamos dentro del modal, añade `data-action="modal#open"`:

[[[ code('9976180ff4') ]]]

¡Probemos esto! ¡Qué emoción! ¡Actualiza! El estilo se ha vuelto raro. Antes teníamos tres etiquetas `a`, que son elementos en línea. Ahora tenemos dos elementos en línea y un elemento de bloque. Así que es algo que cambia ligeramente, pero tiene fácil arreglo. Arriba, en `<td>`, añade una clase `flex`:

[[[ code('11a66d8ecb') ]]]

## Tamaño Modal Condicional y la Etiqueta props

Y ahora... mucho mejor. Y lo que es más importante, cuando pulsamos Eliminar, ¡el modal se abre! Sin embargo, ya me conoces, quiero que esto sea perfecto. Y no estoy contento con el tamaño que tiene. Cuando abro el formulario de edición, tiene sentido que ocupe la mitad del ancho de la pantalla. Pero cuando abro el de borrar, deberíamos dejar que se redujera al tamaño del contenido que hay dentro.

Para ello, en este caso, quiero pasarle una nueva bandera llamada `allowSmallWidth`establecida en `true`:

[[[ code('d17fb3cb1e') ]]]

He añadido este `:` porque, si paso `allowSmallWidth="true"`, pasará la cadena `true`. Al añadir dos puntos, esto se convierte en código Twig, por lo que pasará el booleano `true`. Ambos funcionarían... pero me gusta ser más estricto.

Con el `Button`, aprendimos que si quieres que esto se convierta en una variable en lugar de un atributo, puedes añadir una propiedad pública con ese mismo nombre. Y podríamos crear un nuevo archivo `Modal.php`.

Pero hay otra forma de convertir un atributo en una variable cuando se utiliza un componente anónimo. En la parte superior de `Modal.html.twig`, añade una etiqueta `props`que es especial para los componentes Twig. Añade `allowSmallWidth` y ponla por defecto en`false`:

[[[ code('91d719b89f') ]]]

Genial, ¿eh? A continuación, queremos hacer que este ancho mínimo sea condicional. Digamos`{{ allowSmallWidth }}` - si es cierto, no renderiza nada, sino renderiza el`md:min-w-[50%]`:

[[[ code('6d86576335') ]]]

De vuelta a la página, el enlace de edición sigue abriéndose a media anchura... pero el enlace de borrado, ¡ah, es bonito y pequeño! ¡Ahora se merece un contenido de verdad! En `_row.html.twig`, después de `<h3>`, añadiré algo de estilo... luego quiero un botón de cancelar que cierre el modal. Para eso, podemos ir a la vieja escuela. Añade un `<form method="dialog">`, y dentro un `<twig:Button>` que diga Cancelar. Y quiero que el botón parezca un enlace, así que añade `variant="link"`:

[[[ code('48eaf83992') ]]]

Eso aún no existe, así que en la clase `Button`, añádelo: `variant` y sólo necesita `text-white`:

[[[ code('06f33dc083') ]]]

Después del formulario, para renderizar el botón de eliminar, incluye `voyage/_delete_form.html.twig`:

[[[ code('76b482dc3e') ]]]

Ah, y esa plantilla tiene incorporado `confirm`. Elimínalo porque ahora tenemos algo mucho mejor.

¡Momento de la verdad! Actualiza y borra. ¡Queda genial! Cancelar cierra el modal... y borrar funciona. Y no debería sorprender que funcione. El formulario de borrado no está dentro de un `<turbo-frame>`. Así que cuando hacemos clic en borrar, se activa un envío de formulario normal que redirige y provoca una navegación normal por toda la página.

## Ocultar las opciones de búsqueda en un modal

Vale, sé que esto ya es un día completo, pero quiero utilizar el modal en un punto más. Y es un caso de uso genial.

En la página de inicio, en mi código PHP y Symfony, no lo mostraré, pero ya he añadido lógica para filtrar esta lista por los planetas. Sólo que no añadí ninguna casilla de verificación de planetas a la página porque... en realidad no tenemos espacio para ellas.

Así que ésta es mi idea: añade un enlace aquí que abra un modal que contenga las opciones de filtrado adicionales.

Abre `main/homepage.html.twig` y busca esa entrada. Empieza añadiendo un`<div class="w-1/3 flex">`... añade el cierre al otro lado de la entrada... luego elimina `w-1/3` de la entrada. Estamos haciendo espacio para ese enlace:

[[[ code('a439553cea') ]]]

Pero pegaré un modal completo:

[[[ code('9d5cd71d64') ]]]

Esto será invisible excepto por el desencadenante. Así que básicamente acabamos de añadir un botón que dice "opciones". Pero ya está configurado para abrir el modal. Dentro, para empezar, tenemos un `h3` y un `<twig:Button>` que cierra el modal.

## Añadir un botón de cierre del modal

Pero el resultado cuando hago clic en opciones... ¡es bonito! Aunque, necesita un botón de cierre en la parte superior derecha. Podríamos añadirlo sólo a este modal... pero estaría bien que fuera una opción del componente modal.

¡Hagámoslo! En `Modal.html.twig`, añade una prop más llamada `closeButton`por defecto a `false`:

[[[ code('509a7675ca') ]]]

Si es así, al final de `dialog`, pegaré un botón de cerrar:

[[[ code('3271046149') ]]]

De nuevo, aquí no hay nada especial: algo de estilo absoluto, un icono... y la parte importante: llama a `modal#close`.

En `homepage.html.twig` encuentra ese modal y añade `closeButton="true"`... pero con el `:` como la última vez:

[[[ code('dfbfdc9ff3') ]]]

¡Vamos a verlo! ¡Me encanta!

Por último, vamos a escarchar este pastel. Cerca de la parte inferior del contenido, pegaré las casillas de verificación del planeta:

[[[ code('fc38d63acb') ]]]

¡Esto es más código aburrido! Mi controlador Symfony ya está configurado para leer el parámetro `planets` y filtrar la consulta.

Prueba final. Ábrelo. ¡Precioso! Ahora observa: haz clic en algunos. Cuando pulse "Ver resultados", la tabla debería actualizarse. Bum. ¡Se ha actualizado!

Pero lo mejor es... ¡cómo ha funcionado! Piénsalo: Pulso este botón... y la tabla se recarga. Eso significa que el formulario se está enviando. Pero... ¿qué lo ha provocado? Mira el botón: no hay código para enviar el formulario. Entonces, ¿qué está pasando?

Recuerda: este `button`, las casillas de verificación del planeta y este modal viven físicamente dentro del elemento `<form>`. ¿Y qué ocurre cuando pulsas un botón que vive dentro de un formulario? ¡Envía el formulario! Ejecutamos el `modal#close`, pero también permitimos que el navegador realice el comportamiento por defecto: enviar el formulario. ¡Esto es tecnología alienígena antigua en acción!

En cuanto al botón de cierre, he sido un poco astuto. Cuando lo añadí, incluí un`type="button"`. Eso le dice al navegador que no envíe ningún formulario que pueda estar dentro. Por eso, cuando hacemos clic en "X", no se actualiza nada. Pero cuando hacemos clic en "ver resultados", el formulario se envía.

¡Woh! ¡El mejor día de todos! Mañana veremos los componentes en vivo, en los que tomamos componentes Twig y dejamos que se reproduzcan en la página mediante Ajax cuando el usuario interactúa con ellos.
