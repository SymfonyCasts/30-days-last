# Mapeador de activos

Vale, ¿cómo vamos a introducir CSS y JavaScript en nuestra aplicación? ¿Vamos a añadir un sistema de compilación como Vite o Webpack? ¡Pues no! ¡Esa es una de las cosas divertidas de todo esto! Vamos a crear algo increíble sin ningún sistema de compilación. Para ello, vamos a instalar un nuevo componente de Symfony llamado AssetMapper.

## Instalar AssetMapper

Gira hasta nuestro terminal y ejecuta:

```terminal
composer require symfony/asset-mapper
```

Esta es la nueva alternativa a Webpack Encore. Puede hacer prácticamente todo lo que Encore puede hacer y más... pero es mucho más sencillo. Definitivamente deberías utilizarla en los nuevos proyectos.

Al Ejecuta:

```terminal
git status
```

Vemos que su receta Flex ha realizado una serie de cambios. Por ejemplo, `.gitignore`está ignorando un directorio `public/assets/` y `assets/vendor/`:

[[[ code('2b0d764e8d') ]]]

Hablaremos más sobre esto más adelante. Pero en producción, aquí es donde se escribirán tus activos y, cuando instalemos bibliotecas JavaScript de terceros, vivirán en ese directorio `vendor/`.

También actualiza `base.html.twig` y añade un archivo `importmap.php`. Pero déjalos en un segundo plano por ahora: hablaremos de ellos mañana.

## Las "rutas mapeadas

Para la aventura de hoy, imagina que, cuando instalamos esto, lo único que nos dio fue un nuevo archivo`asset_mapper.yaml` y un directorio `assets/`. Vamos a comprobar ese archivo de configuración: `config/packages/asset_mapper.yaml`:

[[[ code('ea53167677') ]]]

La idea de AssetMapper no podría ser más sencilla: defines rutas -como el directorio`assets/` - y AssetMapper hace que todos los archivos que contengan estén disponibles públicamente... como si vivieran en el directorio `public/`.

## Hacer referencia a un archivo de activos

Veámoslo en acción tú. Si te has descargado el código del curso, deberías tener un directorio `tutorial/`, que he añadido para que podamos copiar algunas cosas de él. Copia `logo.png`. Dentro de `assets/`, podemos darle el aspecto que queramos. Así que vamos a crear un nuevo directorio llamado `images/` y a pegarlo dentro.

Como este nuevo archivo vive dentro del directorio `assets/`, deberíamos poder referenciarlo públicamente. Hagámoslo en nuestro diseño base: `templates/base.html.twig`. En cualquier lugar, di `<img src="">`, `{{` y luego utiliza la función normal `asset()`. Como argumento, pasa la ruta relativa al directorio `assets/`. Esto se llama la ruta lógica: `images/logo.png`:

[[[ code('bbb066562f') ]]]

Antes de probar esto, una forma fácil de ver todos los activos disponibles es mediante: 

```terminal
php bin/console debug:asset
```

Muy sencillo: esto busca en todas tus rutas mapeadas -sólo `assets/` para nosotros-, encuentra cada archivo y luego los enumera con su ruta lógica. Así que puedo ser perezoso y copiar eso, pegarlo aquí.... y listo.

Ahora bien, cuando probamos esto, ¡no funciona! La función `asset()` sigue siendo un componente propio, así que vamos a instalarla:

```terminal
composer require symfony/asset
```

Y ahora.... ¡un logo genial!

## Versionado instantáneo de activos

Para ver lo realmente genial, inspecciona la imagen y fíjate en el nombre del archivo. Es`/assets/images/logo-` y luego este largo hash. Este hash procede del contenido del archivo. Si actualizáramos `logo.png`, se generaría automáticamente un nuevo hash. Y eso es superimportante por dos razones, relacionadas entre sí. Primero, porque cuando despleguemos, el nuevo nombre de archivo reventará la caché del navegador de nuestros usuarios para que vean el nuevo archivo inmediatamente. Y en segundo lugar, gracias a esto, podemos configurar nuestro servidor web de producción para que sirva todos los activos con cabeceras de Expiración de larga duración, lo que maximiza el almacenamiento en caché y el rendimiento.

## Servir activos en desarrollo frente a producción

Ahora, en el entorno `dev`, no existe un archivo físico con este nombre de archivo, sino que la petición de este activo se procesa a través de Symfony y es interceptada por un receptor del núcleo. Ese listener mira la URL, encuentra el `logo.png`correspondiente dentro del directorio `assets/images/` y lo devuelve.

Pero en producción, eso no es lo suficientemente rápido. Así que, al desplegar, se ejecuta:

```terminal
php bin/console asset-map:compile
```

Muy sencillo: esto escribe todos los archivos en el directorio `public/assets/`. Mira: ¡en `public/assets/`, ahora tenemos archivos reales, físicos! Así que cuando voy y actualizo, este archivo no está siendo procesado por Symfony, está cargando uno de esos archivos reales.

Ahora bien, si alguna vez ejecutas este comando localmente, asegúrate de borrar ese directorio después... para que deje de utilizar las versiones compiladas:

```terminal-silent
rm -rf public/assets/
```

¡Vaya! ¡El día 2 ya está hecho! Ahora tenemos una forma de servir imágenes, CSS o cualquier archivo públicamente con versionado automático de archivos. La segunda parte de `AssetMapper` trata sobre los módulos JavaScript. Y ese será el tema de mañana.
