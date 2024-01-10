# Rendimiento

¡Hemos llegado al último día de LAST Stack! Llevaba 30 días esperando para decir eso.

Hoy todo gira en torno al rendimiento, empezando por las cosas que no estamos haciendo.

## No combinar ni minificar archivos

Por ejemplo, no combinamos archivos para reducir peticiones. Y no estamos minificando archivos. No, estamos sirviendo archivos fuente sin procesar desde nuestro directorio `assets/`.

Y, sin embargo, ¡nuestro frontend es rápido! Abre tus herramientas de depuración y ve a Lighthouse. Hagamos un perfil de rendimiento en el escritorio para simplificar las cosas. Dale unos segundos para que se ejecute y... ¡boom! ¡99! ¡Es increíble!

## En producción: Compresión y almacenamiento en caché

Desplázate hacia abajo para ver lo que podríamos mejorar. El problema número uno es la falta de compresión. Hay dos cosas en las que debes pensar cuando despliegues tu aplicación con AssetMapper.

Primero: en tu servidor web, activa la compresión, como gzip o Brotli. O puedes hacer proxy de tu sitio a través de Cloudflare y que haga la compresión por ti. Eso es lo que hacemos nosotros. Por eso no necesitamos preocuparnos de la minificación: si te limitas a comprimir tus archivos CSS y JavaScript, eso hace un trabajo casi tan bueno como la minificación.

La segunda cosa que tienes que hacer -que debería mencionarse aquí abajo, ah sí:

> Servir los activos estáticos con una política de caché eficiente.

Como todos nuestros archivos tienen un hash de versión automático en el nombre del archivo, debes configurar tu servidor web para que almacene en caché todo lo que haya en tu directorio `assets/`... para siempre. Esto significa que cuando tu usuario descargue un archivo, lo almacenará en caché para siempre: nunca necesitará volver a descargarlo. Eso es estupendo para el rendimiento.

## ¿CSS sin usar?

Veamos qué más tenemos. Reduce el CSS no utilizado. Probablemente no sea un problema. De hecho, es una de las ventajas de Tailwind: sólo crea el CSS que realmente utilizamos. Supongo que el resto del CSS se utiliza en páginas diferentes. Y la diferencia es incluso menor de lo que parece. Son 38 kilobytes... antes de la compresión. En producción, la diferencia sería mucho menor.

## JavaScript no utilizado

Bajo reducir JavaScript no utilizado, hay un elemento principal: es el JavaScript de Live Components, que es bastante grande. Lo estamos utilizando, pero es cierto que aún no utilizamos muchas de sus funciones. En producción, debido a la compresión, sería más pequeño... y vamos a optimizarlo un poco.

Lo siguiente es: eliminar los recursos que bloquean la renderización. Esto es importante y enumera nuestro archivo CSS. Volveremos a esto en unos minutos.

Pero en realidad... no hay nada importante. Podríamos minificar el CSS, pero apenas supondría una diferencia. Minificar JavaScript - 68 kilobytes parece bueno, pero de nuevo, eso es antes de comprimirlo. ¡Y recuerda nuestra puntuación de 99! ¡Nuestro frontend es rápido!

Aunque parece que mis imágenes son demasiado grandes. Todavía hay algunas cosas que tienes que manejar por tu cuenta.

## Precargar

Una de las principales razones por las que nuestra aplicación ya es tan rápida es la precarga. Mira el código fuente de la página. Tenemos el mapa de importación, un montón de precargas, y luego lo más importante: `<script type="module">`, `import 'app'`.

Cuando nuestro navegador ve esto, conecta `app` con el nombre de archivo real y empieza a descargarlo. Las etiquetas de script de módulo no son "bloqueantes de renderizado". Esto significa que el navegador comienza a descargar este archivo, pero continúa renderizando la página visualmente mientras lo hace. Pero, por supuesto, no puede ejecutar nuestro JavaScript hasta que termine de descargar `app.js`.

Y ahí se esconde un problema. Sólo cuando termina de descargar `app.js` se da cuenta de que... también necesita descargar este archivo, y este archivo, y este archivo, y este archivo, y este archivo. Y sólo después de descargar `bootstrap.js` se da cuenta de que necesita descargar este archivo. Puedes imaginarte una gran cascada: termina un archivo JavaScript, inicia algunos más, los termina, y luego inicia aún más. Nuestro JavaScript podría tardar mucho tiempo en ejecutarse finalmente.

Aquí es donde entran en juego las precargas. Esto le dice a nuestro navegador:

> Aún no te has dado cuenta, pero deberías empezar a descargar estos archivos inmediatamente.

La forma en que se generan es realmente genial. Abre `templates/base.html.twig`. Todo esto se genera gracias a `importmap('app')`:

[[[ code('50281e14d2') ]]]

Al pasar `app`, el efecto principal es que añade la etiqueta script en la parte inferior que importa `app`.

Pero esto también le dice a AssetMapper que analice `app.js`, encuentre todos los archivos que importa y los añada como precargas. Y lo hace recursivamente: entra en`bootstrap.js` y encuentra su importación. Encuentra todo el JavaScript que se necesita al cargar la página y se asegura de que cada archivo esté precargado. Simplemente funciona.

Y podemos verlo visualmente. En `alien-greeting.js`: comenta la importación del archivo CSS: el retraso sólo hace que la cascada sea más difícil de ver:

[[[ code('9c5baa6162') ]]]

Luego ve a la pestaña Red, mira sólo en JavaScript y haz una actualización forzada: ¡compruébalo! ¡Todos los archivos JavaScript se inician al mismo tiempo! No está esperando a que se descargue nada: todos se inician inmediatamente. Eso es lo que queremos ver.

El único archivo que se inicia más tarde es `celebrate-controller.js`... porque lo hemos configurado para que sea perezoso. Esto significa que nuestro JavaScript se inicializa y luego descarga este controlador sólo cuando es necesario... que es siempre porque está en todas las páginas, pero aún así se retrasa un poco.

## Componentes activos de carga lenta

Ordena esto por tamaño de archivo. El archivo más grande es el JavaScript de los Componentes Vivos. Estos 123 kilobytes no están comprimidos, por lo que serán más pequeños en producción. Pero como sólo lo necesitamos en la búsqueda global, podemos optar por retrasar su carga.

Para ello, dentro de `assets/controllers.json`, busca el controlador de Componentes Vivos y establece `fetch` en `lazy`:

[[[ code('f12258ec44') ]]]

Haz una actualización forzada. Sigue ahí, pero comprueba el iniciador: es de un archivo JavaScript y se inicia mucho más tarde. En el código fuente, busca`live_controller`. Antes estaba precargado. Ahora, cuando actualizamos, sigue en el mapa de importación, pero ya no está precargado. Precargamos lo realmente importante, y dejamos que el controlador en vivo se cargue más tarde.

## Precargar CSS con WebLink

Ok una última cosa, cosa mágica. Lo más importante que vimos dentro de Lighthouse fue el recurso de bloqueo de renderizado para nuestro archivo CSS. Cuando tu navegador ve una etiqueta `<link rel="stylesheet">`, congela la renderización de la página hasta que termina de descargar el archivo. Y eso es bueno: no queremos que nuestra página se renderice sin estilo ni un segundo.

Y ésta es la razón por la que colocamos nuestras etiquetas CSS `link` en `head` de la página: queremos que el navegador se dé cuenta de que necesita descargar el archivo lo antes posible. Sin embargo, hay una forma de decirle a nuestro navegador incluso antes que necesita descargar este archivo.

Busca tu terminal y ejecuta:

```terminal
composer require symfony/web-link
```

Este es un pequeño paquete que puede ayudar a añadir pistas a tu navegador sobre lo que necesita descargar. AssetMapper viene con una integración especial para ello.

Observa: sólo con instalarlo, ve a la pestaña Red, filtra todo, actualiza y ve arriba a la petición principal de la página. Mira aquí abajo en las cabeceras de respuesta ¡Ahí está! Nuestra aplicación acaba de añadir una nueva cabecera de respuesta llamada `link` que apunta al archivo CSS con `rel="preload"`.

Esto indica al navegador que debe descargar este archivo. Y ve esta cabecera incluso antes de ver la línea 11 del HTML. Esto ayuda un poco más al rendimiento.

Ahora que hemos hecho algunos cambios, volvamos a ejecutar Lighthouse. Hay cierta variabilidad en estas ejecuciones, así que si tu puntuación no cambia o incluso baja un poco, no te preocupes. Pero ¡un 100 perfecto! ¡Guau!

Y lo que es más importante: ...., seguimos teniendo compresión de texto... pero no vemos el aviso de bloqueo de recursos de renderizado.

La moraleja de la historia es la siguiente: utilizar AssetMapper es rápido desde el primer momento. Aparte de añadir compresión y almacenamiento en caché a tu servidor web, puedes codificar tranquilamente sin preocuparte. Y claro, más adelante, es útil ejecutar Lighthouse y ver cómo puedes mejorar, pero no tiene por qué ser algo en lo que pienses día a día. En lugar de eso, haz tu trabajo de verdad.

Y... ¡hemos terminado! ¡Gracias por pasar estos 30 días salvajes conmigo! Ha sido un placer absoluto y un viaje increíble. Por favor, ¡ve a construir cosas y cuéntanos qué son! Y si tienes alguna pregunta, comentario, duda o chiste malo, siempre estamos aquí para ti en la sección de comentarios.

Muy bien amigos, ¡hasta la próxima!
