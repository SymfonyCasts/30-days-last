# ¡Popover!

En el menú del día 11 está nuestra primera característica grande, bonita y totalmente funcional: un popover. Pero, ¡un popover precioso, reutilizable y de carga lenta!

Ya existen controladores Stimulus de código abierto para resolver un montón de problemas diferentes. Y una de las mejores fuentes de ellos es Stimulus Components: una rica colección de controladores. Vamos a trabajar con uno llamado popover.

Por si no lo sabes, un popover es un simpático recuadro que aparece para saludarte cuando pasas el ratón por encima de un elemento. Es como un tooltip, salvo que suelen ser más grandes y puedes pasar el ratón por encima del propio cuadro.

## Instalación y configuración de stimulus-popover

Se trata de una biblioteca JavaScript pura. Pero no vamos a instalarla con`yarn` o `npm`. En lugar de eso, ya sabes, ejecuta:

```terminal
php bin/console importmap:require stimulus-popover
```

Como se trata de un paquete JavaScript puro, no hay receta Flex. El único cambio que se hizo fue en `importmap.php`:

[[[ code('24728c5a49') ]]]

Así que tenemos acceso al código, pero esta vez, tenemos que registrar el controlador manualmente.

¡No pasa nada! Copia estas líneas de la documentación... y luego abre `assets/bootstrap.js`. Pega esto encima. No necesitamos `Application.start()`... y mueve`application.register()` después... y llámalo `app`:

[[[ code('5b992f1a4c') ]]]

¡Felicidades! Tenemos un nuevo y reluciente controlador llamado `popover`.

## Utilizar el controlador

El objetivo es pasar el ratón por encima de este planeta y mostrar una ventana emergente con información adicional. Para conseguir que funcione, dirígete a la documentación. Hay documentación de Rails para cosas del lado del servidor.... que no necesitamos. Allá vamos. Así que necesitamos un elemento con`data-controller"popover"` y, dentro, un enlace que, en `mouseenter` llame a un método `show`y, en `mouseleave` llame a `hide`. Debajo, este es el contenido que se mostrará en el popover.

Copia esto entonces, dirígete a `homepage.html.twig`, y busca planetas. Aquí está el`td` y aquí está la imagen del planeta. Pega... y luego moveré mi `img` dentro.

¡Estupendo! Luego tenemos que poner este `data-action` en algún sitio. Es tentador ponerlo en el propio `img`. Pero, la biblioteca añade el contenido del popover dentro del elemento que lo activa... y como no puedes poner contenido dentro de un `img`, es un no-go. En lugar de eso, ponlo directamente en el `div` padre:

[[[ code('4ba334e43b') ]]]

Así que en `mouseenter` de este div, muestra el popover, en `mouseleave` de este div, oculta el popover.

Esto debería funcionar Puede parecer un poco alocado al principio... pero bueno, zambullámonos y veamos qué pasa. Y... ¡funciona! Es raro y nervioso, ¡pero funcional!

## Estilizar el Popover

Es hora de darle un mejor aspecto. Seleccionaré todo este `div` y lo pegaré:

[[[ code('43b6fa00f1') ]]]

No hice nada del otro mundo: añadí una clase `relative` en el exterior `div`... y aquí abajo, hice que el popover tuviera una posición absoluta e imprimí algo de información sobre el planeta.

Ahora... ¡mira eso! ¿Sabes qué estaría bien? ¡Una flechita! Podemos añadir una en CSS puro con un pseudoelemento `:after` en el objetivo del popover `card`. Esta es una estrategia CSS estándar para añadir flechas, y puedes encontrarla en la web, o utilizar AI para ayudarte a generarla.

Abre `app.css` y pegaré algo de código. También puedes hacer esto con clases Tailwind:

[[[ code('850c2e5208') ]]]

¡Ve a comprobarlo! Me encanta

## Carga perezosa con un marco turbo

Llegados a este punto, el popover funciona muy bien y tiene un aspecto estupendo. ¿Te apuntas a un reto? En lugar de cargar todo este código al cargar la página, quiero cargarlo mediante Ajax sólo cuando el usuario pase el ratón por encima. La biblioteca de ventanas emergentes tiene una forma de hacerlo. Pero como reto extra, quiero hacerlo con marcos normales `ol`' Turbo. Porque, ¡los marcos son realmente buenos cargando cosas mediante AJAX! Además, veremos un par de características extra de los frames de las que aún no hemos hablado.

Para empezar, necesitamos una ruta que muestre esta información del planeta. En la plantilla de la página de inicio, copia este código y luego elimínalo:

[[[ code('c566b6b24e') ]]]

En `templates/planet/`, crea un nuevo archivo llamado `_card.html.twig`, y pégalo:

[[[ code('d0671b7614') ]]]

A continuación, crea una ruta para esto. En `src/Controller/PlanetController.php`, en cualquier lugar, pegaré una ruta y un controlador:

[[[ code('dc76d84b12') ]]]

Nada especial: consulta el `Planet` y pásalo a la plantilla. En esa plantilla, ajusta las variables. En lugar de `voyage.planet`, utiliza `planet`en cada lugar:

[[[ code('5324099877') ]]]

Ahora tenemos una ruta AJAX que devuelve el contenido. Aquí está la parte mágica. En `homepage.html.twig`, queremos cargar ese contenido justo aquí. Hazlo añadiendo un `turbo-frame` con `id` ajustado a `planet-card-` y luego a `{{ voyage.planet.id }}`para que sea único en la página:

[[[ code('bb3e0015d8') ]]]

Añade este mismo marco en `_card.html.twig`... con la etiqueta de cierre:

[[[ code('1395368049') ]]]

Normalmente, un `<turbo-frame>` será una parte de una página entera. Pero está perfectamente bien hacer una ruta que sólo devuelva un único fotograma.

Volviendo a `homepage.html.twig`, tenemos la configuración básica. El truco es que... no estamos esperando a que alguien haga clic en un enlace dentro de este marco que luego cargará la página de la tarjeta. No, queremos que se cargue inmediatamente.

Para ello, añade un atributo `src` establecido en `{{ path() }}`... y... eso es casi correcto. La ruta es `app_planet_show_card`:

[[[ code('640ab97a21') ]]]

¡Listo! Cuando aparezca un marco turbo que ya tenga un atributo `src`, hará la llamada AJAX para cargar ese contenido inmediatamente.

Pruébalo. Actualiza y... falta contenido. He estropeado algo. No pasa nada, ¡podemos depurar! La llamada ha fallado con un error 500. Aquí es donde la barra de herramientas de depuración web resulta útil. No podemos ver el error fácilmente... pero a través del elemento de la barra de herramientas Ajax, podemos hacer clic para ver la gran y bonita página de excepción. Ah:

> La variable `voyage` no existe.

Porque tengo que ajustarla a `planet.id`:

[[[ code('3d01652190') ]]]

De acuerdo. Y ahora... ¡lo tengo! Hay un momento en que el popover está vacío... pero lo arreglaremos pronto.

## Carga perezosa de Turbo Frames

Por pura casualidad, nuestro Turbo Frame se está cargando perezosamente. Lo que quiero decir es: cuando tenemos un `<turbo-frame>` con un atributo `src`, la llamada AJAX se realiza inmediatamente. Teniendo esto en cuenta, ¿no deberíamos ver 30 llamadas Ajax sucediendo a la vez? Sí... ¡pero no es así! Sólo ocurre cuando pasamos el ratón por encima. ¿Por qué?

Inspecciona ese elemento. Ah. Es por accidente, gracias al elemento `template`. El elemento`template` es especial en tu navegador: cualquier cosa dentro de él se comporta... como si no estuviera en la página en absoluto: casi como si fuera una cadena en lugar de un elemento. Así, cuando cargamos por primera vez, el `<turbo-frame>` técnicamente no forma parte de la página. Pero en el momento en que pasamos el ratón por encima, lo copia en la página, el `turbo-frame` cobra vida y se realiza la llamada Ajax. ¡Genial!

Pero habrá otras situaciones en las que queramos que un `turbo-frame` cargue su contenido sólo cuando ese marco se haga visible. Para demostrarlo, cambia temporalmente el `template` por un `div`:

[[[ code('1fbe74d0fb') ]]]

Esto va a tener un aspecto horrible... porque todas las cartas serán visibles a la vez. ¡Sí! ¡Están todas en la página y ha hecho 30 llamadas Ajax inmediatamente! ¡Vaya! Para indicar a estos marcos que no se carguen hasta que sean visibles en la página, añade `loading="lazy"`:

[[[ code('682a1bbd69') ]]]

Actualizar ahora. 3 llamadas ajax... ¡porque sólo 3 marcos son visibles! Los demás elementos están todos en la página... pero por debajo del scroll. Observa este número mientras me desplazo. ¿Lo ves? A medida que se hacen visibles, cada uno realiza su llamada AJAX. Qué guay.

Vuelve a cambiar el elemento a `template` para que las cosas vuelvan a funcionar bien:

[[[ code('6c1062fc05') ]]]

## Añadir contenido de carga

Vale, estoy muy contento. Pero quiero perfeccionar esta nueva función. Una cosa que no me gusta es que esté vacío antes de que termine la llamada Ajax. Me gustaría añadir contenido de carga.

Esto es fácil: cuando tienes un `turbo-frame` con un atributo `src`, cualquier contenido que haya dentro se mostrará por defecto mientras se carga. Voy a pegar un `div` con un SVG:

[[[ code('bf9dfd613f') ]]]

Este SVG procede de Tabler Icons. Es una gran fuente para encontrar un icono que puedes copiar en tu proyecto. Lo he unido a una clase `animate-spin` de Tailwind.

Vamos a comprobarlo. ¡Rápido, giratorio y encantador!

## Recordando la llamada Ajax

¿Tenemos tiempo para una cosa más? Cuando pasamos el ratón por encima del elemento, hace la llamada AJAX. Y... la repite cada vez que pasamos el ratón por encima. No recuerda el contenido de la llamada AJAX.

Eso se debe a cómo funciona el controlador de ventanas emergentes... y si hubiera sido menos terco y hubiera utilizado su forma de cargar el contenido mediante Ajax, no sería un problema. De todas formas, cada vez que pasamos el ratón, se crea el `turbo-frame`, se destruye, se crea uno nuevo, se destruye, etc.

Entonces: ¿cómo podemos hacer que el controlador recuerde el contenido? ¡No tengo ni idea! Pero vamos a mirar dentro del código. En `assets/vendor/stimulus-popover/`, abre este archivo. El contenido está minificado... pero un rápido `Cmd`+`L` para reformatear el código lo arregla ¿A que mola? Ahora podemos leer este archivo de proveedor, e incluso añadir código de depuración temporal si lo necesitamos. Y... Creo que veo una forma de hacer que esto funcione.

Al igual que con los controladores Symfony, podemos anular los controladores Stimulus. Dentro del directorio `controllers/`, crearemos nuestro propio `popover_controller.js`. Luego pegaré algo de código:

[[[ code('c75a899e5f') ]]]

Normalmente importamos `Controller` de Stimulus y lo extendemos. Pero en este caso, estoy importando directamente el controlador popover y extendiéndolo. A continuación, anulamos el método `show` y el método `hide` para activar una clase `hidden` en lugar de destruir completamente el elemento.

Y ahora que tenemos nuestro propio controlador llamado `popover`, en `bootstrap.js`, no necesitamos registrar el de los componentes Stimulus. El controlador de `popover` será nuestro controlador... entonces aprovechamos el controlador de componentes Stimulus mediante herencia.

¡Momento de la verdad! Se carga una vez... ¡y luego recuerda su contenido! 

No sólo hemos creado el popover perfecto, sino que ahora podemos repetirlo fácilmente en otras partes de nuestro sitio. Si te estás preguntando si podríamos reutilizar parte del marcado del popover... permanece atento al Día 23, cuando hablemos de los Componentes Twig.

Esto es todo por hoy Tómate un merecido descanso, porque mañana escribiremos un pequeño, pero poderoso, controlador de Stimulus llamado auto-enviar.
