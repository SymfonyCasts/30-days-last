# Stimulus (estímulo)

Bienvenido al día de suerte número 7. Hoy hablamos de Stimulus: una biblioteca JavaScript pequeña y fácil de entender que nos permite crear código superorganizado que... simplemente siempre funciona. Es una de mis razones favoritas para utilizar Internet.

## Instalar StimulusBundle

Pero aunque Stimulus es una librería JavaScript... Symfony tiene un bundle para ayudarnos a cargarla, configurarla y utilizarla. Así que, busca tu terminal y ejecuta:

```terminal
composer require symfony/stimulus-bundle
```

Una de las cosas más importantes de StimulusBundle es su receta. Cuando termine, ejecuta:

```terminal
git status
```

## La Receta Cambia

Oooh. Ha hecho varios cambios. El primero está aquí, en `assets/app.js`. Encima -quitaré ese comentario- ahora importamos un nuevo `bootstrap.js`:

[[[ code('5d5ccda147') ]]]

Ese archivo inicia la aplicación Stimulus.

Observa que importa un módulo `@symfony/stimulus-bundle`:

[[[ code('d57b0f0acf') ]]]

El símbolo `@` no es importante: es sólo un carácter que utilizan los paquetes JavaScript con espacio de nombres. Lo importante es que se trata de una importación desnuda, lo que significa que el navegador intentará encontrar este paquete mirando nuestro mapa de importación.

Vale Abre `importmap.php`. La receta ha añadido dos nuevas entradas aquí:

[[[ code('1f57ed519a') ]]]

La primera es para el propio Stimulus, que ahora vive en el directorio `assets/vendor/`. La segunda es... una especie de paquete "falso" de terceros. Dice que `@symfony/stimulus-bundle`debe resolver a un archivo en nuestro directorio `vendor/`. Esto es un poco de fantasía: decimos `import '@symfony/stimulus-bundle'`... y eso importará en última instancia este archivo `loader.js` desde `vendor/`.

La receta también añadió un directorio `controllers/` -el hogar de nuestros controladores de Stimulus personalizados- y un archivo `controllers.json`, del que hablaremos mañana.

Ah, y en `base.html.twig`, añadió esta línea `ux_controller_link_tags()`:

[[[ code('788558ce83') ]]]

¡Bórrala! Eso era necesario con AssetMapper 6.3, pero ya no. De todas formas, hablaremos de ello mañana.

## Utilizar Stimulus

Vale: todo lo que hemos hecho es `composer require` este nuevo bundle. Y, sin embargo, cuando actualizamos la página y miramos la consola, ¡Stimulus ya está funcionando! Estos`application #starting` y `application #start` proceden de Stimulus. Es increíble.

Con StimulusBundle, cualquier cosa que pongamos en el directorio `controllers/` estará automáticamente disponible como controlador de Stimulus. Así, el hecho de que tengamos un`hello_controller.js` significa que podemos utilizar un controlador llamado `hello`:

[[[ code('36568b772a') ]]]

De hecho, podemos verlo ahora mismo. Cuando se activa este controlador, sustituye el texto del elemento al que está unido. Para comprobar que Stimulus funciona, inspecciona cualquier elemento de la página... e introduce un `data-controller="hello"`.

Cuando pulse intro, ¡boom! Se activa el controlador.

## Crear un controlador personalizado

Ha sido divertido, pero vamos a crear nuestro propio controlador real. Copia `hello_controller.js`y crea un nuevo archivo llamado `celebrate_controller.js`. Eliminaré los comentarios y el método connect:

[[[ code('571bc3e5e2') ]]]

Éste es el objetivo: cuando pasemos el ratón por encima del logotipo, quiero llamar a un método del controlador que active la biblioteca `js-confetti`. Empieza por crear el método. Podría llamarse como quieras, pero `poof()` ¡seguro que es un nombre divertido!

Dirígete a `app.js`, copia el código de `js-confetti` y elimínalo:

[[[ code('9f376e82d9') ]]]

Colócalo en el controlador `celebrate`... y mueve la declaración de importación a la parte superior:

[[[ code('3a84b43701') ]]]

¡Genial! El último paso es activar esto en un elemento. Hazlo en `base.html.twig`. Veamos... aquí está el logotipo. Añade `data-controller="celebrate"`. Y para activar la acción al pasar el ratón, di `data-action=""`... y la sugerencia es casi correcta. El formato es, primero: el evento JavaScript que queremos escuchar. En lugar de `click`, queremos `mouseover`. Luego `->`, el nombre de nuestro controlador, `#` y el nombre del método: `poof`:

[[[ code('aa59ae159b') ]]]

¡Ya está! ¡Refresca y celébralo! Cada vez que `mouseover`, llama al método. Puedes verlo abundantemente en la consola.

Vaya, en cuanto añadimos un controlador al directorio `controllers/`, ya está cargado y listo para funcionar. Recuerda, todo sin compilar.

## Carga perezosa de controladores

Pero a veces puedes tener un controlador que sólo se utiliza en determinadas páginas... por lo que no quieres obligar a tu usuario a descargarlo inmediatamente en cada página. Si te encuentras en esa situación, puedes hacer que tu controlador sea perezoso. Es lo mejor.

Para ello, añade este comentario especial sobre él: `stimulusFetch: 'lazy'`:

[[[ code('61cf5a8dff') ]]]

Sí, es una locura. Pero en cuanto hagamos eso, en lugar de descargar este archivo al cargar la página, esperará hasta que vea un elemento en la página con`data-controller"celebrate"`.

Observa: borra temporalmente el `data-controller`. Luego vuelve, actualiza, y en las herramientas de red, si busco `celebrate`, no hay nada. Si busco`confetti` -desde que nuestro controlador importa- `js-confetti`, tampoco está. No se han descargado.

Limpia tus herramientas de red. Luego ve al logo y hackea ese `data-controller`. Estamos imitando lo que ocurriría si cargáramos algo de HTML fresco vía AJAX y... ese HTML fresco incluye un elemento con `data-controller"celebrate"`.

En cuanto eso aparezca en la página, vuelve a las herramientas de red. ¡Aparecieron dos elementos nuevos! Se dio cuenta del `data-controller` y descargó el controlador y `js-confetti`... ya que eso se importa desde el controlador. Y funciona de maravilla. Esto me encanta.

Mantén este controlador perezoso, pero vuelve a `base.html.twig`, vuelve a añadir `data-controller`.

¡Una de las grandes cosas de Stimulus es que lo utiliza gente de toda la Interwebs! Y hay muchos controladores Stimulus prefabricados por ahí para ayudarnos a resolver problemas. Uno de ellos se llama Symfony-UX. Mañana nos sumergiremos en uno de sus paquetes.
