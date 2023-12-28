# Componentes Twig

Hoy vamos a hablar de una de mis nuevas librerías PHP favoritas: Twig Components. Hacen más o menos lo que su nombre indica. Pero vamos a sumergirnos y verlos en acción.

## Instalación de los Componentes Twig

Busca tu terminal e instala el paquete con:

```terminal
composer require symfony/ux-twig-component
```

Twig Components es una biblioteca PHP pura... y una forma fácil de pensar en ella es: una forma más elegante y potente de hacer un Twig `include()`.

En nuestro navegador, abre la página de edición en una pestaña nueva para que podamos ver la página completa. A continuación, abre el formulario para esto: `_form.html.twig`. Cuando usas Tailwind, crear un botón es... un montón de trabajo. Los Componentes Twig nos ayudarán a centralizarlo.

## make:componente twig

Como éste es nuestro primer Componente Twig, seamos perezosos y generémoslo. Ejecuta:

```terminal
php bin/console make:twig-component
```

Llámalo Botón... y di no a un componente vivo. Hablaremos de eso dentro de 2 días.

Esto creó dos archivos. El primero vive en `src/Twig/Components/Button.php`:

[[[ code('c8f638eb1a') ]]]

Es... una clase vacía. ¡Y ni siquiera se necesita todavía! De hecho, podríamos eliminarla y la primera mitad de hoy funcionaría bien sin ella. Volveremos sobre esto más adelante.

Lo más importante es: `templates/components/Button.html.twig`. Una plantilla Twig de aspecto bastante aburrido. Cambia el div para que sea un `<button>`, y dentro diré: "¡Presióname!":

[[[ code('32fb868b65') ]]]

Para usar esto, en `_form.html.twig`, di `{{ component('Button') }}`:

[[[ code('f40ae8ef5d') ]]]

Si hiciéramos eso, funcionaría. Obtendremos un botón que dirá: "¡Presióname!".

## Pasar atributos a un componente

Una de las primeras cosas interesantes de los Componentes Twig es que puedes pasarles atributos. Como segundo argumento, pasa `formnovalidate` a `true`, luego `class`... copia esta larga lista de clases... y pégala:

[[[ code('675a13128a') ]]]

Cuando hacemos eso, obtenemos un error... porque se me olvidó la coma de cierre. Mejor. Como decía, cuando hacemos eso... ¡vemos un botón con esas clases de Tailwind! Esto es gracias a una genial variable `attributes` que tenemos en cualquier plantilla de Componente Twig. Recoge lo que pasamos al componente -llamado `props` - y lo renderiza.

## La sintaxis HTML opcional

Una de mis características favoritas de los Componentes Twig es que tiene una sintaxis HTML opcional, pero maravillosa. En lugar de la función Twig, podemos decir`<twig:Button>`. Ahora los props se pasan como atributos HTML normales. Los copiaré de la etiqueta real `<button>` y los pegaré:

[[[ code('1d980585a4') ]]]

¿Qué aspecto tiene? ¡Lo mismo de siempre! Esta sintaxis especial proviene de Twig Components y sirve para representar componentes Twig. A algunas personas no les gusta esta sintaxis, mientras que a otras les encanta. Elige la que quieras. A mí me gusta porque parece un elemento HTML nativo. Y si alguna vez has usado un framework front-end como React, te parecerá natural.

## Pasar contenido al componente Twig

Pero seguimos teniendo contenido "¡Presióname!" codificado. Eso no es muy útil. Para hacerlo dinámico, podemos utilizar un bloque. Así es, un bloque Twig a la antigua usanza. Lo he llamado `content`:

[[[ code('13fa571ecb') ]]]

Para pasar ese bloque, copia la etiqueta del botón de abajo, cámbiala por una etiqueta de no autocierre, pégala... y luego añade la etiqueta de cierre:

[[[ code('a53d4e69db') ]]]

Y... ¡funciona! ¿¡Qué!? Cuando pones contenido entre las etiquetas HTML del componente Twig, se convierte en un bloque llamado `content`. Eso ya está incorporado. Si tuvieras otros bloques en tu componente y necesitaras pasarlos también, puedes hacerlo, y los especificarías utilizando la sintaxis normal de `block`, `endblock`. Pero obtienes gratis este bloque `content`, que tiene un aspecto fantástico.

Celébralo eliminando nuestro antiguo botón HTML:

[[[ code('80b5302146') ]]]

## Atributos del componente por defecto

Pero recuerda: el objetivo es que los botones sean más fáciles de crear. Y tener que especificar todas estas clases es... ¡totalmente el problema que queremos solucionar! Cópialos y elimina por completo el atributo `class`:

[[[ code('90480f3ef1') ]]]

En la plantilla de componentes, podríamos añadir un atributo `class` justo aquí y pegarlo. Pero en lugar de eso, llama a `attributes.defaults`, pásale un array con `class:` y luego la cadena de clase:

[[[ code('d233c54058') ]]]

Esto nos permitirá añadir más clases cuando utilicemos este componente. Lo haremos en un minuto.

En el sitio... ¡sigue teniendo un aspecto estupendo! Ahora supongamos que, en esta situación, necesitamos una clase extra - `hover:animate-wiggle` - para que nuestro botón sea más divertido:

[[[ code('b0ebf051c6') ]]]

Se trata de una animación CSS personalizada que he inventado... así que abajo, en `tailwind.config.js`, pegaré la clase `wiggle`... y su fotograma clave:

[[[ code('8007b157aa') ]]]

Vale, ¡actualiza y pasa el ratón! Sin sentido... ¡pero muy divertido! Lo importante es que vemos las clases normales que vienen de la plantilla del componente y la clase extra al final.

## Pasar variables a un componente

¿Podríamos reutilizar ahora el componente para el botón de borrar? ¡Intentémoslo! Éste vive en`_delete_form.html.twig`. Cámbialo a `<twig:` gran "B" `Button`. Entonces la mayoría de estas clases ya están en el componente. Sólo necesitamos las relacionadas con el color:

[[[ code('0e401017c2') ]]]

Y... ¡funciona! Pero... un poco por accidente. Si inspeccionamos ese elemento, tiene el `bg-green-600` de la plantilla del componente Twig y el `bg-red-600`. Podrías pensar... ¡eso tiene sentido! El último anula al anterior, ¿no?

En realidad, no. No hay ninguna regla que diga que ésta deba ganar a ésta o que la verde deba ganar a la roja. La razón por la que gana el rojo es... ¡la suerte! Por casualidad, cuando Tailwind generó el archivo CSS, el `bg-red-600` se generó, aparentemente, más tarde en el archivo. Así que está ganando. En Tailwind, debes evitar competir entre clases porque el resultado no está garantizado.

Lo que realmente queremos hacer es crear distintas variantes del botón. Me gustaría poder decir algo como `variant="danger"`:

[[[ code('a99a674702') ]]]

Y... en la otra plantilla, `variant="success"`:

[[[ code('b046868b1d') ]]]

Ahora mismo, sin sorpresa, eso añade un atributo `variant="danger"`. Eso no es lo que quiero: Quiero utilizar este valor en mi componente para mostrar condicionalmente diferentes clases.

Aquí es donde finalmente nuestra clase PHP resulta útil. Para convertir una prop que pasamos a nuestro componente de un atributo a una variable, podemos añadir una propiedad pública con el mismo nombre: `public string $variant = 'default';`:

[[[ code('e2ed67d9a7') ]]]

Y ahora que tenemos una propiedad pública llamada `variant`, obtenemos una variable local dentro de Twig llamada `variant`. Mira `{{ variant }}`:

[[[ code('641376f758') ]]]

Y ahora... ¡lo vemos en los dos sitios! Peligro aquí arriba, éxito allí abajo.

## Añadir un método PHP de componente

Bien: ahora necesitamos utilizar la variable `variant` para representar condicionalmente diferentes clases. Necesitamos... una especie de sentencia switch-case para asignar cada variante a un conjunto de clases. Escribir algo así en Twig... no es muy divertido.

Pero recuerda: tenemos una clase PHP del componente Twig que está vinculada a esta plantilla. ¡Y podemos añadir métodos allí! Pondré una nueva función pública llamada`getVariantClasses()`:

[[[ code('6208c0698c') ]]]

Tiene una sentencia `match`... que basándose en `$this->variant`, devuelve un conjunto diferente de clases.

Uno de los superpoderes de los componentes Twig es que este objeto `Button` está disponible dentro de la plantilla del componente como una variable llamada `this`. Eso significa que podemos ir al final de la lista `class`, eliminar las específicas de color y luego concatenarlas con una `~` y `this.variantClasses`:

[[[ code('2b26048ac3') ]]]

Ve a comprobarlo. ¡Sí! Tenemos verde aquí abajo... ¡y rojo allí arriba! Para comprobar realmente que funciona, en el botón de borrar, elimina las clases extra:

[[[ code('e2ccb068ec') ]]]

Me encanta el aspecto que tiene en el código... y en el sitio.

## Apuntando Tailwind a tus clases PHP componentes

Aunque, un detalle. Tailwind escanea nuestros archivos fuente, encuentra todas las clases Tailwind que estamos utilizando y las incluye en el archivo CSS final. Y como ahora estamos incluyendo clases dentro de PHP, tenemos que asegurarnos de que Tailwind las ve.

En `tailwind.config.js`, en la parte superior, pegaré una línea más para que escanee las clases PHP de nuestro componente Twig:

[[[ code('4efa1bd902') ]]]

## Cambiar el nombre de la etiqueta del componente

Bien, ya casi hemos terminado por hoy, pero quiero celebrarlo y utilizar el nuevo componente en un lugar más: en la página de índice de viajes, para el botón "Nuevo viaje".

Abre `index.html.twig`... cambia esto por un `<twig:Button>`... entonces podremos eliminar la mayoría de estas clases. La negrita es específica de este punto, creo:

[[[ code('7c98d2e49f') ]]]

Cuando actualizamos... ¡se renderiza! Aunque... cuando hago clic... ¡no pasa nada! Probablemente hayas visto por qué: esto es ahora un botón, no una etiqueta `a`.

No pasa nada: podemos hacer que nuestro componente sea un poco más flexible. En`Button.php`, añade otra propiedad: `string $tag` por defecto `button`:

[[[ code('443fca7573') ]]]

Luego, en la plantilla, utiliza `{{ tag }}` para la etiqueta inicial y la etiqueta final:

[[[ code('011025934c') ]]]

Termina en `index.html.twig`: pasa a `tag="a"`:

[[[ code('03cd41d265') ]]]

Ahora por aquí... cuando hagamos clic... ¡ya está!

¡Ya está! Espero que te gusten los componentes Twig tanto como a mí. ¡Pero pueden hacer aún más! No te he contado cómo puedes anteponer a cualquier atributo el prefijo `:` para pasar variables o expresiones dinámicas Twig a un prop. Tampoco hemos hablado de que las clases PHP de los componentes son servicios. Sí, puedes añadir una función `__construct()`, autoconectar otros servicios, como `VoyageRepository`, y luego utilizarlos para proporcionar datos a tu plantilla... haciendo que todo el componente sea independiente y autosuficiente. Ésa es una de mis características favoritas.

Mañana vamos a seguir aprovechando los componentes Twig para crear un componente modal... y ver con qué facilidad podemos utilizar modales cuando queramos.
