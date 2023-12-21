# Módulos JavaScript

Inspecciona el elemento de esta página y dirígete a la consola del navegador. Ah, tenemos un registro de consola que dice que viene de `assets/app.js`. Y efectivamente, si giramos y abrimos ese archivo... ¡ahí está!

[[[ code('6f2a0ece8e') ]]]

Pero, ¿cómo se carga este archivo? Para responder a eso, mira el código fuente de la página. Aquí ocurren cosas interesantes, pero quiero centrarme en una parte:`<script type="module">`, `import 'app';`.

## Módulos ECMAScript

Resulta que todos los navegadores modernos -básicamente todos excepto IE 11... y no deberías seguir soportando IE 11-, ejem, todos los navegadores modernos soportan módulos JavaScript, también conocidos como módulos ECMAScript o ESM. Pero no son nada del otro mundo: un módulo JavaScript es cualquier archivo JavaScript que utilice las sentencias `import` o`export` a las que probablemente te hayas acostumbrado en Webpack Encore.

La gran novedad es que: ¡los navegadores entienden `import` y `export` por sí mismos! No es necesario ningún paso de compilación. Si abres cualquier página HTML y dices `<script type="module">`, el código de su interior podrá utilizar las sentencias `import` y `export`. 

## Importmaps

Así que... la segunda pregunta es: ¿qué demonios es `app`? ¿Cómo es que `app` remite en última instancia a `assets/app.js`? También se trata de un nuevo truco de los navegadores llamado importmaps. Y esto no tiene nada que ver con Symfony ni con AssetMapper. Si en tu página tienes un `<script type="importmap">`, éste se convierte en un mapa clave-valor que utiliza tu navegador cuando carga módulos. Así que si decimos `import 'app'`, mira dentro de esta lista, ve `app` y en última instancia carga este archivo... que es servido por AssetMapper ¡Es un bonito trabajo en equipo!

Importmaps es compatible con todos los navegadores modernos... aunque tiene algo menos de compatibilidad que los módulos JavaScript. Afortunadamente, existe un shim o polyfill para que si tu usuario utiliza un navegador que no admite importmaps, ese shim lo añada y todo funcione.

## La función importmap()

La última pregunta que me hago es: ¿de dónde demonios viene todo esto? Para responderla, abre `templates/base.html.twig`. Todo viene de esta única línea de aquí: `{{ importmap('app') }}`:

[[[ code('567f855a87') ]]]

Como hemos pasado `app`, esto generará un `<script type="module">` con`import 'app'` dentro. Pero esto también vuelca el polyfill, algunas precargas -son buenas para el rendimiento, pero no necesarias- y, por supuesto, el propio mapa de importación. El mapa de importación se genera principalmente, aunque no totalmente (ya llegaremos a eso), a partir de este archivo `importmap.php`:

[[[ code('86eed589e8') ]]]

## El archivo importmap.php

Cuando instalamos AssetMapper, su receta nos proporcionó este archivo. Y ésta es la razón por la que el `importmap` de nuestro HTML tiene una clave `app` que apunta a `assets/app.js`.

## Escribiendo algunos módulos JavaScript

Así que quiero jugar un poco con este nuevo sistema. Dentro del directorio `assets/` -podemos organizarlo como queramos- crea un directorio `lib/` con un archivo`alien-greeting.js`. Dentro, voy a escribir un poco de JavaScript impresionante y moderno:`export default` una función, dale `message` y `inPeace` argumentos... luego registraré un mensaje utilizando un literal de plantilla -los elegantes signos de retroceso- y algunos emojis:

[[[ code('468b46743f') ]]]

¡Genial! Este nuevo archivo vive dentro de `assets/` así que, técnicamente, está disponible públicamente. Pero... nadie lo utiliza todavía.

Intentemos algo no tradicional, pero divertido para empezar. Entra en el diseño base y, en cualquier lugar, pon `<script type="module">`. Dentro, `import alienGreeting`... y pulso tabulador:

[[[ code('cd21c5cebc') ]]]

Hmm: PhpStorm ha utilizado `../assets` para la ruta. Eso no va a funcionar. En su lugar, podemos utilizar la función `asset()` y la ruta lógica: `lib/alien-greeting.js`. Luego, abajo, utiliza eso: `alienGreeting()` ¡un mensaje y no vendremos en son de paz!

[[[ code('097320f523') ]]]

¡A ver si funciona! Cierra eso, y... ¿no funciona? ¡En realidad pensaba que sí! Obtenemos un 404 para `lib/alien-greeing.js` - ¡sin "t"...! ¡Boop!

[[[ code('91fd8bff71') ]]]

¡Ahora funciona! Sin construcción, código bonito, nada especial. 

Si ves el código fuente de la página, tenemos, por supuesto, este bonito nombre de archivo versionado en `import`. Así que puedes importar cosas sencillas como `app` y confiar en que`importmap` apunte al verdadero nombre de archivo, o puedes incluir rutas completas.

## Importar desde archivos JS

Por muy divertido que fuera hackear esto en el HTML, en realidad, no solemos escribir código en línea como éste. Copia esto, deshazte del`<script type="module">`:

[[[ code('90a38d4cc7') ]]]

Luego entra en `app.js`. Pega el código aquí:

[[[ code('c5bfc76958') ]]]

Y ahora que estamos dentro de JavaScript, cuando hagamos referencia a una ruta, podemos escribirla con rutas normales y relativas: `./alien-greeting.js`:

[[[ code('f191abe3fc') ]]]

Este es el código exacto que tendríamos en Webpack Encore, con una pequeña diferencia. En Webpack, no necesitas tener el `.js` al final. Resulta que omitir la extensión es algo específico de Node. En JavaScript real, sí necesitas la extensión. Así que tienes que añadir `.js`.

Y... ¡funciona!

## PhpStorm: Auto-añadir Extensión

Por cierto, si dejas que PhpStorm autocomplete la ruta al archivo JavaScript importado, por defecto no incluirá el `.js` al final. Para solucionarlo, abre la configuración... y busca "extensiones". Ahí está "Editor"=>"Estilo de código"=> "JavaScript". Justo aquí, cambia "usar extensión de archivo" a "siempre".

Bien, ¡el día 3 está en los libros! Mañana haremos que nuestra configuración de JavaScript sea mucho más potente aprendiendo a instalar paquetes de terceros
