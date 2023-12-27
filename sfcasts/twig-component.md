# Twig Components

Today, we get to talk about one of my favorite new-ish PHP libraries: Twig
Components. They... do kind of what their name sounds like. But let's dive in
and *see* them in action.

## Installing Twig Components

Find your terminal and install the package with:

```terminal
composer require symfony/ux-twig-component
```

Twig Components is a pure PHP library... and an easy way to think about it is: a
fancier and more powerful way to do a Twig `include()`.

Over in our browser, open the edit page in a new tab so we can see the full page.
Then open the form for this: `_form.html.twig`. When you use Tailwind, creating
a button is... kind of a lot of work. Twig Components will help us centralize this.

## make:twig-component

Because this is our first Twig Component, let's be lazy and generate it. Run:

```terminal
php bin/console make:twig-component
```

Call it Button... and say no to a live component. We get to talk about
*those* in 2 days.

This created two files. The first lives in `src/Twig/Components/Button.php`:

[[[ code('c8f638eb1a') ]]]

It's... an empty class. And it's not even needed yet! In fact, we could *delete* this
and the first half of today would work fine without it. We'll come back to this later.

The more important thing is: `templates/components/Button.html.twig`. A pretty
boring-looking Twig template. Change the div to be a `<button>`,
and inside, I'll say, "Press me!":

[[[ code('32fb868b65') ]]]

To use this, over in `_form.html.twig`, say `{{ component('Button') }}`:

[[[ code('f40ae8ef5d') ]]]

If we *just* did that, it would work. We get a button that says, "press me".

## Passing Attributes to a Component

One of the first interesting things about Twig Components is that you can pass
attributes into them. As a second argument, pass `formnovalidate` set to `true`,
then `class`... copy this long class list... and paste:

[[[ code('675a13128a') ]]]

When we do that, we get an error... because I forgot my closing comma. Better.
As I was saying, when we do that... we see a button with those Tailwind classes!
This is thanks to a cool `attributes` variable that we have in any Twig Component
template. It collects what we pass into the component - called `props` - and
renders them.

## The Optional HTML Syntax

One of my *favorite* features of Twig Components is that it has
an optional, but wonderful, HTML syntax. Instead of the Twig function, we can say
`<twig:Button>`. Now props are passed like normal HTML attributes. I'll copy them
from the real `<button>` tag and paste:

[[[ code('1d980585a4') ]]]

What does it look like? The same darn thing! This special syntax comes from
Twig Components and is for *rendering* Twig Components. Some people are "meh"
on this syntax, while others *love* it. Choose whatever you want. I like it because
it *feels* like a native HTML element. And if you've ever used a front-end framework
like React, it will feel natural.

## Passing Content to the Twig Component

But, we still have hard-coded "Press me!" content. That's not very helpful. To make
this dynamic, we can use a block. That's right, a good old-fashioned Twig block!
I called this one `content`:

[[[ code('13fa571ecb') ]]]

To pass *in* that block, copy the button label below, change this to a *not*
self-closing tag, paste... then add the closing tag:

[[[ code('a53d4e69db') ]]]

And... it works! What!? When you put content between the Twig component HTML
tags, it becomes a block called `content`. That's just built in. If you also had
other blocks in your component and needed to pass *those* in too, you can do that.
And you would specify those using the normal `block`, `endblock` syntax. But you
get this `content` block for free, which *looks* fantastic.

Celebrate by removing our old HTML button:

[[[ code('80b5302146') ]]]

## Default Component Attributes

But remember: the goal is to make buttons easier to create. And needing to specify
all of these classes is... *entirely* the problem we want to fix! Copy those and
delete the `class` attribute entirely:

[[[ code('90480f3ef1') ]]]

In the component template, we *could* add a `class` attribute right here and paste.
But instead, call `attributes.defaults`, pass that an array with `class:` then
the class string:

[[[ code('d233c54058') ]]]

This will let us add *more* classes when we *use* this component. We'll do that
in minute.

Over on the site... it still looks great! Now suppose, in this one situation, we
need an extra class - `hover:animate-wiggle` - to make our button more fun:

[[[ code('b0ebf051c6') ]]]

This is a custom CSS animation I invented... so down in `tailwind.config.js`, I'll
paste the `wiggle`... and its keyframe:

[[[ code('8007b157aa') ]]]

Ok, refresh and hover! Pointless... but so fun! The important part is that we
see the normal classes that come from the component template *and* the extra class
at the end.

## Passing Variables to a Component

Could we now reuse the component for the delete button? Let's try! This lives in
`_delete_form.html.twig`. Change this to `<twig:` big "B" `Button`. Then most of
these classes are in the component already. We only need the ones related to color:

[[[ code('0e401017c2') ]]]

And... it works! But... kind of by accident. If we inspect that element, it has
the `bg-green-600` from the twig component template *and* the `bg-red-600`. You might
think... that makes sense! The later one overrides the earlier one right?

Actually, no. There's no rule that says that this one should win over this one or
the green should win over the red. The reason red is winning is... luck! By chance,
when Tailwind generated the CSS file, the `bg-red-600` was, apparently, generated
*later* in the file. So, it's winning. In Tailwind, you need to avoid competing
classes because the result isn't guaranteed.

What we really want to do is create different *variants* of the button. I'd like
to be able to say something like `variant="danger"`:

[[[ code('a99a674702') ]]]

And... over in the other template, `variant="success"`:

[[[ code('b046868b1d') ]]]

Right now, no surprise, that adds a `variant="danger"` attribute. That's not what
I want: I want to *use* this value in my component to conditionally render different
classes.

This is *finally* where our PHP class comes in handy. To convert a prop that we pass
into our component from an attribute to a *variable*, we can add a public property
with the same name: `public string $variant = 'default';`:

[[[ code('e2ed67d9a7') ]]]

And now that we have a public property called `variant`, we get a local variable
inside of Twig called `variant`. Watch `{{ variant }}`:

[[[ code('641376f758') ]]]

And now... we see it in both spots! Danger up here, success down there.

## Adding a Component PHP Method

Ok: we now need to use the `variant` variable to conditionally render
different classes. We need... kind of a switch-case statement to map each variant
to a set of classes. Writing something like that in Twig... isn't super fun.

But remember: we have a Twig component PHP class that's *bound* to this template.
And we can add methods there! I'll paste in a new public function called
`getVariantClasses()`:

[[[ code('6208c0698c') ]]]

It has a `match` statement... which based on `$this->variant`, returns a different
set of classes.

One of the superpowers of Twig components is that this `Button` object is available
inside the component template as a variable called `this`. That means we can go
to the end of the `class` list, remove the color-specific ones, then concatenate
with a `~` and `this.variantClasses`:

[[[ code('2b26048ac3') ]]]

Go check it. Yes! We have green down here... and red up there! To *really* prove
it's working, on the delete button, remove the extra classes:

[[[ code('e2ccb068ec') ]]]

I love the way that looks in code... and on the site.

## Pointing Tailwind at your Component PHP Classes

Though, one detail. Tailwind scans our source files, finds all the Tailwind
classes we're using and includes those in the final CSS file. And because
we're now including classes inside PHP, we need to make sure Tailwind sees those.

In `tailwind.config.js`, on top, I'll paste in one more line to make it scan
our Twig component PHP classes:

[[[ code('4efa1bd902') ]]]

## Changing the Component Tag Name

Ok, we're *nearly* done for today - but I want to celebrate and use the new
component in one more spot: on the voyage index page, for the "New Voyage" button.

Open `index.html.twig`... change this to a `<twig:Button>`... then we can remove
most of these classes. The bold *is* specific to this spot I think:

[[[ code('7c98d2e49f') ]]]

When we refresh... it renders! Though... when I click... nothing happens!
You probably saw why: this is now a *button*, not an `a` tag.

That's okay: we can make our component just a *bit* more flexible. In
`Button.php`, add another property: `string $tag` defaulting to `button`:

[[[ code('443fca7573') ]]]

Then in the template, use `{{ tag }}` for the starting tag and the ending tag:

[[[ code('011025934c') ]]]

Finish in `index.html.twig`: pass `tag="a"`:

[[[ code('03cd41d265') ]]]

Now over here... when we click... got it!

That's it! I hope you love Twig components as much as I do. But they can do
even more! I didn't tell you how you can prefix any attribute with `:` to pass
dynamic Twig variables or expressions to a prop. We also didn't discuss that the
component PHP classes are *services*. Yea, you can add an `__construct()` function,
autowire other services, like `VoyageRepository`, then use those to provide data
to your template... making the entire component standalone and self-sufficient.
That's one of my favorite features.

Tomorrow we're going to keep leveraging Twig components to create a modal
component... then see just how easily we can use modals whenever we want.
