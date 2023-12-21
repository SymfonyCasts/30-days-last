# Turbo Streams: Actualiza cualquier elemento

Hoy nos sumergimos de cabeza en el final de la trilogía Turbo: los flujos Turbo: Los flujos nos permiten resolver problemas para los que... aún no tenemos solución.

Tomemos, por ejemplo, nuestra página de inicio: tenemos este sistema de tablas de datos realmente bonito... con un pequeñísimo problema. Cuando escribimos en esta casilla, el número de resultados no cambia. Se queda como estaba al cargar la página. El Marco Turbo está alrededor de esta tabla, por lo que el recuento de resultados está fuera de ella.

Aquí es donde entra en juego Turbo Streams. Cuando trabajas con un Turbo Marco y necesitas actualizar algo fuera de él, necesitas un flujo. Los streams tienen un nombre elegante, pero es una idea sencilla. Un Turbo Stream es en realidad un elemento HTML personalizado. Podría cogerlo, ponerlo en mi página, y se ejecutaría instantáneamente. Encontraría el elemento de la página cuyo `id` es `messages` y añadiría este contenido. Y hay acciones para todo: preañadir, sustituir, actualizar, etc. Podemos utilizar un Turbo Stream para realizar cualquier cambio que queramos en cualquier elemento de la página... desde cualquier lugar. ¡El poder!

## Añadir un `<turbo-stream>` directamente en la página

Para probarlo, copia el Turbo Stream que es una actualización. De vuelta en nuestra página, inspecciona el elemento en el nombre "Invitadores del Espacio". Temporalmente, dale un `id` llamado`company_name` para que podamos apuntar a él.

Ahora, en cualquier otro lugar de la página -qué tal aquí abajo, en el pie de página- edita como HTML y pega ese Turbo Stream. En este caso, queremos que el objetivo sea `company_name`y dentro del elemento de plantilla, di "¡Invasores del Espacio!". Ahora, fíjate en esto. En cuanto haga clic fuera de esto, el elemento personalizado `<turbo-stream>` se activará y ejecutará su acción. Observa. ¡Pum! ¡Ha encontrado ese elemento y lo ha actualizado!

Vuelve a echar un vistazo al pie de página: ¡ese `<turbo-stream>` ha desaparecido! Se ejecuta, luego se autodestruye y se elimina de la página. Es el más noble de los elementos personalizados.

Y aunque estuviera en la página por un momento, recuerda: todos los `<turbo-streams>` tienen un elemento `template` en su interior. Ya hablamos de ese elemento el Día 11: cualquier cosa dentro de un `<template>`... no está realmente en la página: está completamente oculta e inactiva. Así que, aunque estuviera en la página por un momento, sería invisible.

Los flujos simplemente funcionan.

## Actualizar el recuento de resultados con una secuencia

Así que ¡utilicémoslos para resolver nuestro problema! Abre `templates/main/homepage.html.twig`y busca "resultados". Aquí está el elemento que tenemos que actualizar. Para apuntarlo, dale un `id`: ¿qué tal `voyage-result-count`:

[[[ code('60d7223411') ]]]

Cópialo. Cuando buscamos en la página, en realidad es este `<turbo-frame>` el que está navegando. Así que en cualquier lugar dentro de esto -iré a la parte inferior- podemos añadir un `<turbo-stream>`. Di: `<turbo-stream action="replace"`, `target=""` y pega. Luego añade el elemento`<template>` -no lo olvides- y codificaré un mensaje para empezar:

[[[ code('b21ba69993') ]]]

Vale, mira lo que pasa cuando actualizo. ¡Bum! Como el elemento `<turbo-stream>` existe al cargar la página, se ejecuta inmediatamente y sustituye el elemento por el contenido personalizado.

## Sustituir el contenido real por un bloque

Así que ahora... vamos a poner el contenido real. Esencialmente, queremos copiar todo este div... y pegarlo aquí abajo. Pero... sin duplicarlo.

Para ello, utilizaremos un truco con bloques Twig. Rodea el recuento de resultados con un nuevo bloque llamado `result_count`... y a continuación `endblock`:

[[[ code('968975b906') ]]]

En Twig, eres libre de añadir bloques donde quieras. Cuando lo haces, no hacen nada inmediatamente. Cuando esto se renderice, Twig verá este bloque.... lo ignorará... y renderizará el `div` como de costumbre.

Pero ahora, podemos bajar dentro de nuestro `<turbo-stream>` y decir `{{ block('result_count') }}`:

[[[ code('9c0f279769') ]]]

¡Creo que estamos listos! Empieza por ir a la página de inicio para que veamos los 30 resultados completos. Y luego, mientras tecleamos... ¡ah, precioso! El recuento se actualiza a medida que se recargan los resultados. ¡Joder, qué fácil!

Para aquellos de vosotros que sois unos frikis de los detalles, primero, os queremos, y segundo, sí, al cargar la página, estamos renderizando el recuento de resultados dos veces: aquí... y, aunque no podamos verlo, también lo estamos renderizando aquí abajo dentro del Turbo Stream. Así que se está mostrando dos veces dentro del HTML. Y eso no es un problema en absoluto, a menos que, por alguna razón, calcular el recuento de resultados lleve mucho trabajo. Si tuvieras esa situación, podrías establecer el recuento en una variable Twig, y luego renderizar en ambos lugares.

Muy bien, mañana empezaremos con la parte más grande y atrevida de toda esta serie: construir un sistema modal reutilizable que sea absolutamente genial. ¡Estoy entusiasmado!
