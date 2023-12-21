# ¡Hola ÚLTIMA PILA!

¡Hola a todos! ¡Bienvenidos a los 30 días de LAST Stack! Tengo que decir que éste puede que sea mi tutorial favorito. Me lo he pasado en grande construyéndolo, porque a diferencia de lo que suelo hacer por aquí, voy a centrarme un poco menos en enseñar conceptos profundos y más en crear un producto rico, pulido y bonito. Y creo que te va a encantar.

Pero primero, LAST Stack, ¿qué demonios es eso? Es un acrónimo que... Me inventé. Quería algo divertido que encajara con un paradigma totalmente nuevo. Son las siglas de Live Components, AssetMapper, Stimulus y Turbo. Es una pila de front-end que nos permitirá construir una interfaz de usuario realmente rica -como una aplicación de una sola página, con modales y AJAX por todas partes- pero totalmente con Symfony, Twig... y sólo un poco de JavaScript. Ah, y esto no requerirá ningún paso de compilación ni Node.js. ¡Guau!

Al final de este tutorial, vamos a tener patrones reutilizables que podemos aprovechar en nuestros proyectos para hacer las cosas realmente rápido pero que funcionan y sientan increíblemente bien.

El núcleo de todo este sistema es Hotwire: una colección de bibliotecas que incluye Turbo, Stimulus y Strada. Strada es el nuevo chico del barrio y parece genial. No tendremos tiempo de hablar de ella, pero promete permitirte tomar el mismo proyecto que estamos a punto de construir y utilizarlo para impulsar una aplicación móvil. Woh.

Otra cosa genial de Hotwire es que... no es exclusivo de Symfony. Lo utiliza, por ejemplo, la comunidad Ruby on Rails. Y muchas de las cosas que vamos a construir proceden de patrones que aprendí de gente de esa comunidad. El hecho de que todos utilicemos la misma herramienta significa que podemos compartir bibliotecas, compartir ideas y construir sobre los hombros de los demás. Eso es enorme.

## Configuración del proyecto

Así que, ¡manos a la obra! Como es divertido hacer cosas bonitas que saltan en la pantalla, deberías descargarte el código del curso y codificar conmigo. Cuando descomprimas el archivo, encontrarás un directorio `start/`, que contiene los mismos archivos que ves aquí, ¡incluido el importantísimo README.md! Aquí se explica cómo configurar el proyecto.

El último paso será abrir un terminal, entrar en el proyecto y ejecutar:

```terminal
symfony serve -d
```

Para iniciar un servidor web local en ... oh, en mi caso, `127.0.0.1:8001`. Ya debo tener algo funcionando en el puerto 8000. Haré clic en el enlace para ver una página grande y fea de... ¡nada! ¡Eso es a propósito!

Vamos a empezar con un proyecto Symfony 6.4. He preinstalado Twig y tenemos dos entidades diferentes - `Planet` y `Voyage` - porque vamos a construir un sitio de planificación de viajes para extraterrestres. También tengo algunos data fixtures y he utilizado MakerBundle para generar un CRUD para cada entidad. Este `PlanetController`,`VoyageController` y estas plantillas proceden de MakerBundle, con sólo unos pocos ajustes de estilo.

Pero básicamente... ¡no pasa nada especial! Tenemos un `MainController`, que alimenta esta página de inicio:

[[[ code('fb904fa3fa') ]]]

Contiene una consulta que nos ayudará más adelante... pero la plantilla, ahora mismo, está haciendo un montón de nada:

[[[ code('6bb0a02edd') ]]]

Sin CSS, sin JavaScript, sin activos de ningún tipo... y el sitio no hace nada. Pero en 30 breves lecciones, transformaremos esto en una pequeña obra maestra digital.

Eso es todo por el día 1. Mañana instalaremos AssetMapper: un sistema para manejar CSS, JavaScript y otros activos del frontend con pilas incluidas... pero sin ningún paso de compilación.
