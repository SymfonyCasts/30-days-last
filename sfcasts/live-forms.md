# Real-Time Validation & Dependent Form Fields

For day 28, I want to show you one of the most common ways that people are using
Live Components: forms. Because Live Components have this power to reload as
you type, they give us interesting possibilities with forms, like real-time validation!
So here's today's goal: convert the Voyage form into a Live Component and *see*
some cool real-time validation for ourselves!

We already have a controller that takes care of creating the Voyage form and
handles this submit. What *we're* going to do is wrap the frontend part of the form
inside a Live Component so that as we type, it re-renders. But ultimately,
when we save, it'll save like normal through the controller.

## Moving the Form into a Twig Component

For step one, forget about Live Components: let's just convert the form rendering
into a Twig Component. In this case, I know we're going to need a PHP class, so
create a new one called `VoyageForm` and make it a Twig Component with
`#[AsTwigComponent]`:

[[[ code('a0318f8fba') ]]]

Perfect! The form itself lives in `templates/voyage/_form.html.twig` and uses
a `form` variable, which we'll need to pass *into* the Twig component.

In the `VoyageForm` class, add a public property for this: `public FormView $form`,
because `FormView` is the object type for the `form` variable:

[[[ code('49ec130ec6') ]]]

Next, in `templates/components/`, create the component template: `VoyageForm.html.twig`.
Copy the entire form, paste it here:

[[[ code('80ae1da5ac') ]]]

And then in `_form.html.twig`, it's simple: `<twig:VoyageForm />`:

[[[ code('6d264901ae') ]]]

And over at the browser... bah! We get:

> Variable `form` does not exist.

Let's think about this. We *do* have a public property in the component class called
`form`... so we *should* have a local variable with that name. *But*, the property
is uninitialized because I forgot to pass in that value. My bad! Pass
`:form="form"` - using `:` so that the value - `form` - is Twig code: that's the
`form` variable:

[[[ code('83053fc278') ]]]

And now... got it! Before we keep going, inside the template, remember to render
the `attributes` variable. The easiest is to wrap this in a `div` and say
`{{ attributes }}`. I'll put the closing tag... then indent the entire form:

[[[ code('93d52858fe') ]]]

So the form rendering is now a Twig component. But to give it *behavior*, we
need a Live Component.

## LiveComponent & Symfony Forms

Let's think. After changing any field, I want a Live Component to collect the value
of every field and send them to the Live Component system via an Ajax call. The
Live Component will then *submit* these values into the form object and rerender
the template.

Using Symfony forms with Live Components is a bit more of a complex use-case than
the *normal* case of Live components: where we create some public properties and
make them writable.

Fortunately, Live Component ships with a trait to help. In `VoyageForm`, first,
convert this to a Live Component by saying `#[AsLiveComponent]` then using the
`DefaultActionTrait`:

[[[ code('c9fcc6a6d2') ]]]

Next, because we want to bind this component to a form object, use
`ComponentWithFormTrait`. When we do that, we don't need this public `form`
property anymore because that lives inside the trait:

[[[ code('0b8a4efa51') ]]]

However, this trait *does* require one new method. Go to "Code"->"Generate" - or
`Cmd`+`N` on a Mac - and implement the one we need: `instantiateForm()`:

[[[ code('35b679eeed') ]]]

This might look strange at first. But remember, as we change fields in our form, the
form values will be sent via Ajax back to our Live component... which then needs
to *submit* them into the form object so it can re-render. This means that, during
the Ajax call, our Live Component needs to be able to create our form object. To
do that, it calls this method.

To get the logic for this, in `VoyageController`, all the way at the bottom, copy
the guts of `createVoyageForm()`... then paste them here. Hit okay to add
the two `use` statements:

[[[ code('408b1b9578') ]]]

There's... just one problem: the `createForm()` and `generateUrl()` methods don't
exist here! But I haven't told you about a crazy, cool thing: Live Components
are Symfony controllers in disguise! And this means we can extend
`AbstractController`:

[[[ code('ef0029f4fe') ]]]

That's totally allowed and gives us access to all the shortcuts we know and love.

Ok, showtime! Move over. When I type, nothing happens. In
this case, Live Components waits for the field to *change*... so it waits for us
to move *off* of the field. As soon as we do, we'll see an Ajax request fire
down here. Watch. Boom! See it? That sent the data back, submitted the form and
*re-rendered* the form.

To prove this, clear out the field and hit tab. A validation error! That's coming
from Symfony and the normal form validation rendering! Type something again, tab,
it goes away. The best part? The planet field down here is *also* required thanks
to Symfony's validation constraints. But the Live Component system is smart: it
knows that the user hasn't *changed* this field yet, so it shouldn't show the
validation error. But if we *do* select a planet... then clear, when it re-renders,
it shows the error.

## Passing the Initial Form Data

This also works fine for the edit form. Hit edit & clear out a field.

Though, check out `instantiateForm()`. Hmm, we're always instantiating a
*new* `Voyage` object: there's never a `$voyage` variable. We change a field,
Live Components sends an Ajax request and, when it creates the form, it does it
using a brand *new* `Voyage` object, not the *existing* `Voyage` object from
the database.

And... that's probably okay... because it submits all the data onto it, and it
renders correctly.

However, one thing you can do with Live components is submit the form directly
*into* the Component object and handle the save logic there. We're not going to do
that, but if we *did*, the `Voyage` object bound to the form would always be a *new*
object... and it would always insert a new row into the database. 

## Passing in the Initial Form Data

So even though this works, it's a bit weird.

To tighten this up, we can store the existing `Voyage` object on the component
and use *that* during form creation. Add a public `?Voyage` `$initialFormData`
property. Above this, to make the component system *remember* this value through
all of its Ajax requests, add `#[LiveProp]`:

[[[ code('277ffc3369') ]]]

This is now a non-writable prop that our component will keep track of. And yes,
it's non-writable: the user changes the *form* data directly, not this property.
This is *just* here to help us create the form object on each Ajax call.

Below, change this to `$voyage` equals `$this->initialFormData`, else `new Voyage()`:

[[[ code('325c642e43') ]]]

Finally, pass in the `initialFormData` by saying `:initialFormData="voyage"`, which
is a Twig variable that we already have:

[[[ code('37beb9eeb5') ]]]

So we won't notice a difference, but when we hit edit and change a field,
that Ajax request now creates a Form object bound to this existing `Voyage` object.

That got a bit technical, but let's zoom out. By rendering out form through
a Live Component, we get real-time validation for free! That's *cool*.

## Dependent Form Fields

We're almost out of time, but I think we can tackle one more form problem today.
In fact, maybe *the* most *painful* form problem in all of Symfony.

On this form, if the planet is *not* in our solar system, I want to render a new
dropdown for an optional wormhole upgrade. This is the classic dependent form field
problem. In Symfony, it's hard because we need to leverage form events. On
the frontend it's hard too! Historically, we needed to write JavaScript
to trigger an Ajax call to re-render the form.

But... that second part is now taken care of! Live Components is great at
re-rendering the form when fields change. And the first part? Yea, there's a new
library that makes *that* easy too!

It's called `symfonycasts/dynamic-forms`... created by us because this problem
drove me absolutely crazy. Hat tip to Symfony dev Ben Davies who really
cracked the code on this.

Copy the composer require line, spin over, and run that:

```terminal-silent
composer require symfonycasts/dynamic-forms
```

Using this is really pleasant. Find the form class: `src/Form/VoyageType.php`.
The library uses decoration. At the top, say `$builder` equals
`new DynamicFormBuilder()` and pass in `$builder`:

[[[ code('66069516ed') ]]]

This `DynamicFormBuilder` has the same methods as the original, but one extra:
`addDependent()`. But before we use it, comment-out the
`'autocomplete' => true`:

[[[ code('184956e151') ]]]

There's a bug with the autocomplete system and Live Components. It should be fixed
soon, but I don't want it to get in the way.

Anyway, the `addDependent()` method takes three arguments. The first is the name
of the new field: `wormholeUpgrade`. The second is an array of fields that
this field *depends* on. In this case, that's only `planet`. The final
argument is a callback function and *its* first argument will always be a
`DependentField` object. We'll see how that's used in a minute. Then, this will
receive the value of every field that it depends on. Because we depend only
on `planet`, the callback will receive *that* as an argument: `?Planet` `$planet`:

[[[ code('a79a91f757') ]]]

Inside, if we *don't* have a planet - because the user hasn't selected one yet
*or* the planet is in the Milky Way, just return. And yes, I borked up my
space science: I meant for this to be `isInOurSolarSystem()` - not the milky way.
Forgive me Data!

Anyway, because we're returning, there won't be a `wormholeUpgrade`
field at all. Else, add one with `$field->add()`. This method is identical
to the normal `add()` method except that we don't need to pass the *name* of the
field... because we already pass it earlier. So skip straight to
`ChoiceType::class`... then the options with `choices` set to an array of "Yes"
for true, and "No" for false:

[[[ code('685d418d03') ]]]

Done! Go check out the result. Refresh, edit and change to a planet that's not in
our system. There it is! The field popped into existence! If we go back to a planet
that *is* in our solar system... gone! And... the field saves just fine. When we
edit the voyage, the form starts with it. It just works!

Ok, we're nearly at the end of our 30-day journey! Tomorrow, it's time to talk
about how we can *test* our beautiful new frontend features.
