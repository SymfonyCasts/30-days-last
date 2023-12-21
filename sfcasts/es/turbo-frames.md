# Marcos Turbo

En este, el día 10: vamos a hablar de un concepto antiguo: los marcos. Si eres lo suficientemente viejo en Internet, como yo, puede que recuerdes los iframes. Eran esas cosas raras en las que podías separar tu sitio en diferentes partes. Y cuando hacías clic en un enlace dentro de un marco, la navegación se quedaba dentro de ese marco. Era como tener páginas web separadas que unías en una sola.

La segunda parte de Turbo es Turbo Frames... que es una forma no extraña y moderna de dividir tu página en partes... algo parecido a iframes.

¿Ves esta barra lateral izquierda? Cuando hacemos clic en un planeta, nos lleva a la página de presentación de ese planeta. Genial. Pero no lo suficiente En lugar de eso, cuando haga clic en un planeta, quiero que ese contenido se cargue justo dentro de esta barra lateral sin cambiar de página.

## Añade el botón `<turbo-frame>`

Para ello, busca la barra lateral: está en `templates/main/homepage.html.twig`... cerca de la parte superior. Este parcial renderiza esa lista de planetas. Para convertirlo en un marco, busca el elemento que lo rodea y cámbialo a `<turbo-frame>`. Y la única regla de los marcos es que cada uno debe tener un atributo `id`. Debe ser algo único que describa lo que contiene. ¿Qué te parece `planet-info`:

[[[ code('05db661c1b') ]]]

Vale: ¿qué hace eso? En principio, nada. Un `<turbo-frame>` no es más que un elemento HTML como un `div`, y por tanto se renderiza normalmente. Aunque, a efectos de estilo, `turbo-frame`es un elemento en línea por defecto.

Sin embargo, cuando hacemos clic en el enlace... ¡está roto! Dice "Falta contenido". Y en la consola

> La respuesta no contenía el contenido esperado `<turbo-frame id="planet-info">`.

Cuando hacemos clic en este enlace, realiza una petición Ajax a la página de presentación... como haría normalmente con Turbo. Pero como el enlace está dentro de un `<turbo-frame>`, coge el HTML y busca un `<turbo-frame>` que coincida con `id="planet-info"`. Si lo encuentra, coge el contenido que hay dentro y pone sólo eso en el `<turbo-frame>`de aquí.

## Añadir la coincidencia `<turbo-frame>`

Esto significa que cada enlace dentro de `<turbo-frame>` -sea cual sea la página a la que vaya- debe tener un `<turbo-frame>` coincidente.

Copia este `<turbo-frame id="planet-info>` y luego abre `planet/show.html.twig`. Ponlo alrededor del contenido que queremos cargar en la barra lateral. En realidad no quiero el `h1`... así que ponlo alrededor de esta tabla. Añade el `</turbo-frame>`de cierre en la parte inferior:

[[[ code('abb6ae6b38') ]]]

¡Refrescar! Y haz clic. ¿No es genial? Hace una petición AJAX a esta página, coge sólo el contenido de `<turbo-frame>` y lo pone aquí.

¿Sabes qué más sería genial? ¡Un enlace "atrás"! Para añadirlo, sigue dentro de `<turbo-frame>`, añade un `<div class="mt-2">` y luego un `a`,`href` y `{{ path() }}`. Enlace a la página de inicio:

[[[ code('d6cbba9f7c') ]]]

¡Pruébalo! Ni siquiera necesitamos actualizar. ¡Contempla! ¡Un enlace de vuelta! Uy, que sea más bien una flecha. Cuando hacemos clic en él... ¡vuelve atrás! Eso hizo una petición AJAX a la página de inicio y buscó un`<turbo-frame id="planet-info">` que coincidiera. ¿Y adivina qué contiene? Esta lista de planetas.

¡Estamos en racha! Antes de terminar hoy, añade un enlace más: un enlace de edición. La ruta es `app_planet_edit`... con `id` ajustado a `planet.id`:

[[[ code('418e66c400') ]]]

Genial! esta vez, si hacemos clic en un planeta... y luego editamos... ¡no funciona! Y seguro que adivinas por qué. Se hizo una petición AJAX a la página de edición ...., pero no hay ninguna `<turbo-frame>` que coincida en esa página. Y así, obtenemos este error.

Pero... No quiero añadir un `<turbo-frame>` a la página de edición. De todas formas, el formulario no cabría en la barra lateral. No, cuando haga clic en este enlace, quiero que dé lugar a una navegación Turbo de "página completa".

En cuanto añadas un `<turbo-frame>`, tienes que hacer un seguimiento de los enlaces que tienes dentro de él y asegurarte de que cada uno vaya a una página que tenga un `<turbo-frame>`.... correspondiente, o bien orientar el enlace o formulario para que haga una visita completa.

## Dirigir los enlaces a la página completa

¿Cómo lo haces? Busca el enlace y añade `data-turbo-frame` -es un error tipográfico, Ryan- a `_top`:

[[[ code('eb0db440c3') ]]]

Ahora, sin actualizar, pulsa editar. Se sigue rompiendo. Me he equivocado. Es `data-turbo-frame="_top"`. Ya está.

Ahora pulsa editar. ¡Navegación por toda la página! Sigue funcionando con Ajax, pero cambia toda la página.

La otra forma de dirigir enlaces o formularios a la página completa es en el propio `<turbo-frame>`. Podrías decir:

> ¡Oye! Quiero que todos los enlaces de este `<turbo-frame>` sean de navegación a página completa por
> por defecto.

Para ello, establece `target` en `_top`. Luego, si tienes un enlace específico que quieres cargar en este marco, añade `data-turbo-frame` igual y luego el id:`planet-info`.

Ambos enfoques están bien: tú decides. Pero añadir `target="_top"` a cada marco es un poco más seguro.

## Ocultar contenido que no está en un marco

Así que esto funciona ¡súper bien! Excepto por el hecho de que si el usuario llega alguna vez a la página de presentación del planeta, tenemos un conjunto extra de enlaces. En realidad, sólo queremos mostrarlos cuando estemos dentro del `<turbo-frame>`. ¿Cómo podemos hacerlo?

Cuando Turbo envía una petición Ajax a `<turbo-frame>`, añade una cabecera de petición que indica a tu aplicación que se trata de una petición Turbo Frame. Puedes usar esto dentro de Symfony para hacer diferentes cosas condicionalmente... como renderizar condicionalmente estos enlaces.

Vamos a hacer eso una vez más adelante en el tutorial. Sin embargo, intento minimizarlo: añade complejidad. Otra opción es ocultar cosas adicionales con CSS Por ejemplo, podríamos añadir una clase a la barra lateral... y sólo mostrar estos enlaces si estamos dentro de esa clase.

Sin embargo, Tailwind en realidad no funciona así. En Tailwind, no puedes cambiar el estilo condicionalmente en función de tu padre. Al menos no fuera de la caja. Pero podemos hacerlo con un truco llamado variante.

Lo primero que hay que observar es que un `<turbo-frame>`, por defecto, tiene este aspecto: exactamente igual al que tenemos en nuestra plantilla. Pero en cuanto hacemos clic en un enlace, éste tiene un atributo `src`. Podemos aprovecharnos de ello añadiendo una forma dentro de Tailwind para dar estilo a los elementos condicionalmente en función de si están dentro de un `<turbo-frame>`que tenga un atributo `src`. Porque, tendrá un `src` por aquí... pero no tendrá un `src` dentro de este `<turbo-frame>`... porque nunca navega. De hecho, sería una buena idea añadir un `target="_top'` a este marco, ya que no necesitamos una navegación de marco extravagante en esta página.

De todos modos, las variantes de Tailwind son un poco más avanzadas, pero bastante sencillas. Importa este módulo `plugin`, y luego baja a `plugins`. Voy a pegar algo de código:

[[[ code('229fdf30a6') ]]]

Esto añade una variante llamada `turbo-frame`: verás cómo la utilizamos en un segundo. Básicamente se aplica a un elemento que está dentro de un `<turbo-frame>` que tiene un atributo `src`.

Como hemos llamado a esto `turbo-frame`, cópialo. Ahora, en `show.html.twig`, añade una clase `hidden` para ocultar este `div` por defecto.

Cuando actualizamos, desaparece. Pero entonces, si coincidimos con nuestra variante `turbo-frame`, cambia para mostrar `block`:

[[[ code('ce77a64553') ]]]

Compruébalo. Cuando actualizamos, esos enlaces siguen ocultos. Pero aquí... ¡los tenemos! Como estamos dentro de un `turbo-frame` con un atributo `src`, nuestra variante se activa y la visualización `block` toma el control.

Los Turbo Frames añaden cierta complejidad, pero sólo hemos empezado a arañar la superficie de lo que hacen posible.

Mañana, cuando pase el ratón por encima de cada planeta, quiero añadir una ventana emergente con más información sobre los planetas. Para ello, vamos a instalar otro controlador Stimulus de terceros.
