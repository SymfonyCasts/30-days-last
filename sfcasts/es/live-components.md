# Componentes en vivo

¡Feliz día 27 de LAST Stack! Hemos conseguido mucho durante los primeros 26 días con sólo tres letras de LAST Stack: Mapeador de Activos, Stimulus y Turbo. Hoy desciframos el código de la L de LAST Stack: Componentes en vivo. Los componentes en vivo nos permiten tomar un componente Twig... y volver a renderizarlo mediante Ajax cuando el usuario interactúa con él.

Nuestro objetivo es esta búsqueda global. Cuando hago clic, ¡no pasa nada! Lo que quiero hacer es abrir un modal con un cuadro de búsqueda que, a medida que escribimos, cargue una búsqueda en vivo.

## Abrir el modal de búsqueda

Comienza dentro de `templates/base.html.twig`. ¡Busca la búsqueda! Perfecto: esta es la entrada de búsqueda falsa que acabamos de ver. Añade un `<twig:Modal>` con `:closeButton="true"`, luego un `<twig:block>` con `name="trigger"`. Para que esto abra el modal, necesitamos `data-action="modal#open"`:

[[[ code('72856ac5a5') ]]]

¡Genial! Si actualizamos, no cambia nada: la única parte visible del modal es el activador. Para el contenido del modal, después del bloque Twig, pegaré un div:

[[[ code('2a7ef09201') ]]]

Aquí no hay nada especial: sólo una entrada de búsqueda real.

De vuelta al navegador, cuando hago clic... uh oh. ¡no pasa nada! La depuración se inicia siempre en la consola. No hay errores, pero cuando hago clic, mira: no hay ningún registro que diga que se está lanzando la acción. ¿Hay algo que no funciona y tal vez hayas visto mi error? Añadimos el `data-action` a un `div`. A diferencia de un `button`o un `form`, Stimulus no tiene un evento predeterminado para un `div`. Añade `click->`:

[[[ code('56d17b0986') ]]]

Y ahora... ¡ya está!

Ah, ¡y autoenfoca la entrada! Eso es.... ¡una característica de los diálogos! Funcionan como una minipágina dentro de una página: autoenfoca el primer elemento tabulable... o puedes utilizar el atributo normal `autofocus` para tener más control. Funciona como tú quieras.

## Modal: Controla el relleno

De todos modos, soy quisquilloso: esto tiene más relleno del que quiero. Pero ¡no pasa nada! Podemos hacer que nuestro componente Modal sea un poco más flexible. En `components/Modal.html.twig`, el relleno extra es este `p-5`. En la parte superior, añade un tercer `prop`: `padding='p-5'`. Cópialo. Y aquí abajo, renderiza `padding`:

[[[ code('25868ace78') ]]]

En `base.html.twig`, en el modal, añade `padding` igual a comillas vacías:

[[[ code('9d705f4063') ]]]

¡Vamos a comprobarlo! Y... mucho más ordenado.

## Crear el componente Twig

Para dar vida a los resultados, podríamos repetir la configuración de las tablas de datos de la página de inicio. Podríamos añadir un `<turbo-frame>` con los resultados justo aquí y hacer que la entrada se autoenvíe a ese marco.

Otra opción es construir esto con un componente en vivo. Pero antes de hablar de eso, organicemos primero el contenido del modal en un componente Twig.

En `templates/components/`, crea un nuevo archivo llamado `SearchSite.html.twig`. Añadiré un div con `{{ attributes }}`. Luego ve a robar todo el cuerpo del modal, y pégalo aquí:

[[[ code('ecf565684e') ]]]

En `base.html.twig`, es fácil, ¿verdad? `<twig:SearchSite />` y listo:

[[[ code('0e94612f0f') ]]]

En el navegador, obtenemos el mismo resultado.

## Obtención de datos con un componente Twig

La búsqueda del sitio va a ser realmente una búsqueda de viaje. Para obtener los resultados, tenemos dos opciones. En primer lugar, podríamos... obtener de algún modo los viajes que queremos mostrar dentro de `base.html.twig` y pasarlos a `SearchSite` como prop. Pero... obtener datos de nuestro diseño base es complicado... probablemente necesitaríamos una función Twig personalizada.

La segunda opción es aprovechar nuestro componente Twig Uno de sus superpoderes es la capacidad de obtener sus propios datos: ser autónomo.

Para ello, este componente Twig necesita ahora una clase PHP. En `src/Twig/Components/`, crea una nueva clase PHP llamada `SearchSite`. Lo único que necesita para ser reconocido como componente Twig es un atributo: `#[AsTwigComponent]`:

[[[ code('102fd407d6') ]]]

Esto es exactamente lo que vimos dentro de la clase `Button`. Hace unos días, mencioné rápidamente que las clases de componentes Twig son servicios, lo que significa que podemos autoconectar otros servicios como `VoyageRepository`, `$voyageRepository`:

[[[ code('f055aa7e33') ]]]

Para proporcionar los datos a la plantilla, crea un nuevo método llamado `voyages()`. Éste devolverá una matriz... que en realidad será una matriz de `Voyage[]`. Dentro de`return $this->voyageRepository->findBySearch()`. Es el mismo método que usamos en la página de inicio. Pasa `null`, un array vacío, y limita a 10 resultados:

[[[ code('ce79365f0b') ]]]

La consulta de búsqueda aún no es dinámica, pero ahora tenemos un método `voyages()` que podemos utilizar en la plantilla. Empezaré con un poco de estilo, luego es código Twig normal: `{% for voyage in this` - ese es nuestro objeto componente -`.voyages`. Añade `endfor`, y en el medio, pegaré eso:

[[[ code('912f03e122') ]]]

Nada especial: una etiqueta de anclaje, una etiqueta de imagen y algo de información.

Vamos a probarlo. ¡Abre! ¡Genial! Aunque, por supuesto, cuando escribimos, ¡nada se actualiza! Lamentable!

## Instalar y actualizar un LiveComponent

Aquí es donde los componentes en vivo resultan útiles. ¡Así que vamos a instalarlo!

```terminal
composer require "symfony/ux-live-component:^2.0"
```

Para actualizar nuestro componente Twig a un componente Live, sólo tenemos que hacer dos cosas. Primero, es `#[AsLiveComponent]`. Y segundo, utilizar `DefaultActionTrait`:

[[[ code('3e501d5fe5') ]]]

Es un detalle interno... pero necesario.

Hasta aquí, nada cambiará. Sigue siendo un componente Twig... y no hemos añadido ninguna superpotencia de componente en vivo.

## Añadir un accesorio escribible

Uno de los conceptos clave con un componente vivo es que puedes añadir una propiedad y permitir que el usuario cambie esa propiedad desde el frontend. Por ejemplo, crea un `public string $query` para representar la cadena de búsqueda:

[[[ code('e19ebad1d5') ]]]

A continuación, utilízala cuando llamemos al repositorio:

[[[ code('eea18c7ca1') ]]]

Para permitir que el usuario modifique esta propiedad, necesitamos darle un atributo:`#[LiveProp]` con `writeable: true`:

[[[ code('f61f86f139') ]]]

Por último, para vincular esta propiedad a la entrada -de modo que la propiedad `query` cambie a medida que el usuario escribe- añade `data-model="query"`:

[[[ code('c0aa812925') ]]]

¡Ya está! Comprueba el resultado. Empezamos con todo, pero cuando tecleamos... ¡filtra! Incluso tiene depuración incorporada.

Entre bastidores, hace una petición AJAX, rellena la propiedad `query` con esta cadena, vuelve a renderizar la plantilla Twig y la coloca justo aquí.

Y, mira, es sólo PHP, así que esto es fácil. Si no es `$this->query`, devuelve un array vacío:

[[[ code('66a0d28e44') ]]]

Y en `SearchSite.html.twig`, añade una declaración if alrededor de esto: `if this.voyages`
no está vacío, devuelve eso... con el `endif` al final:

[[[ code('8f5ef0938d') ]]]

Para los quisquillosos con los detalles, sí, con `this.voyages`, estamos llamando al método dos veces. Pero hay formas de evitarlo, y mi favorita se llama `#[ExposeInTemplate]`. No la mostraré, pero es un cambio rápido.

## Fijar el modal a la parte superior

Así que, ¡estoy contento! Pero, esto no es perfecto... y yo quiero eso. Una cosa que me molesta es la posición: parece baja cuando está vacía. Y al escribir, salta de un lado a otro. Es el posicionamiento nativo de `<dialog>`, que normalmente es estupendo, pero no cuando nuestro contenido cambia. Así que, en este caso, vamos a fijar la posición cerca de la parte superior.

En `Modal.html.twig`, añade una última pieza de flexibilidad a nuestro componente: una prop llamada `fixedTop = false`:

[[[ code('730d1c52e4') ]]]

Entonces, al final de las clases `dialog`, si `fixedTop`, renderiza `mt-14` para fijar el margen superior. Si no, no hagas nada:

[[[ code('7bef2b660c') ]]]

En `base.html.twig`, en el modal... es hora de dividirlo en varias líneas. Entonces pasa a `:fixedTop="true"`:

[[[ code('2e55e72236') ]]]

Y ahora, ah. Mucho más bonito y sin más saltos.

## Establecer la Búsqueda como Turbo Permanente

¿Y qué más? Es necesario pulsar arriba y abajo en el teclado para recorrer los resultados, aunque eso lo dejaré para otra ocasión. Pero fíjate en esto. Si busco, luego hago clic fuera y navego a otra página, como es lógico, al abrir el modal de búsqueda, está vacío. Sería genial que recordara la búsqueda.

Y podemos hacerlo con un truco de Turbo. En `base.html.twig`, en el modal, añade `data-turbo-permanent`:

[[[ code('627a9d9f00') ]]]

Eso le dice a Turbo que mantenga esto en la página cuando navegue. Cuando utilices esto, necesita un id.

Veamos cómo se siente esto. Abre la búsqueda, escribe algo, pulsa fuera, ve a la página principal y ábrela de nuevo. ¡Qué guay!

## Abrir la búsqueda con Ctrl+K

Vale, ¡última cosa! Aquí arriba estoy anunciando que abres la búsqueda con un atajo de teclado. ¡Eso es mentira! Pero podemos añadir esto... y, de nuevo, es fácil.

En el modal, añade un `data-action`. Stimulus tiene soporte incorporado para hacer cosas en `keydown`. Así que podemos decir `keydown.`, y luego la tecla que queramos, como`K`. O en este caso, `Ctrl`+`K`.

Si nos detuviéramos ahora, esto sólo se activaría si el modal estuviera enfocado y luego alguien pulsara `Ctrl`+`K`. Eso... no va a ocurrir. En lugar de eso, queremos que se abra independientemente de lo que esté enfocado. Queremos una escucha global. Para ello, añade`@window`.

Cópialo, añade un espacio, pégalo y activa también `meta+k`. Meta es la tecla comando en un Mac:

[[[ code('97700817a0') ]]]

¡Hora de probar! Me muevo y... ¡teclado! ¡Me encanta! ¡Listo!

## Componente vivo de carga perezosa

Ah, ¡y los Componentes Vivos también se pueden cargar perezosamente mediante AJAX! Observa: añade un atributo `defer`. Cuando actualicemos, no veremos ninguna diferencia... porque ese componente se oculta al cargar la página de todos modos. Pero en realidad, acaba de cargarse vacío y luego ha hecho inmediatamente una llamada Ajax para cargarse de verdad. Podemos verlo aquí abajo, en la barra de herramientas de depuración web Esta es una forma estupenda de aplazar la carga de algo pesado, para que no ralentice tu página.

No es especialmente útil en nuestro caso porque el componente SearchSite es muy ligero, así que lo eliminaré.

Mañana pasaremos un día más con los Componentes Vivos, esta vez para dotar a un formulario de superpoderes de validación en tiempo real y resolver el viejo y molesto problema de los campos de formulario dinámicos o dependientes.
