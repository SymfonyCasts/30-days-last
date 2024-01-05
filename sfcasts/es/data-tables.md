# Tablas de datos con Turbo Frames

Nuestra configuración similar a las tablas de datos funciona. Y es casi genial. Lo que no me gusta es cómo salta de un lado a otro. Cada vez que hacemos clic en un enlace, salta de nuevo a la parte superior de la página. Buf.

Eso es porque Turbo está recargando la página completa. Y cuando lo hace, se desplaza a la parte superior... ¡porque eso es lo que solemos querer! Pero esta vez no. Quiero que nuestra tabla de datos funcione como una pequeña aplicación: donde el contenido cambia sin desplazarse.

## ¿Turbo 8 Morphing? 

Hay dos formas estupendas de resolver esto. En Turbo 8 -que aún no se ha lanzado, es la Beta 1 en el momento de grabar esto- hay una nueva función llamada refresco de página que aprovecha el morphing. Sin obsesionarme demasiado -y quiero hacerlo-, al navegar a la misma página -como hacen nuestro formulario de búsqueda, la ordenación por columnas y los enlaces de paginación- podemos decirle a Turbo que sólo actualice el contenido de la página que ha cambiado... y que conserve la posición de desplazamiento. Así que estate atento a esto.

## Añadir un marco Turbo

La segunda forma -que hoy funciona fantásticamente- es rodear toda esta tabla con un `<turbo-frame>`. En `homepage.html.twig`, busca el `table`. Aquí está: este `div` representa la tabla. Encima, añade `<turbo-frame id="voyage-list">`. Pon sangría a este `div`... y también a estos enlaces de paginación: queremos que estén dentro del marco Turbo para que, cuando hagamos clic en ellos, hagan avanzar el marco y se actualicen:

[[[ code('88574e7472') ]]]

Probemos esto. Borra esa búsqueda. Y oh... precioso. ¡Fíjate! Todo se mueve dentro del marco. Prueba la paginación. ¡Eso también! Todos nuestros enlaces apuntan a la página de inicio... y la página de inicio, por supuesto, tiene este marco.

Pero recuerda: ahora que esta tabla vive dentro de un marco Turbo, si tenemos algún enlace dentro, dejará de funcionar. De nuevo, para solucionarlo, en cada enlace, añade`data-turbo-frame="_top"`. O para ser más conservador, ve hasta el nuevo`<turbo-frame>` y añade `target="_top"`. Si haces eso, también tendrás que encontrar los enlaces de ordenación y paginación que deben navegar por el marco y añadir`data-turbo-frame="voyage-list"`.

Pero los eliminaré... porque no tenemos ningún enlace en el cuadro.

## Orientación de la búsqueda en el formulario

En este punto, ¡los enlaces de paginación y ordenación funcionan perfectamente! Pero... ¿la búsqueda? La búsqueda sigue siendo una recarga completa de la página. ¡Eso tiene sentido! No la puse dentro del marco. ¿Por qué? Porque, si lo hubiéramos hecho, al escribir y recargarse el marco, también se habría recargado el cuadro de búsqueda... lo que seguiría restableciendo la posición de mi cursor. Así que no queremos que el formulario se recargue.

¿Podemos... mantenerlo fuera del marco pero dirigirlo al marco cuando se envíe el formulario? Sí, podemos En el elemento `form` que se envía, añade`data-turbo-frame="voyage-list"`:

[[[ code('c861024e8a') ]]]

¿No es genial? Ahora cuando actualicemos: mira. ¡Es perfecto! La tabla se carga, pero mantengo el foco de entrada. Esto es precioso.

## Añadir una pantalla de carga

¡Y ahora tenemos tiempo para hacer las cosas más extravagantes! ¿Qué tal un indicador de carga en la tabla mientras navega? Para hacerlo evidente, ve a nuestro controlador y añade un `sleep()` durante un segundo:

[[[ code('2877140665') ]]]

Ahora... es lento... y cuando hacemos clic o buscamos, ni siquiera recibimos ninguna señal de que el sitio esté haciendo algo.

¿Cómo podemos añadir un indicador de carga? Este es un punto en el que Turbo nos cubre las espaldas. Así que aquí está el elemento `<turbo-frame>`. Observa los atributos del final cuando navego. ¿Lo has visto? Turbo añadió un atributo `aria-busy="true"` mientras se cargaba. Está ahí por accesibilidad, ¡pero también es algo que podemos aprovechar dentro de Tailwind!

Sobre ese elemento `<turbo-frame>`, aquí está, digamos `class=""` con`aria-busy:opacity-50`.

Esta sintaxis especial dice que, si este elemento tiene un atributo `aria-busy`, aplica el `opacity-50`. Añade otro `aria-busy:` con `blur-sm` para difuminar el fondo. Y para ganar puntos extra, incluye `transition-all` para que la transición entre opacidad y desenfoque no se produzca de forma brusca:

[[[ code('f15c734c69') ]]]

***TIP
Para un efecto aún más bonito, también puedes cambiar la opacidad y el desenfoque sólo si la carga tarda más de, por ejemplo, 700 ms. Hazlo añadiendo una clase `aria-busy:delay-700`.
***

Refréscala y observa. ¡Qué bonito! Y todo ocurre gracias a 3 clases CSS. Y, aunque me encanta verlo, en `MainController`, quita la suspensión.

## Avanzar el marco

¿Se ha cumplido esta misión? Casi. Hay un problema gigantesco y horrible... con una solución fácil. Cuando buscamos, ordenamos o paginamos, la URL no cambia. Ése es el comportamiento por defecto de los marcos Turbo: cuando navegan, no actualizan la URL. Sin embargo, podemos decirle a Turbo que queremos esto. En el marco Turbo, añade `data-turbo-action="advance"`:

[[[ code('5bb7f7f1df') ]]]

Avanzar significa que actualizará la URL y avanzará el historial del navegador, de modo que si pulsamos el botón "Atrás", irá a la URL anterior. También puedes utilizar `replace` para cambiar la URL, pero sin añadirla al historial.

Observa: esta vez cuando busquemos... ¡la URL se actualiza! Y cuando navegamos a la página dos o tres... se actualiza... y podemos pulsar atrás, atrás, y adelante, adelante.

Ahora tenemos una configuración de tablas de datos realmente fantástica... totalmente escrita sin ningún JavaScript personalizado dentro de Twig y Symfony. Qué momento para estar vivo.

El último pequeño problema son los "30 resultados". Cuando buscamos, esto nunca cambia: se queda en el número que había cuando se cargó la página original. Esto se debe a que vive fuera del marco Turbo. La solución más fácil sería moverlo al marco... ¡pero no lo quiero ahí! Visualmente, ¡lo quiero aquí arriba!

Vamos a dejarlo así por ahora. Pero lo arreglaremos dentro de unos días con Turbo Streams.

Mañana, ¡vamos a sumergirnos en una nueva función del navegador! Se llama Transiciones de vista, y nos permitirá aplicar transiciones visuales a cualquier navegación.