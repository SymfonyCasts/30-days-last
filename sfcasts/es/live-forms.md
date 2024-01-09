# Validación en tiempo real y campos de formulario dependientes

Para el día 28, quiero mostrarte una de las formas más comunes en que la gente utiliza los Componentes Vivos: los formularios. Dado que los Componentes Vivos tienen la capacidad de recargarse a medida que escribes, nos ofrecen interesantes posibilidades con los formularios, ¡como la validación en tiempo real! Así que éste es el objetivo de hoy: ¡convertir el formulario Voyage en un Componente Vivo y ver por nosotros mismos una genial validación en tiempo real!

Ya tenemos un controlador que se encarga de crear el formulario Voyage y gestiona el envío. Lo que vamos a hacer es envolver la parte frontal del formulario dentro de un Componente LIVE para que, a medida que escribimos, se vuelva a renderizar. Pero al final, cuando guardemos, se guardará como siempre a través del controlador.

## Trasladar el formulario a un componente Twig

Para el primer paso, olvídate de los Componentes Vivos: simplemente convirtamos la renderización del formulario en un Componente Twig. En este caso, sé que vamos a necesitar una clase PHP, así que crea una nueva llamada `VoyageForm` y conviértela en un Componente Twig con`#[AsTwigComponent]`:

[[[ code('a0318f8fba') ]]]

¡Perfecto! El formulario en sí vive en `templates/voyage/_form.html.twig` y utiliza una variable `form`, que tendremos que pasar al componente Twig.

En la clase `VoyageForm`, añade una propiedad pública para esto: `public FormView $form`, porque `FormView` es el tipo de objeto de la variable `form`:

[[[ code('49ec130ec6') ]]]

A continuación, en `templates/components/`, crea la plantilla del componente: `VoyageForm.html.twig`. Copia todo el formulario y pégalo aquí:

[[[ code('80ae1da5ac') ]]]

Y luego en `_form.html.twig`, es sencillo: `<twig:VoyageForm />`:

[[[ code('6d264901ae') ]]]

Y en el navegador... ¡bah! Obtenemos:

> La variable `form` no existe.

Pensemos en esto. Tenemos una propiedad pública en la clase componente llamada`form`... por lo que deberíamos tener una variable local con ese nombre. Pero, la propiedad no está inicializada porque olvidé pasarle ese valor. ¡Culpa mía! Pasa`:form="form"` - utilizando `:` para que el valor - `form` - sea código Twig: ésa es la variable`form`:

[[[ code('83053fc278') ]]]

Y ahora... ¡ya lo tengo! Antes de continuar, dentro de la plantilla, recuerda renderizar la variable `attributes`. Lo más fácil es envolverla en `div` y decir`{{ attributes }}`. Pondré la etiqueta de cierre... y luego sangraré todo el formulario:

[[[ code('93d52858fe') ]]]

Así que la representación del formulario es ahora un componente Twig. Pero para darle comportamiento, necesitamos un Componente Live.

## LiveComponent y formularios Symfony

Pensemos. Después de cambiar cualquier campo, quiero que un Live Component recoja el valor de cada campo y los envíe al sistema Live Component mediante una llamada Ajax. A continuación, el Componente Live enviará estos valores al objeto formulario y volverá a renderizar la plantilla.

Utilizar formularios Symfony con Live Components es un caso de uso un poco más complejo que el caso normal de Live Components: en el que creamos algunas propiedades públicas y las hacemos escribibles.

Afortunadamente, Live Component incluye un trait para ayudarte. En `VoyageForm`, primero, conviértelo en un Live Component diciendo `#[AsLiveComponent]` y luego utilizando el rasgo`DefaultActionTrait`:

[[[ code('c9fcc6a6d2') ]]]

A continuación, como queremos vincular este componente a un objeto formulario, utiliza`ComponentWithFormTrait`. Cuando hagamos eso, ya no necesitaremos esta propiedad pública `form`porque vive dentro del rasgo:

[[[ code('0b8a4efa51') ]]]

Sin embargo, este rasgo requiere un nuevo método. Ve a "Código"->"Generar" -o`Cmd`+`N` en un Mac- e implementa el que necesitamos: `instantiateForm()`:

[[[ code('35b679eeed') ]]]

Esto puede parecer extraño al principio. Pero recuerda que, a medida que cambiemos los campos de nuestro formulario, los valores del formulario se enviarán vía Ajax de vuelta a nuestro componente Live... que luego necesita enviarlos al objeto formulario para que pueda volver a renderizarse. Esto significa que, durante la llamada Ajax, nuestro componente Live necesita ser capaz de crear nuestro objeto formulario. Para ello, llama a este método.

Para obtener la lógica de esto, en `VoyageController`, abajo del todo, copia las tripas de `createVoyageForm()`... y pégalas aquí. Pulsa OK para añadir las dos sentencias `use`:

[[[ code('408b1b9578') ]]]

Hay... sólo un problema: ¡los métodos `createForm()` y `generateUrl()` no existen aquí! Pero no te he hablado de una cosa loca y genial: ¡Los Componentes Live son controladores Symfony disfrazados! Y esto significa que podemos extender`AbstractController`:

[[[ code('ef0029f4fe') ]]]

Eso está totalmente permitido y nos da acceso a todos los atajos que conocemos y amamos.

Vale, ¡hora del espectáculo! Muévete. Cuando escribo, no pasa nada. En este caso, Live Components espera a que el campo cambie... así que espera a que nos movamos del campo. En cuanto lo hagamos, veremos dispararse una petición Ajax aquí abajo. Observa. ¡Bum! ¿Lo ves? Ha devuelto los datos, ha enviado el formulario y lo ha vuelto a renderizar.

Para comprobarlo, borra el campo y pulsa tabulador. ¡Un error de validación! ¡Eso viene de Symfony y de la renderización normal de validación del formulario! Vuelve a escribir algo, pulsa tabulador y desaparecerá. ¿Lo mejor? El campo planeta de aquí abajo también es obligatorio gracias a las restricciones de validación de Symfony. Pero el sistema Live Component es inteligente: sabe que el usuario aún no ha cambiado este campo, por lo que no debería mostrar el error de validación. Pero si seleccionamos un planeta... y luego borramos, cuando se vuelve a renderizar, muestra el error.

## Pasar los datos iniciales del formulario

Esto también funciona bien para el formulario de edición. Pulsa editar y borra un campo.

Aunque, echa un vistazo a `instantiateForm()`. Hmm, siempre estamos instanciando un nuevo objeto `Voyage`: nunca hay una variable `$voyage`. Cambiamos un campo, Live Components envía una petición Ajax y, cuando crea el formulario, lo hace utilizando un objeto `Voyage` totalmente nuevo, no el objeto `Voyage` existente en la base de datos.

Y... eso probablemente esté bien... porque envía todos los datos en él, y se renderiza correctamente.

Sin embargo, una cosa que puedes hacer con los componentes Live es enviar el formulario directamente al objeto Componente y manejar allí la lógica de guardado. No vamos a hacer eso, pero si lo hiciéramos, el objeto `Voyage` vinculado al formulario sería siempre un objeto nuevo... y siempre insertaría una nueva fila en la base de datos. 

## Pasar los datos iniciales del formulario

Aunque esto funciona, es un poco raro.

Para ajustarlo, podemos almacenar el objeto `Voyage` existente en el componente y utilizarlo durante la creación del formulario. Añade una propiedad pública `?Voyage` `$initialFormData` . Sobre ella, para que el sistema del componente recuerde este valor a través de todas sus peticiones Ajax, añade `#[LiveProp]`:

[[[ code('277ffc3369') ]]]

Esta es ahora una propiedad no escribible de la que nuestro componente hará un seguimiento. Y sí, es no escribible: el usuario cambia directamente los datos del formulario, no esta propiedad. Esto sólo está aquí para ayudarnos a crear el objeto formulario en cada llamada Ajax.

A continuación, cambia esto a `$voyage` igual a `$this->initialFormData`, si no `new Voyage()`:

[[[ code('325c642e43') ]]]

Por último, pasa `initialFormData` diciendo `:initialFormData="voyage"`, que es una variable Twig que ya tenemos:

[[[ code('37beb9eeb5') ]]]

Así que no notaremos ninguna diferencia, pero cuando pulsemos editar y cambiemos un campo, esa petición Ajax creará ahora un objeto Formulario vinculado a este objeto `Voyage` existente.

Esto es un poco técnico, pero ampliémoslo. Al renderizar nuestro formulario a través de un Componente Vivo, ¡obtenemos validación en tiempo real gratis! Es genial.

## Campos de formulario dependientes

Casi no nos queda tiempo, pero creo que hoy podemos abordar otro problema de formularios. De hecho, quizá el problema de formularios más doloroso de todo Symfony.

En este formulario, si el planeta no está en nuestro sistema solar, quiero mostrar un nuevo desplegable para una mejora opcional del agujero de gusano. Este es el clásico problema de campo de formulario dependiente. En Symfony, es difícil porque necesitamos aprovechar los eventos del formulario. En el frontend también es difícil Históricamente, necesitábamos escribir JavaScript para activar una llamada Ajax para volver a renderizar el formulario.

Pero... ¡ahora ya no es necesario! Live Components es genial para volver a mostrar el formulario cuando cambian los campos. ¿Y la primera parte? Sí, ¡hay una nueva biblioteca que también lo hace fácil!

Se llama `symfonycasts/dynamic-forms`... creada por nosotros porque este problema me volvía absolutamente loco. Gracias a Ben Davies, desarrollador de Symfony, que ha descifrado el código.

Copia la línea "composer require", dale la vuelta y ejecútalo:

```terminal-silent
composer require symfonycasts/dynamic-forms
```

Usar esto es realmente agradable. Busca la clase de formulario: `src/Form/VoyageType.php`. La biblioteca utiliza decoración. En la parte superior, di que `$builder` es igual a`new DynamicFormBuilder()` y pasa a `$builder`:

[[[ code('66069516ed') ]]]

Este `DynamicFormBuilder` tiene los mismos métodos que el original, pero uno extra:`addDependent()`. Pero antes de utilizarla, comenta el`'autocomplete' => true`:

[[[ code('184956e151') ]]]

Hay un error con el sistema de autocompletar y Live Components. Debería solucionarse pronto, pero no quiero que estorbe.

De todos modos, el método `addDependent()` recibe tres argumentos. El primero es el nombre del nuevo campo: `wormholeUpgrade`. El segundo es una matriz de campos de los que depende este campo. En este caso, sólo es `planet`. El último argumento es una función de devolución de llamada y su primer argumento siempre será un objeto`DependentField`. Veremos cómo se utiliza en un minuto. Entonces, ésta recibirá el valor de cada campo del que dependa. Como sólo dependemos de `planet`, la llamada de retorno lo recibirá como argumento: `?Planet` `$planet` :

[[[ code('a79a91f757') ]]]

Dentro, si no tenemos un planeta -porque el usuario aún no lo ha seleccionado o el planeta está en la Vía Láctea-, simplemente devuelve. Y sí, me he equivocado con la ciencia espacial: Quería que fuera `isInOurSolarSystem()` - no la vía láctea ¡Perdóname Data!

De todos modos, como estamos regresando, no habrá ningún campo `wormholeUpgrade`. Si no, añade uno con `$field->add()`. Este método es idéntico al método normal `add()`, salvo que no necesitamos pasar el nombre del campo... porque ya lo pasamos antes. Así que pasa directamente a`ChoiceType::class`... y luego a las opciones con `choices` configurado como una matriz de "Sí" para verdadero, y "No" para falso:

[[[ code('685d418d03') ]]]

¡Listo! Comprueba el resultado. Actualiza, edita y cambia a un planeta que no esté en nuestro sistema. ¡Ahí lo tienes! ¡El campo ha aparecido! Si volvemos a un planeta que esté en nuestro sistema solar... ¡desapareció! Y... el campo se guarda perfectamente. Cuando editamos el viaje, el formulario se inicia con él. ¡Funciona!

Vale, ¡ya casi hemos llegado al final de nuestro viaje de 30 días! Mañana toca hablar de cómo podemos probar nuestras nuevas y bonitas funciones del frontend.
