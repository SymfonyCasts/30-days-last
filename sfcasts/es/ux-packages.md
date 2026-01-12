# Paquetes Symfony UX

Visita https://ux.symfony.com. Este es el sitio de la Iniciativa Symfony UX: un grupo de paquetes PHP y JavaScript que nos proporcionan controladores Stimulus gratuitos. Hay un controlador Stimulus que puede renderizar chart.js, otro que puede añadir un recortador de imágenes, etc.

Hoy vamos a centrarnos en conseguir un controlador Stimulus gratuito que nos proporcione un elegante elemento autocompletar `select`. Puedes buscar, seleccionar... es todo muy bonito.

En nuestro sitio, dirígete a la sección de viajes y pulsa editar. El formulario tiene un desplegable de planetas, que está bien... ¡pero quiero darle más asombroso!

## Instalar UX Autocompletar

Vamos a instalar este paquete. La librería UX Autocompletar es una mezcla de PHP con un controlador Stimulus en su interior. Copia la línea `composer require` y pégala:

```terminal-silent
composer require "symfony/ux-autocomplete:^2.0"
```

Cuando termine... Ejecuta:

```terminal
git status
```

Oooh: la receta modificó dos cosas interesantes: `controllers.json` y `importmap.php`Sabemos que todo lo que haya en el directorio `assets/controllers/` estará disponible como controlador Stimulus. Además, todo lo que esté en `controllers.json`también se registrará como controlador Stimulus:

[[[ code('a0c5cb651e') ]]]

Es una forma de que los paquetes PHP de terceros añadan más controladores. La receta ha añadido esta entrada, lo que básicamente significa que cogerá algo de código del paquete que acabamos de instalar y lo registrará como controlador Stimulus.

El caso es que ahora tenemos un tercer controlador Stimulus en nuestra aplicación El otro cambio que hizo la receta está en `importmap.php`: añadió una nueva entrada para `tom-select`:

[[[ code('148f78d522') ]]]

`tom-select` es un paquete JavaScript... y en realidad es lo que hace el trabajo pesado para la funcionalidad de autocompletar. Este controlador de Stimulus es sólo una pequeña envoltura alrededor de `tom-select`. Y así, como ese controlador necesita `tom-select`, ¡se añadió!

## UX "autoimportar" CSS

Pero cuando actualizamos la página, nos encontramos con un error encantador. Dice

> No se ha podido encontrar el `autoimport` `tom-select.default.css` en `importmap.php`.
> Prueba a ejecutar `importmap:require` y luego esa ruta.

Vuelve a mirar en `controllers.json`. A veces, estos controladores tienen una sección extra llamada `autoimport`:

[[[ code('b7da768a4a') ]]]

La idea es que un controlador Stimulus puede tener un archivo CSS que lo acompañe. Esta sección te permite activar o desactivar esos archivos CSS. Por ejemplo, con `tom-select`, hay un archivo CSS por defecto. O si utilizas Bootstrap, puedes utilizar el archivo CSS de Bootstrap 5. Podríamos poner esto en `false` y esto en `true`.

Una diferencia entre utilizar módulos JavaScript en un navegador frente a Node y Webpack es la cantidad de paquete que obtienes. Con Node, cuando escribes `npm add tom-select`, se descarga todo el paquete: los archivos JavaScript, los archivos CSS y todo lo demás. Pero con AssetMapper y el entorno del navegador en general, cuando `importmap:require tom-select`, descargas un único archivo: sólo el archivo JavaScript. Los archivos CSS no están ahí.

Sin embargo, con `importmap:require`, podemos, por supuesto, coger un paquete con su nombre, así:

```terminal-silent
php bin/console importmap:require tom-select
```

Genial. Pero también podemos importar una ruta de archivo específica dentro de ese paquete. Y, como AssetMapper admite archivos CSS, esa ruta puede ser a un archivo CSS.

En otras palabras, si necesitamos este archivo CSS del proveedor, podemos obtenerlo con:

```terminal
php bin/console importmap:require tom-select/dist/css/tom-select.default.css
```

¡Lo tengo! En el directorio `assets/vendor/`... ¡ahí está! Y en`importmap.php`, también está ahí. Esto significa que está disponible para que lo importe nuestro controlador Stimulus.

¿El resultado final? ¡El error ha desaparecido! Y en el código fuente de la página, está el archivo CSS.

## Aplicar la función Autocompletar a un campo

Vale, después de una llamada a `composer require`, una llamada a `importmap:require` y un montón de parloteos míos, tenemos un nuevo controlador Stimulus de autocompletar listo para funcionar.

Podríamos añadir un `data-controller` al elemento `select`. Pero recuerda: Los paquetes UX suelen ser una mezcla de controladores Stimulus y código PHP. En este caso, el código PHP nos permite activar el controlador directamente en nuestro formulario. Abre`src/Form/VoyageType.php`. El campo `planet` es un `EntityType`:

[[[ code('47f0fc9e5a') ]]]

Y, gracias al nuevo paquete, cualquier `EntityType` o `ChoiceType` tiene ahora una opción`autocomplete`. Ponlo en `true`:

[[[ code('3df89ff996') ]]]

Y ahora... ¡Tachán! Puede que a la policía de la moda no le encante esto, ¡pero funciona! Esa opción activó el controlador Stimulus: incluso puedes verlo en la página. Aquí está el `select` ahora con un `data-controller` seguido del nombre largo de ese controlador.

## Personalizar el CSS

¿Cómo podemos mejorar este aspecto? Gracias al `autoimport`, el`tom-select.default.css` al menos hace que se vea bien. Si estuviéramos usando Bootstrap, cambiaría esto por `true`, esto por `false`, `importmap:require` el archivo Bootstrap y estaríamos bien.

Ahora mismo, no hay soporte oficial para Tailwind, así que le daremos estilo manualmente. En `assets/styles/app.css`, quitaré `body`. Además de las cosas de Tailwind, puedes pegar cualquier estilo personalizado que necesites. Estos anulan algunos de los estilos predeterminados para que se vean bien en nuestro modo oscuro con temática espacial:

[[[ code('a3eaffe7d1') ]]]

Y ahora... ¡me encanta!

## Hacer perezosos los controladores UX

Ah, y ¿recuerdas que podemos hacer que nuestros controladores sean perezosos añadiendo un comentario especial? Podemos hacer lo mismo con los controladores cargados en `controllers.json` estableciendo `fetch` en `lazy`:

[[[ code('67972b3b98') ]]]

Compruébalo. Ve a la página de viajes. Voy a mis herramientas de red, actualizo y busco "autocompletar"... y "TomSelect". Nada Pero en cuanto vayamos a la página de edición donde se está utilizando: busca "autocompletar". ¡Ahí lo tienes! "TomSelect" y el archivo CSS también se cargaron perezosamente, sólo cuando los necesitábamos.

¡Ya hemos terminado con el día 8! ¡Una semana y un día completos en LAST Stack! Mañana, ¡vamos a darle caña y a transformar nuestra aplicación en una maravilla elegante de una sola página con Turbo! En los próximos 7 días... las cosas empezarán a volverse locas.
