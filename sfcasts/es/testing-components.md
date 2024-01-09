# Pruebas, Parte 1: Twig y Componentes Vivos

Todos estos ingeniosos artilugios que hemos construido no son más que juguetes, a menos que podamos probarlos. Así que, ¡esa es la misión de hoy! Hay mucho que hacer, así que ¡manos a la obra!

Ejecuta:

```terminal
composer require phpunit
```

Que instala el `symfony/test-pack`, nos da todos los paquetes que necesitamos y los pone en `require-dev`.

## Probar un componente Twig

Para nuestro primer acto, vamos a probar un Componente Twig. Esto está muy bien: podemos crear el objeto componente, llamar a sus métodos y comprobar cómo se representa, todo ello de forma aislada. Es sencillo, pero vamos a probar el componente `Button`.

En el directorio `tests/`, crea un directorio `Integration/` -porque esto será una prueba de integración- y luego `Twig/Components/`. Si no conoces las pruebas de integración, consulta nuestro [Tutorial de pruebas de integración](https://symfonycasts.com/screencast/phpunit-integration).

Dentro, crea una nueva clase `ButtonTest`... y amplía la normal `KernelTestCase` para pruebas de integración:

[[[ code('c492dbae7c') ]]]

Para ayudarnos a trabajar con el componente, utiliza un rasgo llamado `InteractsWithTwigComponents`, y añade una nueva función: `testButtonRendersWithVariants()`:

[[[ code('685ba6d0b8') ]]]

## Montar el componente

El trait nos proporciona dos métodos. El primero nos permite crear el objeto componente. Digamos `$this->mountTwigComponent()` pasando el nombre del componente `Button` y cualquier props, como `variant` puesto en `success`.

Esto debería darnos un `Button`: `assertInstanceOf`, `Button::class`, `$component`. Vuelca `$component` y luego `assertSame` que `success` es igual a `$component->variant`:

[[[ code('6104b0a083') ]]]

¡Genial! Para probarlo, ejecuta:

```terminal
./vendor/bin/simple-phpunit tests/Integration
```

Eso descargará PHPUnit, y... ¡pasa! Tenemos algunos avisos de desaprobación, pero ignóralos.

## Renderizar el componente

Lo segundo que podemos hacer es renderizar un componente. Copia la parte superior, pégala en la parte inferior, cámbiale el nombre a `$rendered` y llama a `renderTwigComponent()`. Esto tiene casi los mismos argumentos, pero también podemos pasar bloques. El tercer argumento es un atajo para pasar el bloque `content`.

Vuelca `$rendered`:

[[[ code('fda53550e5') ]]]

¡Y veamos qué aspecto tiene esto!

```terminal-silent
./vendor/bin/simple-phpunit tests/Integration
```

¡Fantástico! Un objeto con el HTML dentro. Con esto, podemos obtener la cadena en bruto... o podemos acceder a un objeto `Crawler`. Esto es genial: `$this->assertSame()` que`Click Me!`, es igual a `$rendered->crawler()->filter()` - para encontrar el `span` - entonces`->text()`:

[[[ code('a431f53132') ]]]

¡Genial! Mi editor está gritando 'error de sintaxis', pero está siendo dramático. Observa:

```terminal-silent
./vendor/bin/simple-phpunit tests/Integration
```

¡Pasa!

## Probar un componente vivo

¿Qué tal si probamos la integración de un componente vivo... como nuestro elegante `SearchSite`? En el mismo directorio, crea una nueva clase llamada `SearchSiteTest`, extiende `KernelTestCase` y... esta vez utiliza `InteractsWithLiveComponents`. Crea un método: `testCanRenderAndReload()`:

[[[ code('66880afdc1') ]]]

Con este trait, podemos decir que `$testComponent` es igual a `$this->createLiveComponent()`. Pasar el nombre - `SearchSite`... y también podemos pasar cualquier props, pero no lo haré. Dejaremos que `$query` empiece vacío. `dd($testComponent)`:

[[[ code('a7e4a754fc') ]]]

Cuando lo ejecutemos:

```terminal-silent
./vendor/bin/simple-phpunit tests/Integration
```

El objeto es enorme... pero es un `TestLiveComponent`. Y tiene un montón de cosas buenas. Podemos decir `$testComponent->component()` para obtener el objeto componente subyacente, podemos renderizarlo, e incluso podemos imitar el comportamiento del usuario, como cambiar un valor del modelo, llamar a acciones en vivo, emitir eventos o incluso iniciar sesión.

## Configuración de la base de datos de prueba

Para probar la búsqueda, tenemos que añadir algunos viajes a la base de datos. En la parte superior,`use ResetDatabase` y `use Factories`:

[[[ code('ba291d937f') ]]]

Aquí abajo, utiliza `VoyageFactory::createMany()` para crear 5 travesías... y dales a todas el mismo `purpose` para que podamos buscarlas fácilmente. A continuación, crea otro`Voyage` con cualquier otro `purpose` aleatorio:

[[[ code('6f61429b9c') ]]]

Antes de aprovecharlos, vuelve a hacer la prueba:

```terminal-silent
./vendor/bin/simple-phpunit tests/Integration
```

¡Un error de conexión a la base de datos! Estoy ejecutando la base de datos a través de Docker y utilizando el binario `symfony`para establecer la variable de entorno `DATABASE_URL`. Para inyectar esa variable al ejecutar la prueba, antepone al comando `symfony php`:

```terminal-silent
symfony php vendor/bin/simple-phpunit tests/Integration
```

Y... ¡ya estamos de vuelta! Una prueba arriesgada porque no tenemos ninguna aserción. ¡Vamos a añadirlas!

Recuerda: si no hay `query`, nuestro componente no devuelve ningún viaje. Y en la plantilla `templates/components/SearchSite.html.twig`, cuando tenemos resultados, cada uno es una etiqueta `a`.

En la prueba, `$this->assertCount()` que 0 es igual a`$testComponent->render()`, luego utiliza ese mismo `->crawler()` para filtrar las etiquetas `a`.

Aquí está la parte realmente guay: llama a `$testComponent->set()` `query` a `first 5`para imitar al usuario escribiendo en el cuadro de búsqueda. Y ahora deberíamos tener 5 resultados:

[[[ code('9eb2693535') ]]]

¡Hazlo!

```terminal-silent
symfony php vendor/bin/simple-phpunit tests/Integration
```

¡Verde! Vale, hoy es un día poco ortodoxo porque... se nos ha acabado el tiempo... ¡pero tengo más cosas que decir! La próxima parte será la segunda, en la que nos ocuparemos de las pruebas funcionales de nuestro frontend con JavaScript.
