# Modal Twig Component

Today is a good day. Today we get to combine our modal system with Twig components
to achieve a goal! I want to be able to quickly add a modal *anywhere* in our app.

## Creating the Modal Component

Start in `base.html.twig`. All the way at the bottom, copy the modal markup.
You can see... it's quite a bit: not something we want to copy and
paste somewhere else.

Copy, then delete it. Let's craft a `Modal` component, this time by hand. Create
a new file in `templates/components/` called `Modal.html.twig`, and paste.

Like I said with the `Button`, we don't *need* a PHP class for a component.
Because we don't have one, we call this an "anonymous component".

On top, render `attributes`... then add `.defaults()` so we can move these two
attributes *into* that. Paste... then each of these needs a makeover to fit as
Twig keys and values instead of HTML attributes.

I like it! Over in `base.html.twig`, render this with `<twig:Modal>`. Easy!

## Adding Blocks to the Component

However, look closer at `Modal.html.twig`: there are some things that *shouldn't*
be here. For example, the `<turbo-frame>`! Not every modal needs a frame.
A lot of times, we'll render a modal with simple, hardcoded content inside.

Copy this, and replace it with, of course, `{% block content %}` and `{% endblock %}`.

In `base.html.twig`, paste the frame... and add a closing tag.

Let's keep going! The loading template down here? Yea, that's also something
*specific* to this *one* modal. If our modal is a hardcoded message, it
won't need this at all!

Copy the loading `div`, delete, then around the `<template>` add: if
`block('loading_template')`.

So *if* we pass the block, render it inside the `<template>`.

Back in `base.html.twig`, anywhere, define that block. But instead of the normal
`{% block %}` tag - which *would* work - when you're inside a Twig component,
you can use a special `<twig:block>` syntax with `name="loading_template"`.
Then, paste.

We just moved around a *lot* of stuff. And yet... the existing modal still works!
And now, we have a leaner, meaner modal component that we can *reuse* in other
places.

## Delete Modal with Custom Content

Let's do exactly that. I want to add a delete link on each row that, on click,
opens a modal with a confirmation. Open `_row.html.twig`. Copy the edit link,
paste, and call it delete.

To get this to work, one option is to create a new, standalone delete
confirmation page, point to that and... done! The `data-turbo-frame="modal"`
would load that page into the modal.

But since we've done that before, let's try something different. Delete the `href`,
change this to a `button`, remove the `data-turbo-frame` attribute... and change
the yellow colors to red.

Let's go check out the look. Nice!

Back over, I'll paste in a modal.

There's nothing special here. The big difference is, instead of a `<turbo-frame>`,
the content we need is *right* here. When we refresh, *every* row now has a delete
dialog inside of it. But that's totally okay! It's not open, so it's invisible.

## Opening the Modal

Now for the tricky part. When we click this button, we need to open this modal. To
make this happen, we need the button to physically live *inside* the
`data-controller="modal"` element so that it can call the `open` action on the modal
Stimulus controller.

To allow that, inside the modal template, add a new block called `trigger`,
`endblock`.

Now, if you have a button that triggers the modal to open, you can put that
right here! Over in `_row.html.twig`, copy the button, remove it, say
`<twig:block name="trigger">` and paste.

And because we're inside the modal, add `data-action="modal#open"`.

Let's try this! So excited! Refresh! The styling got weird. Before, we had
three `a` tags, which are inline elements. Now we have two inline elements and
a block element. So that *is* something that changes slightly, but it's an easy fix.
Up on the `<td>`, add a `flex` class.

## Modal Conditional Size & the props Tag

And now... much better. Most importantly, when we hit delete, the modal opens!
However, you know me, I want this to be *perfect*. And I'm not happy with how *big*
this is. When I open the edit form, it makes sense to be half the screen width. But
when I open the delete, we should let it shrink down to the size of the content
inside.

To do that, in this one case, I want to be pass a new flag called `allowSmallWidth`
set to `true`. I added this `:` because, if I pass `allowSmallWidth="true"`, that
will pass the *string* `true`. By adding a colon, this becomes Twig code, so that
will pass the *Boolean* `true`. They would both work... but I like being stricter.

With the `Button`, we learned that if you want this to become a *variable* instead
of an attribute, you can add a public property with that same name. And we *could*
create a new `Modal.php` file.

But there's another way to convert from an attribute into a variable when
using an anonymous component. At the top of `Modal.html.twig`, add a `props`
tag that's special to Twig components. Add `allowSmallWidth` and default it to
`false`.

Cool, huh? Below, we want to make this min-width conditional. Say
`{{ allowSmallWidth }}` - if that is true, render nothing, else render the
`md:min-w-[50%]`.

Back on the page, the edit link still opens with half width... but that delete link,
ah, it's nice and small! Now it deserves some real content! In `_row.html.twig`,
after the `<h3>`, I'll add some styling... then I want a cancel button that
closes the modal. For that, we can go old-school. Add a `<form method="dialog">`,
and inside a `<twig:Button>` that says Cancel. And I want the button to look like
a link, so add `variant="link"`.

That doesn't exist yet, so in the `Button` class, add it: `variant` and it just
needs `text-white`.

After the form, to render the delete button, include `voyage/_delete_form.html.twig`.

Oh, and that template has a built-in `confirm`. Delete that because we have something
*way* nicer now.

Moment of truth! Refresh and delete. It looks great! Cancel closes the modal...
and deleting *works*. And it shouldn't be a surprise that it works. The delete
form is *not* inside a `<turbo-frame>`. So when we click delete, that triggers
a normal form submit that redirects and causes a normal full page navigation.

## Hiding Search Options in a Modal

Ok, I know this is already a full day, but I *really* want to use the modal in
one more spot. And it's a cool use-case.

On the homepage, in my PHP & Symfony code, I won't show it, but I already added
logic to filter this list by the *planets*. I only didn't add any planet checkboxes
to the page because... we don't really have *space* for them.

So here's my idea: add a link here that opens a modal that *holds* the
extra filtering options.

Open up `main/homepage.html.twig` and find that input. Start by adding a
`<div class="w-1/3 flex">`... add the closing on the other side of the input...
then remove `w-1/3` *from* the input. We're making space for that link.

But I'll paste in a full modal. This will be invisible except for the trigger.
So we basically just added a button that says "options". But it's already set *up*
to open the modal. Inside, to start, we have an `h3` and a `<twig:Button>` that
closes the modal.

## Adding a Modal Close Button

But the result when I click options... is nice! Though, it needs a close
button on the upper right. We *could* add it to just this modal... but it might
be nice if it were an *option* in the modal component.

Let's do it! In `Modal.html.twig`, add one more prop called `closeButton` defaulting
to `false`. If that's true, at the end of the `dialog`, I'll paste
in a close button.

Again, nothing special here: some absolute styling, an icon... and the important
part: it calls `modal#close`.

In `homepage.html.twig` find that modal and add `closeButton="true"`... but
with the `:` like last time.

Let's check it out! I *love* that!

*Finally*, let's frost this cake. Near the bottom of the content, I'll paste in
the planet checkboxes. This is more boring code! I loop over the planets and
render input check boxes. My Symfony controller is already set up to read the
`planets` parameter and filter the query.

Final test. Open it up. Lovely! Now watch: click a few. When I press "See Results",
the table should update. Boom. It did!

But the coolest part is... *how* this worked! Think about it: I click this button...
and the table reloads. That means the form is submitting. But... what caused
that? Look at the button: there's no code to submit the form. So what's going on?

Remember: this `button`, the planet checkboxes and this modal physically live
*inside* the `<form>` element. And what happens when you press a button that lives
inside a form? It submits the form! We run the `modal#close`, but we also allow
the browser to do the default behavior: submitting the form. This is ancient
alien technology at work!

On the close button, I was a bit sneaky. When I added that, I included a
`type="button"`. That tells the browser to *not* submit any form that it might
be inside. That's why when we click "X", nothing updates. But when we click
"see results", the form submits.

Woh! Best day ever! Tomorrow, it's time to look at Live components, where
we take Twig components and let them re-render on the page via Ajax as the
user interacts when them.
