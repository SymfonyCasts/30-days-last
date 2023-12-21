# Tailwind CSS

Me encanta utilizar Tailwind para CSS. Si nunca lo has usado antes, o quizá sólo hayas oído hablar de él, puede que... lo odies al principio. Esto se debe a que utilizas clases dentro de HTML para definirlo todo. Y así tu HTML puede acabar pareciendo, bueno, un poco loco. Pero dale una oportunidad. Yo me he enamorado de él. Y, en lugar de parecerme feo, me parece descriptivo.

## Tailwind requiere construcción

Tailwind no es tu tradicional monstruo CSS en el que descargas un archivo CSS gigante y lo incluyes. En su lugar, Tailwind tiene un binario que analiza todas tus plantillas, encuentra las clases que utilizas y vuelca un CSS final que contiene sólo esas clases. Así mantiene tu CSS final lo más pequeño posible.

¡Pero para hacer esto, duh duh duh! Tailwind requiere un paso de compilación. Y no pasa nada. Que no tengamos un paso de compilación para todo nuestro sistema JavaScript no significa que no podamos optar por uno pequeño para un propósito específico.

## Instalación de symfonycasts/tailwind-bundle

Existe un bundle superfácil para ayudarnos a hacer esto con AssetMapper. Se llama`symfonycasts/tailwind-bundle`. Oye, ¡he oído hablar de ellos!

Baja aquí, ve a la documentación... y copiaré la línea `composer require`. Gira y ejecútalo:

```terminal-silent
composer require symfonycasts/tailwind-bundle
```

Este bundle nos ayudará a ejecutar el comando de compilación en segundo plano y a servir el archivo final. Lo apuntamos a un archivo CSS real, y luego colará el contenido dinámico. Ya lo verás.

Mientras estamos aquí, Ejecuta::

```terminal
php bin/console debug:config symfonycasts_tailwind
```

para ver la configuración por defecto del bundle. Por defecto, el archivo que "construye" es `assets/styles/app.css`... ¡lo cual es genial porque ya tenemos un archivo `assets/styles/app.css`!

Para poner las cosas en su sitio, ejecuta un comando del bundle:

```terminal
php bin/console tailwind:init
```

Esto descarga el binario de Tailwind en segundo plano, lo cual es genial. Ese binario es independiente y no requiere Node. Simplemente funciona. El comando también hizo otras dos cosas. Primero: añadió las tres líneas necesarias dentro de `app.css`:

[[[ code('e4b6a191cd') ]]]

Cuando se construya este archivo, se sustituirán por el CSS real que necesitamos. En segundo lugar, creó un archivo `tailwind.config.js`:

[[[ code('75b1715df7') ]]]

Esto le dice a Tailwind dónde buscar todas las clases que utilizaremos. Esto encontrará cualquier clase en nuestros archivos JavaScript o en nuestras plantillas.

Para ejecutar Tailwind, ejecuta:

```terminal
php bin/console tailwind:build -w
```

Para ver. Eso construye... y luego se queda colgado, esperando futuros cambios.

Y... ¿qué ha hecho eso? Recuerda: ya estamos incluyendo `app.css` en nuestra página. Cuando actualizamos, ¡woh! ¡Se ve un poco diferente! La razón es que, si ves el código fuente de la página y haces clic para abrir el archivo `app.css`, ¡está lleno de código Tailwind! ¡Nuestro archivo`app.css` ya no es exactamente este archivo fuente! Entre bastidores, el binario de Tailwind analiza nuestras plantillas y vuelca una versión final de este archivo, que luego devuelve. Este archivo ya contiene un montón de código porque llené las plantillas CRUD con clases Tailwind antes de empezar el tutorial.

## Utilizar Tailwind

Pero veamos esto en acción de verdad. Si refrescamos la página, este es mi `h1`. Es pequeño y triste. Abre `templates/main/homepage.html.twig`. En`h1`, añade `class="text-3xl"`:

[[[ code('35feee1337') ]]]

Ahora, actualiza. ¡Funciona! Si ese `text-3xl` no estaba antes en el archivo `app.css`, Tailwind acaba de añadirlo porque se está ejecutando en segundo plano.

## Pegar el diseño

¡Así que Tailwind funciona! Para celebrarlo, vamos a pegar un diseño adecuado. Si has descargado el código del curso, deberías tener un directorio `tutorial/` con un par de archivos. Mueve `base.html.twig` a plantillas:

[[[ code('21bde93dda') ]]]

Y estos otros dos al directorio `main/`:

[[[ code('b02ad450ef') ]]]

[[[ code('4e5a7e2a38') ]]]

Actualizar. Huh, no hay diferencia. Eso es porque, al menos en un Mac, como moví y sobrescribí esos archivos, Twig no se dio cuenta de que estaban actualizados... por lo que su caché está desactualizada.

Abre una nueva pestaña del terminal y ejecuta:

```terminal
php bin/console cache:clear
```

Entonces... ¡guau! ¡Bienvenido a Space Inviters! ¡Con estilo y listo para empezar! Pero las nuevas plantillas no tienen nada de especial. Tenemos una lista de viajes... pero todo es aburrido, código Twig normal con clases Tailwind.

## Referenciar activos dinámicamente

Tenemos algunas imágenes de planetas rotas. Para arreglarlas, entra en el directorio`tutorial/assets/`... y mueve todos esos planetas a `assets/images/`. Elimina la carpeta `tutorial/`.

Esa etiqueta `img` rota proviene de la parcial `_planet_list.html.twig`. Aquí la tienes:

[[[ code('ea4148e2f8') ]]]

¡Lo dejé para que lo termináramos! ¡Qué amable por mi parte! Sabemos que podemos hacer `{{ assets() }}`y luego algo como `images/planets-1.png`. Eso funcionaría. Pero esta vez, la parte`planet-1.png` es una propiedad dinámica de la entidad `Planet`. Así que, en vez de eso, di `~` y luego `planet.imageFilename`:

[[[ code('f0b3e3e8d6') ]]]

Y... ¡bonito! Sí, ya sé que la Tierra y Saturno no tienen ese aspecto -tengo algo de aleatoriedad en mis instalaciones-, ¡pero es divertido verlos!

## Uso de KnpTimeBundle

Ya que el día 6 es el día de "hacer que todo parezca increíble", vamos a hacer dos cosas más. Para empezar, no me encanta esta fecha. Es aburrida Quiero una fecha con un aspecto genial.

Así que instala uno de mis bundles favoritos:

```terminal
composer require knplabs/knp-time-bundle
```

Esto nos proporciona un práctico filtro `ago`. Así que en cuanto esto termine, gira y abre`homepage.html.twig`. Busca `leaveAt` y ya está. Sustituye ese filtro `date` por `ago`:

[[[ code('d9d5d4b100') ]]]

Y... ¡mucho más chulo!

¿Qué más? Echa un vistazo a las áreas CRUD. Éstas se generaron mediante MakerBundle... pero... Las precargué con clases de Tailwind para que tuvieran buen aspecto. Vaya, hay mucho que celebrar ahora mismo. No me quejo.

Pero... si vas a un formulario, ¡se ve fatal! ¿Por qué? El marcado del formulario proviene del tema de formularios de Symfony... que genera los campos sin clases Tailwind.

## Ejemplos de Flowbite para Tailwind

Entonces, ¿qué hacemos? ¿Tenemos que crear un tema de formulario? Afortunadamente, no. Una de las cosas buenas de Tailwind es que hay todo un ecosistema creado a su alrededor. Por ejemplo, Flowbite es un sitio con una mezcla de ejemplos de código abierto y funciones profesionales de pago. En la parte de código abierto puedes encontrar, por ejemplo, una página de "Alertas" con diferentes marcas para crear alertas de gran aspecto. Y no necesitas instalar nada con Flowbite. Todas estas clases son nativas de Tailwind. Puedes copiar este marcado en tu proyecto y actualizarlo. Nada especial. Y me encanta.

Esto también tiene una sección de formularios donde muestra cómo podemos hacer que los formularios tengan un aspecto realmente bonito. En teoría, si pudiéramos hacer que nuestros formularios salieran así, tendrían un aspecto estupendo.

## Añadir un tema de formulario Tailwind

Y afortunadamente, hay un bundle que puede ayudarnos. Se llama`tales-from-a-dev/flowbite-bundle`. Haz clic en "Instalación" y copia la línea `composer
require`. Luego ejecútalo:

```terminal
composer require tales-from-a-dev/flowbite-bundle
```

¡Hoy nos van a instalar un montón de cosas! Nos pregunta si queremos instalar la receta contrib. Digamos que sí, permanentemente. ¡Genial!

Volviendo a las instrucciones de instalación, no necesitamos registrar el bundle -eso ocurre automáticamente-, pero sí necesitamos copiar esta línea para el archivo de configuración de tailwind.

Abre `tailwind.config.js`, y pega esto:

[[[ code('f9fbd3886e') ]]]

Este bundle viene con su propia plantilla de tema de formulario con sus propias clases Tailwind, así que queremos asegurarnos de que Tailwind las ve y genera el CSS para ellas.

El último paso en los documentos es decirle a nuestro sistema que utilice este tema de formulario por defecto. Esto ocurre en `config/packages/twig.yaml`. Lo pegaré... y luego arreglaré la sangría:

[[[ code('53a05536d6') ]]]

Ya está. Vuelve atrás, actualiza y ¡eureka! En poco más de 10 minutos, instalamos Tailwind y empezamos a utilizarlo en todas partes.

Mañana volveremos a JavaScript y aprovecharemos Stimulus para escribir código JavaScript fiable que nos encante.
