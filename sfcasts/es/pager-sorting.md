# Paginación y ordenación por columnas

¡Bienvenido al Día 13! Hoy vamos a dejar de lado Stimulus y Turbo y trabajaremos únicamente con Symfony y Twig. Nuestro objetivo es añadir paginación y ordenación por columnas a esta lista.

## Añadir paginación

Me gusta añadir paginación con Pagerfanta. Me encanta esta librería, aunque me pierdo un poco en su documentación. Pero oye: es de código abierto, si no estás contento, ¡ve y arréglalo!

Para utilizar Pagerfanta, instalaremos tres bibliotecas:

```terminal
composer require babdev/pagerfanta-bundle pagerfanta/doctrine-orm-adapter pagerfanta/twig
```

¡Genial! Primero hagamos funcionar la parte PHP. Abre `src/Controller/MainController.php`. La página actual se almacenará en la URL como `?page=1` o `?page=2`, así que necesitamos leer ese parámetro de consulta `page`. Lo haremos con un nuevo atributo`#[MapQueryParameter]`. Y en realidad, antes... Estaba haciendo demasiado trabajo. Si tu parámetro de consulta coincide con el nombre de tu argumento, no necesitas especificarlo ahí. Así que lo suprimiré en esos dos. Es diferente para `searchPlanet`: un parámetro que utilizaremos más adelante.

De todos modos, esto leerá el `?page=` y lo pondremos por defecto a 1. Ah, y el orden de estos no importa:

[[[ code('b7502623e3') ]]]

A continuación, copia la línea `$voyageRepository->findBySearch()` y sustitúyela por un objeto Pager: `$pager` es igual a `Pagerfanta::createForCurrentPageWithMaxPerPage()`:

[[[ code('378b27ea11') ]]]

El primer argumento es un adaptador: nuevo `QueryAdapter` luego pega el código de antes. Pero, eso no es del todo correcto: este método devuelve una matriz de viajes:

[[[ code('094033a391') ]]]

pero ahora necesitamos un `QueryBuilder`. Afortunadamente, ya he configurado las cosas para que podamos obtener este mismo resultado, pero como un `QueryBuilder` a través de: `findBySearchQueryBuilder`:

[[[ code('5ab552db61') ]]]

Pega ese nombre de método.

El siguiente argumento es la página actual - `$page` - y luego el máximo por página. ¿Qué te parece 10?

[[[ code('13981a44c5') ]]]

Pasa `$pager` a la plantilla como la variable `voyages`:

[[[ code('ff58dc06a4') ]]]

Eso... debería funcionar porque podemos hacer un bucle sobre `$pager` para obtener los viajes.

## Renderizar los enlaces de paginación

A continuación, en `homepage.html.twig`, ¡necesitamos enlaces de paginación! En la parte inferior, ya tengo un lugar para ello con los enlaces anterior y siguiente codificados:

[[[ code('17ca7d9f77') ]]]

La forma en que se supone que debes mostrar los enlaces Pagerfanta es diciendo `{{ pagerfanta() }}`y pasando `voyages`:

[[[ code('cd89cedecb') ]]]

Cuando probamos esto -permíteme aclarar mi búsqueda- la paginación tiene un aspecto horrible... ¡pero funciona! A medida que hacemos clic, los resultados van cambiando.

Entonces... ¿cómo podemos cambiar estos enlaces de paginación de "bla" a "ah"? Hay una plantilla Tailwind incorporada que puedes decirle a Pagerfanta que utilice. Eso implica crear un archivo `babdev_pagerfanta.yaml` y un poco de configuración. No he utilizado esto antes, así que ¡hazme saber cómo va!

```yaml
babdev_pagerfanta:
    # The default Pagerfanta view to use in your application
    default_view: twig

    # The default Twig template to use when using the Twig Pagerfanta view
    default_twig_template: '@BabDevPagerfanta/tailwind.html.twig'
```

Porque... Voy a ser testaruda. Sólo quiero tener los botones anterior y siguiente... y quiero que tengan el mismo estilo que estos. Así que ¡vamos al lío!

Lo primero que tenemos que hacer es mostrar estos enlaces condicionalmente, sólo si hay una página anterior. Para ello, di if `voyages.hasPreviousPage`, then render. Y, si tenemos una página siguiente, renderiza esa:

[[[ code('0071611bf0') ]]]

Para las URL, utiliza un ayudante llamado `pagerfanta_page_url()`. Pásale el paginador,`voyages`, y luego la página a la que queremos ir: `voyages.previousPage`. Cópialo y repítelo a continuación con `voyages.nextPage`:

[[[ code('760bfc9e0c') ]]]

¡Estupendo! Vamos a probarlo. Refrescar. ¡Me encanta! Falta la página anterior, hacemos clic en siguiente y ya está. Vuelve a hacer clic en siguiente. La página 3 es la última. ¡Ya está!

Para obtener un crédito extra, imprimamos incluso la página actual. Añade un div... y luego imprime`voyages.currentPage`, una barra y `voyages.nbPages`:

[[[ code('2be4dae34c') ]]]

¡Buen trabajo, IA!

Y... ya está. Página 1 de 3. Página 2 de 3.

## Ordenación por columnas

¿Qué pasa con la ordenación por columnas? Quiero poder hacer clic en cada columna para ordenar por ella. Para ello, necesitamos dos nuevos parámetros de consulta. Un nombre de columna `sort` y`sortDirection`. ¡Vuelve a PHP! Añade `#[MapQueryParameter]` a un argumento `string` llamado `$sort`. Pon por defecto `leaveAt`. Ése es el nombre de la propiedad de esta columna de salida. A continuación, vuelve a hacer `#[MapQueryParameter]` para añadir una cadena`$sortDirection` que por defecto sea ascendente:

[[[ code('92f6506b1a') ]]]

Dentro del método, pegaré 2 líneas aburridas que validan que `sort` es una columna real:

[[[ code('26f16f64e6') ]]]

Probablemente podríamos hacer lo mismo para `$sortDirection`, pero me lo saltaré y pasaré a`findBySearchQueryBuilder()`. Esto ya está configurado para esperar los argumentos de ordenación. Así que pasa `$sort` y `$sortDirection`... ¡y debería estar contento!

[[[ code('254194c30f') ]]]

Por último, vamos a necesitar esta información en la plantilla para ayudar a mostrar los enlaces de ordenación. Pasa `sort` ajustado a `$sort` y `sortDirection` ajustado a `$sortDirection`:

[[[ code('b366b6110c') ]]]

## Añadir los enlaces de ordenación de columnas

La parte más tediosa es transformar cada `th` en el enlace de ordenación adecuado. Añade una etiqueta `a` y divídela en varias líneas. Establece el `href` en esta página -la página principal- con un `sort` adicional establecido en `purpose`: el nombre de esta columna. Para `sortDirection`, esto es más complejo: si `sort` es igual a `purpose`y `sortDirection` es `asc`, entonces queremos `desc`. En caso contrario, utiliza `asc`.

Por último, además de los parámetros de consulta `sort` y `sortDirection`, necesitamos mantener cualquier parámetro de consulta existente que pueda estar presente, como la consulta de búsqueda. Y hay una forma genial de hacerlo: `...` y luego `app.request.query.all`:

[[[ code('ed56b194a3') ]]]

¡Listo! Ah, pero después de la palabra Propósito, añadamos una bonita flecha hacia abajo o hacia arriba. Para ayudarte, pegaré una macro Twig. No suelo utilizar macros... pero esto nos ayudará a averiguar la dirección, y luego imprimir el SVG correcto: una flecha hacia abajo, una flecha hacia arriba, o una flecha hacia arriba y otra hacia abajo:

[[[ code('98fd2431b1') ]]]

Aquí abajo... utiliza esto con `{{ _self.sortArrow() }}` pasando por `'purpose'`,`sort` y `sortDirection`:

[[[ code('0b2f0959dc') ]]]

¡Uf! Repitamos todo esto para la columna de salida. Pega, cambia `purpose`por `leaveAt`, el texto por `Departing`... luego utiliza `leaveAt` en los otros dos puntos:

[[[ code('a9c3ff4b5c') ]]]

Así pues, todo un código bastante aburrido, aunque ha costado un poco de trabajo configurarlo. ¿Podríamos tener algunas herramientas en el mundo Symfony para que todo esto fuera más fácil de construir? Probablemente, sería genial que alguien trabajara en ello. 

¡El momento de la verdad! Actualiza. Tiene buena pinta. ¡Y funciona de maravilla! Podemos ordenar por cada columna... podemos paginar. El filtrado mantiene nuestra página... y mantiene el parámetro de búsqueda. ¡Es todo lo que quiero! ¡Y todo ocurre mediante Ajax! ¡La vida es buena!

¿El único contratiempo ahora? Ese incómodo desplazamiento cada vez que hacemos algo. Quiero que esto parezca una aplicación independiente que no salta de un lado a otro. Mañana: puliremos esto gracias a Turbo Frames.
