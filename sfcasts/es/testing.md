# Pruebas Parte 2: Pruebas funcionales

Bienvenido de nuevo a la 2ª parte del día 29. Hoy me he saltado las normas y lo he convertido en un artículo doble. Hemos hablado de las pruebas de los componentes Twig y Live... pero también tenemos que hablar de las pruebas funcionales -o de extremo a extremo- en general. Es cuando controlamos mediante programación un navegador, hacemos que haga clic en enlaces, rellene formularios, etc.

Dos cosas sobre esto. Primero, vamos a crear un sistema que me gusta mucho. Y segundo, el camino para conseguirlo va a ser... sinceramente, un poco accidentado. No es un proceso suave y eso es algo en lo que debemos trabajar como comunidad.

## zenstruck/navegador

Symfony tiene herramientas de pruebas funcionales integradas, pero a mí me gusta utilizar otra biblioteca. En tu terminal, instálala con:

```terminal
composer require zenstruck/browser --dev
```

A continuación, en la carpeta `tests/`, crearé un nuevo directorio llamado `Functional/`... luego una nueva clase llamada `VoyageControllerTest`. Y supongo que también podría ponerla en un directorio `Controller/`.

Para las tripas, pegaré una prueba terminada:

[[[ code('1f432ba29c') ]]]

Vale, estamos utilizando `ResetDatabase` y `Factories`... extiende el`WebTestCase` normal para pruebas funcionales... y luego `HasBrowser` viene de la biblioteca Browser y nos da la capacidad de llamar a `$this->browser()` para controlar un navegador con esta API realmente suave. Esto sigue el flujo de ir a la página del viaje, hacer clic en "Nuevo viaje", rellenar el formulario, guardar y aseverar al final. La prueba comienza con un único `Voyage` en la base de datos, así que después de crear uno nuevo, aseveramos que hay dos en la página.

Para ejecutarlo, utiliza el mismo comando, pero apunta al directorio `Functional/`:

```terminal-silent
symfony php vendor/bin/simple-phpunit tests/Functional
```

Y... ¡realmente pasa! ¡Genial!

## Probar JavaScript con Panther

Pero espera un momento. Entre bastidores, esto no está utilizando un navegador real: sólo está haciendo peticiones falsas en PHP. Eso significa que no ejecuta JavaScript. Estamos probando la experiencia que tendría un usuario si tuviera desactivado JavaScript. Eso está bien para muchas situaciones. Sin embargo, esta vez quiero probar toda la fantasía modal.

Para ejecutar la prueba utilizando un navegador real que admita JavaScript -como Chrome- cambia a `$this->pantherBrowser()`:

[[[ code('102f3aa123') ]]]

Pruébalo:

```terminal-silent
symfony php vendor/bin/simple-phpunit tests/Functional
```

¡Nada! Pero un bonito error: necesitamos instalar `symfony/panther`. ¡Hagámoslo!

```terminal
composer require symfony/panther --dev
```

Panther es una biblioteca PHP que puede controlar mediante programación los navegadores reales de tu máquina. Para utilizarla, también necesitamos ampliar `PantherTestCase`:

[[[ code('613fb03282') ]]]

Inténtalo de nuevo:

```terminal-silent
symfony php vendor/bin/simple-phpunit tests/Functional
```

No vemos el navegador -se abre invisiblemente en segundo plano- ¡pero ahora está utilizando Chrome! Y la prueba falla - bastante pronto:

> Elemento clicable "Nuevo Viaje" no encontrado.

Hmmm. Ha hecho clic en "Viajes", pero no ha encontrado el botón "Nuevo viaje". Una característica fantástica de `zenstruck/browser` con Panther es que, cuando falla una prueba, hace una captura de pantalla del fallo.

Dentro del directorio `var/`... aquí está. Huh, la captura de pantalla muestra que seguimos en la página de inicio, como si nunca hubiéramos hecho clic en "Viajes"... aunque puedes ver que el enlace "Viajes" parece activo.

El problema es que la navegación por la página se realiza mediante Ajax... y nuestras pruebas no saben esperar a que termine. Hace clic en "Viajes"... e inmediatamente intenta hacer clic en "Nuevo viaje". Esto será lo principal que tendremos que arreglar.

## Cargando un servidor de desarrollo "de prueba

Pero antes de eso, ¡veo un problema mayor! Mira los datos: ¡no proceden de nuestra base de datos de prueba! Vienen de nuestro sitio de desarrollo

Aunque no podamos verlo, Panther está controlando un navegador real. Y... un navegador real necesita acceder a nuestro sitio utilizando un servidor web real a través de una dirección web real. Como estamos utilizando el servidor web Symfony, Panther lo detectó y... ¡lo utilizó!

Pero... ¡eso no es lo que queremos! ¿Por qué? Nuestro servidor utiliza el entorno `dev` y la base de datos `dev`. Nuestras pruebas deberían utilizar el entorno `test` y la base de datos`test`.

Para solucionarlo, abre `phpunit.xml.dist`. Pega dos variables de entorno:

[[[ code('97266a4f53') ]]]

La primera... es una especie de hack. Le dice a Panther que no utilice nuestro servidor. En su lugar, Panther iniciará ahora silenciosamente su propio servidor web utilizando el servidor web PHP incorporado. La segunda línea le dice a Panther que utilice el entorno `test` cuando haga eso.

En la prueba, para que sea aún más fácil ver si esto funciona, después de hacer clic en viajes, llama a `ddScreenshot()`:

[[[ code('6329ca6df4') ]]]

Haz una captura de pantalla, luego vuelca y muere.

Ejecuta:

```terminal-silent
symfony php vendor/bin/simple-phpunit tests/Functional
```

Lo hace... ¡y guarda una captura de pantalla! ¡Genial! Búscalo en `var/`. Y... vale. Parece que se está utilizando el nuevo servidor web... ¡pero faltan todos los estilos!

## Depurar abriendo el navegador

¡Es hora de hacer un poco de trabajo detectivesco! Para entender qué está pasando, podemos decirle temporalmente a Panther que abra realmente el navegador, por ejemplo, para que podamos verlo y jugar con él.

Después de visitar, digamos `->pause()`:

[[[ code('485ce2aac1') ]]]

Luego, para abrir el navegador, anteponemos al comando de prueba `PANTHER_NO_HEADLESS=1`:

```terminal-silent
PANTHER_NO_HEADLESS=1 symfony php vendor/bin/simple-phpunit tests/Functional
```

Y... ¡woh! Se abrió el navegador y luego se detuvo. Ahora podemos ver el código fuente de la página. Aquí está el archivo CSS. Ábrelo. Es un 404 no encontrado. ¿Por qué?

En el entorno de desarrollo, nuestros recursos se sirven a través de Symfony: no son archivos físicos reales. Si antepones a la URL `index.php`, funciona. Panther utiliza el servidor web PHP integrado... y necesita una regla de reescritura que le indique que envíe estas URL a través de Symfony. Sinceramente, es un detalle molesto, pero podemos arreglarlo.

De vuelta al terminal, pulsa intro para cerrar el navegador. En `tests/`, crea un nuevo archivo llamado `router.php`. Pega el código:

[[[ code('a58f94651c') ]]]

Este es un archivo "enrutador" que utilizará el servidor web incorporado. Para decirle a Panther que lo utilice, en `phpunit.xml.dist`, pegaré otra variable de entorno:`PANTHER_WEB_SERVER_ROUTER` ajustada a `../tests/router.php`:

[[[ code('6723a0c0df') ]]]

¡Pruébalo!

```terminal-silent
PANTHER_NO_HEADLESS=1 symfony php vendor/bin/simple-phpunit tests/Functional
```

Y ahora... ¡funciona! Pulsa intro para terminar. A continuación, elimina el `pause()`.

Ejecuta de nuevo la prueba, pero sin la variable de entorno:

```terminal-silent
symfony php vendor/bin/simple-phpunit tests/Functional
```

## Esperando la carga de la página turbo

Genial: ha llegado a nuestra línea de captura de pantalla. Ábrela. Vale, volvemos al problema original: no espera a que se cargue la página después de que hagamos clic en el enlace.

Resolver esto... no es tan sencillo como debería. Di `$browser =`, ciérralo e inicia una nueva cadena con `$browser` debajo. Entre medias, pegaré dos líneas. Ésta es de nivel inferior, pero espera a que se añada el atributo `aria-busy` al elemento`html`, cosa que hace Turbo cuando se está cargando. Luego espera a que desaparezca:

[[[ code('3fbdc3cc82') ]]]

Prueba ahora:

```terminal-silent
symfony php vendor/bin/simple-phpunit tests/Functional
```

Luego... abre la captura de pantalla. ¡Woh! Ahora está esperando a que termine la llamada Ajax. Pero recuerda: también estamos utilizando transiciones de vista. La página se ha cargado... pero aún está en medio de la transición. Lo arreglaremos en un minuto.

## Navegador personalizado y clase de prueba base

Pero antes, tenemos que limpiar esto: es demasiado trabajo. Lo que me gustaría es un nuevo método en el propio navegador, como `waitForPageLoad()`. ¡Y podemos hacerlo con una clase de navegador personalizada!

En el directorio `tests/`, crea una nueva clase llamada `AppBrowser`. Voy a pegar las tripas:

[[[ code('9e1a625047') ]]]

Esto extiende la clase normal `PantherBrowser` y añade un nuevo método que esas mismas dos líneas.

Cuando llamemos a `$this->pantherBrowser()`, ahora queremos que devuelva nuestro`AppBrowser` en lugar del `PantherBrowser` normal. Para ello, lo has adivinado, es una variable de entorno: `PANTHER_BROWSER_CLASS` ajustada a `App\Tests\AppBrowser`:

[[[ code('c64259d64c') ]]]

Para asegurarnos de que esto funciona, `dd(get_class($browser));`:

[[[ code('01de12c27b') ]]]

Ejecuta la prueba:

```terminal-silent
symfony php vendor/bin/simple-phpunit tests/Functional
```

Y... ¡sí! ¡Obtenemos `AppBrowser`! Por desgracia, aunque el nuevo método funcionaría, no obtenemos autocompletado. Nuestro editor no tiene ni idea de que hemos intercambiado una subclase.

Para mejorar esto, hagamos una última cosa: en `tests/`, crea una nueva clase base de prueba: `AppPantherTestCase`. Pegaré el contenido:

[[[ code('15d4e7719d') ]]]

Extiende la clase normal `PantherTestCase`... luego anula el método `pantherBrowser()`, llama al padre, pero cambia el tipo de retorno para que sea nuestro `AppBrowser`.

En `VoyageControllerTest`, cambia esto a `extend` `AppPantherTestCase` , y asegúrate de eliminar `use HasBrowser`:

[[[ code('5ef9014134') ]]]

Luego podemos ajustar las cosas: vuelve a conectar todos estos puntos... y utiliza el nuevo método: `->waitForPageLoad()`... ¡con autocompletar! Elimina el`ddScreenshot()`:

[[[ code('a6da70b75a') ]]]

¡Y veamos dónde estamos!

```terminal-silent
symfony php vendor/bin/simple-phpunit tests/Functional
```

¡Más lejos!

> Campo del formulario "Propósito" no encontrado.

Así que hizo clic en Viajes, hizo clic en "Nuevo viaje"... pero no encontró el campo de formulario. Si miramos hacia abajo en la captura de pantalla del error, podemos ver por qué: ¡el contenido del modal todavía se está cargando! Puede que veas el formulario en la captura de pantalla -a veces la captura de pantalla se produce un momento después, por lo que el formulario es visible-, pero éste es el problema.

## Desactivar las transiciones de vista

Ah, pero antes de arreglar esto, también quiero desactivar las transiciones de vista. En`templates/base.html.twig`, la forma más fácil de asegurarse de que las transiciones de vista no estropean nuestras pruebas es eliminarlas. Digamos que si `app.environment != 'test`, entonces renderiza esta etiqueta `meta`:

[[[ code('879b278701') ]]]

## Esperando a que se cargue el modal

En fin, volvamos a nuestro fallo. Cuando hacemos clic para abrir el modal, lo que necesitamos es esperar a que se abra el modal -que en realidad es instantáneo-, pero también esperar a que termine de cargarse el `<turbo-frame>`que hay dentro.

Abre `AppBrowser`. Voy a pegar dos métodos más:

[[[ code('3cf9bde54f') ]]]

El primero - `waitForDialog()` - espera hasta que ve un diálogo en la página con un atributo `open`. Y, si ese `dialog` abierto tiene un `<turbo-frame>`, espera a que se cargue: espera hasta que no haya ningún marco `aria-busy` en la página.

En `VoyageControllerTest`, después de hacer clic en "Nuevo viaje", digamos `->waitForDialog()`:

[[[ code('a836f5e9af') ]]]

Y ahora

```terminal-silent
symfony php vendor/bin/simple-phpunit tests/Functional
```

¡Tan cerca!

> `table` `tbody` `tr` esperaba 2 elementos en la página, pero sólo encontró 1.

¡Eso viene de aquí abajo! ¿Cuál es el problema esta vez? ¡Volvamos a la captura de pantalla de error! Ah: hemos rellenado el formulario, parece que incluso hemos pulsado Guardar... ¡pero estamos afirmando demasiado rápido!

Recuerda: esto se envía a un `<turbo-frame>`, así que tenemos que esperar a que ese marco termine de cargarse. Y tenemos una forma de hacerlo:`->waitForTurboFrameLoad()`. También añadiré una línea para afirmar que no podemos ver ningún diálogo abierto: para comprobar que el modal se cerró:

[[[ code('5c9767007b') ]]]

Ejecuta la prueba una vez más:

```terminal-silent
symfony php vendor/bin/simple-phpunit tests/Functional
```

Pasa. ¡Guau! Lo admito, ha sido trabajo, ¡demasiado trabajo! Pero me encanta el resultado final.

Mañana, en nuestro último día, hablaremos del rendimiento. Y a diferencia de hoy, las cosas se pondrán rápidamente en su sitio, te lo prometo.
