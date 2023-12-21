# Turbo Drive

¡Es el día 9! Hermoso día 9 en el que empezamos a hacer brillar nuestra aplicación. Todos los fundamentos están en su sitio (AssetMapper, Tailwind y Stimulus), así que hoy es... casi una vuelta de la victoria. Estamos a punto de sacar un gran partido a nuestro dinero gracias a una biblioteca llamada Turbo.

En este momento, nuestro sitio, por supuesto, tiene actualizaciones de página completas. Fíjate en el logotipo de la barra de direcciones. Cuando hago clic, todo se hace con una actualización completa de la página. Eso está bien. No importa, ¡no está bien! Quiero que nuestro sitio tenga una experiencia de usuario devastadoramente buena. 

Por suerte, tenemos a Turbo en nuestro equipo: una biblioteca JavaScript forjada en las profundidades de Internet, empeñada en destruir todas las actualizaciones de página completa. Mira en su sitio: no verás ninguna recarga de página completa mientras navegamos. Y comprueba lo rápido que se siente. Parece una aplicación de una sola página, porque, bueno, lo es, sólo que no es una que necesitemos construir con un framework frontend como React.

## Instalando Turbo

Al igual que Stimulus, Symfony tiene un paquete que nos ayuda a trabajar con este Turbo. Busca tu terminal y ejecuta:

```terminal
composer require symfony/ux-turbo
```

Cuando termine, haz:

```terminal
git status
```

Al igual que el otro paquete UX, este modificó `controllers.json` y `importmap.php`. En `assets/controllers.json`, añadió dos nuevos controladores:

[[[ code('661e2a081c') ]]]

El primero es... una especie de controlador falso. Carga y activa Turbo -verás lo que hace en un momento-, pero no es un controlador de Stimulus que vayamos a utilizar directamente. El segundo controlador es opcional -no vamos a hablar de él- y está desactivado por defecto.

El otro cambio, en `importmap.php` es, ninguna sorpresa: ha añadido `@hotwired/turbo`:

[[[ code('82d54c52e1') ]]]f

El resultado de este único comando es asombroso. Cuando actualice, mira la barra de direcciones: ¡no vamos a ver más recargas de páginas completas! Y todo va superrápido. Me encanta. ¡Incluso los formularios! Haz clic en editar. Observa: se envía mediante AJAX. O, si creo uno nuevo y pulso enter, se envía vía AJAX. ¡Nuestro sitio se ha transformado en una aplicación de una sola página con un solo comando!

## Turbo: ¿Cuál es el truco?

Puede que estés pensando

> Esto es demasiado bueno para ser verdad, Ryan. ¿Cuál es el truco?

Vale, hay una pega, pero menor para los nuevos proyectos: tu JavaScript debe estar escrito para funcionar sin actualizaciones completas de la página. Históricamente, hemos escrito nuestro JavaScript para que se ejecute al cargar la página... o se ejecute en `document.ready`. Y esas cosas no ocurren después de la primera carga de la página. Pero siempre que tengas todo escrito en Stimulus, estarás bien.

Por ejemplo: nuestro controlador `celebrate`: no importa en cuántas páginas haga clic, eso sigue funcionando.

Si tu aplicación aún no está preparada para Turbo -por el problema del JavaScript-, puedes desactivarlo. En `app.js`, `import * as Turbo` `from '@hotwired/turbo'` . Abajo, pon `Turbo.session.drive = false`. No voy a hacerlo... así que lo comentaré:

[[[ code('46c6ae9099') ]]]

Pero, ¿por qué iba a instalar Turbo... sólo para desactivarlo? Porque Turbo consta en realidad de tres partes. La primera se llama Turbo Drive. Es la parte que nos proporciona navegación AJAX gratuita en todos los clics de enlaces y envíos de formularios. Y eso es lo que esto desactiva.

Pero incluso si no estás preparado para Drive, puedes utilizar las otras dos partes: Turbo Frames y Turbo Streams. Son muy potentes y pasaremos mucho tiempo en este tutorial haciendo cosas increíbles con ellas.

## Precargar enlaces

Turbo Drive en sí es bastante sencillo, pero tiene algunos trucos más en la manga. Y constantemente añaden cosas nuevas. Por ejemplo, una función se llama precarga. Para mostrar esto, entra en `templates/base.html.twig`. Si alguna vez estás en una página... y estás realmente seguro de saber qué enlace va a pulsar el usuario a continuación, puedes precargarlo.

Por ejemplo, en el enlace "viajes", añade `data-turbo-preload`:

[[[ code('9407ada213') ]]]

Actualizar, inspeccionar elemento, luego ir a herramientas de red, XHR... y borrar el filtro. Cuando actualice, ¡veremos inmediatamente que se hace una petición AJAX para la página de los viajes! Por eso, cuando hagamos clic en este enlace, mira: va a ser instantáneo. ¡Boom!

Utiliza esto sólo cuando estés completamente seguro de cuál será la página siguiente. No queremos desencadenar un montón de tráfico innecesario a tu sitio que no se utilizará.

Ah, ¿y ves estos errores de JavaScript? Provienen de la barra de herramientas de depuración web y del perfilador de Symfony. No estoy seguro de por qué... pero no le gusta la precarga. Eso es algo que tenemos que arreglar, pero la precarga en sí funciona bien. Puedes ignorar esto.

De vuelta a la plantilla, elimina el `data-turbo-preload`... porque en realidad no sabemos en qué página hará clic el usuario a continuación.

Hoy ha ido genial. Con una biblioteca, hemos eliminado todas las recargas de página completa. ¿Qué podría ser lo siguiente? Mañana hablaremos de los Turbo Frames: una forma de crear "porciones" de nuestra página que se carguen con Ajax, sin escribir una sola línea de JavaScript.
