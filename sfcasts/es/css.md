# CSS

¿Ya es el 5º día? ¡Estamos volando! Es hora de añadir algo de CSS a nuestro sitio. ¿Y cómo funciona eso dentro de AssetMapper?

## ¿Incluyendo una Etiqueta de enlace manual?

Bueno, ya tenemos un archivo `assets/styles/app.css`. Y... nada nos impide entrar en `base.html.twig`, y añadir una etiqueta de enlace: `link`,`rel="stylesheet"`, `href` luego `asset()` y la ruta lógica: `styles/app.css`.

¡Estupendo! Cuando actualizamos... y miramos la fuente de la página, ¡ahí está! Funciona de maravilla y es superaburrido. El tipo de aburrimiento que me gusta.

Sin embargo, si eliminamos esta línea... y vamos a actualizar la página. Huh, seguimos teniendo este fondo de `blue`: un fondo azul que viene de `app.css`:

[[[ code('63108a07e9') ]]]

Echa otro vistazo a la fuente de la página. ¿Todavía hay una etiqueta `link` que apunta a ese archivo? Volvamos a `base.html.twig`, hmm, aquí no hay nada. ¿De dónde viene eso?

La respuesta -apuesto a que lo has adivinado- es la función `importmap()`:

[[[ code('5b3d3d7599') ]]]

Y es porque se está importando de `app.js`:

[[[ code('b96c71e397') ]]]

## Cómo funciona CSS

Importar un archivo CSS desde JavaScript es algo a lo que probablemente te hayas acostumbrado con Webpack Encore. Importas un archivo CSS... y en última instancia, se renderiza en la página como una etiqueta `link`. Sin embargo, esto no es algo que admitan realmente los módulos ECMAScript. Lo único que puedes importar son archivos JavaScript. Así que esto debería fallar estrepitosamente: como que debería descargar el archivo CSS e intentar parsearlo como JavaScript.

Sin embargo, como habrás notado, ¡no falla! ¡Me encantan los misterios!

Se trata de una función totalmente adicional que hemos añadido a AssetMapper. Funciona así En `base.html.twig`, decimos `importmap('app')`. El `app` se conoce como el punto de entrada: el único archivo que el navegador ejecutará directamente. Y sabemos que se refiere a `assets/app.js`.

Así que lo que hace AssetMapper es entrar en este archivo y encontrar todas las declaraciones `import`para archivos JavaScript y CSS. Por cada importación CSS que encuentra, la añade como etiqueta `link`. Es... básicamente así de sencillo.

## El truco del mapa de importación CSS

Bueno, hay una pequeña y fascinante complicación. Ve a la pestaña red de tu navegador y busca `app`. Este es el archivo `app.js` que está ejecutando el navegador. Fíjate: ¡todavía tiene la declaración de importación al archivo CSS! Si lo piensas, cuando nuestro navegador ejecuta esta línea, ¡debería fallar! Debería descargar el archivo CSS, intentar interpretarlo como JavaScript y encontrar un error de sintaxis, pero no lo hace.

La razón es un truco dentro de AssetMapper. Cuando importas un archivo CSS, AssetMapper añade una entrada importmap para él. Así que, aunque empiece por `./`, nuestro navegador busca si hay una ruta coincidente dentro del mapa de importación, y la hay. Por eso, descarga este archivo.... que no es un archivo real. Es un archivo falso que no hace.... absolutamente nada. Así que hace que esa línea no de error y... no haga nada.

## Importar CSS de otros archivos JavaScript

Para ver lo potente que es esto, vamos a crear un segundo archivo CSS para apoyar nuestro saludo alienígena. Llámalo `alien-greeting.css` y haz que el fondo del cuerpo sea `darkgreen`. Aunque, personalmente, espero que los extraterrestres tengan los colores del arco iris:

[[[ code('6e3923c492') ]]]

En `alien-greeting.js`, importa eso: `../styles/alien-greeting.css`:

[[[ code('d10e2f8aaf') ]]]

¿Funcionará? ¡Pruébalo! Actualiza y... ¡fondo verde! En el código fuente, tenemos una segunda etiqueta `link` y un segundo elemento nuevo en `importmap`. ¡Estupendo! Como `app.js` importa `alien-greeting.js`, AssetMapper también encuentra los archivos CSS que importa.

## Carga perezosa de CSS

Aquí es donde las cosas se ponen realmente espeluznantes. Los módulos JavaScript tienen una sintaxis de importación dinámica que te permite importar módulos de forma asíncrona. Esto nos permite cargar un archivo más tarde, cuando lo necesitemos, en lugar de al cargar la página. Y podemos utilizar este truco con CSS.

Copia esto. Imagina que sólo queremos cargar ese archivo CSS cuando `inPeace` sea igual a false. Así que diré, si no `inPeace`, entonces usa `setTimeout()` para esperar 4 segundos. Después de 4 segundos, importa el archivo CSS. Excepto que, en cuanto necesites que una importación no viva al principio de tu archivo, tendrás que llamarla como una función:

[[[ code('192d631fec') ]]]

Eso está muy bien. Pruébalo. Al principio, ¡fondo azul! 2, 3, 4, ¡fondo verde! El archivo CSS se cargó perezosamente. ¿Cómo? Bueno, ya no hay etiqueta de enlace `alien-greeting.css` en el código fuente de la página. En su lugar, esperamos a que el navegador ejecute esta línea JavaScript. Cuando lo hace, lo busca en el mapa de importación, lo encuentra y descarga este archivo falso. Pero esta vez, en lugar de ser una línea que no hace nada, este archivo falso añade una nueva etiqueta `link` a la sección `head` con`rel="stylesheet"` y `href` establecidas en `alien-greeting.css`.

Joder, ¡podemos ver esto en tiempo real! Aquí, bajo la etiqueta `head`, vemos la hoja de estilos. Si actualizo y abro rápidamente eso, no está ahí. Y... entonces se añade. Qué guay.

Ahora que ya sabemos cómo funciona el CSS, ¡mañana lo utilizaremos para dar vida a nuestro sitio web! Pero quiero hacerlo desde un ángulo extra divertido: Quiero utilizar Tailwind CSS.
