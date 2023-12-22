# Fancy things on Modal Form Success

We have been busy. We've cooked up a reusable AJAX-powered modal system that I
*love*. Submitting with validation errors already works. And success? It's
nearly there. We when save... no toast notification, but the modal *did* close.

The reason it closed is important. In the `new()` action, we redirect to the
index page. That page extends the normal `base.html.twig`... so it *does* have a
`<turbo-frame id="modal">` on it... but it's this empty one. This means the
modal frame becomes empty, our modal Stimulus controller notices that then
closes it.

## Planning: When Forms are in Frames

In general, when you add a `<turbo-frame>` around something - like on the homepage
with our planets sidebar - you need to think about where the links inside point to.
We need to make sure each goes to a page that has a matching `<turbo-frame>`.

When a *form* lives inside a `<turbo-frame>`, we need to think about what happens
on *submit*. The error case is easy: it always renders the same page that has
the same frame with the errors inside. But on success, we need to think about where
the form redirects to and ask: does that page have a matching `<turbo-frame>` and
does it contain the right content?

In the case of this modal and the index page, it's perfect: there *is* a
matching frame, it's empty and the modal closes.

## Rendering Success Flashes with a Turbo Streams

Ok, back to the missing toast notification! This is a situation where we need
to update the `<turbo-frame>` - to empty it - and we *also* need to update another
area on the page: we need to render the success flash messages into the flash
container.

This is a super common need when a form submits inside a `<turbo-frame>`. So we're
going to solve this, I think, in a cool and global way. When we redirect on success,
this `<turbo-frame>` is ultimately loaded on the page, which causes the modal to
close. Inside it, add a `<turbo-stream>` with `action="append"` and
`target="flash-container"`:

[[[ code('8437d2a124') ]]]

When we added the toast system, we added an element with `id="flash-container`:

[[[ code('81c173a849') ]]]

We didn't need that then, but now it's going to come in handy because we can
target that to add flash messages into it. 

Inside the stream, add the `template` tag, of course, then
`{{ include('_flashes.html.twig') }}`:

[[[ code('cbdb9032b9') ]]]

This will render the flash messages... and the stream will append them into that
container.

Let's try it! Fill out a new voyage, submit and... absolutely nothing happens.
The problem... is subtle. When we redirect to the index page, Symfony renders that
entire page... even though Turbo will only use the `<turbo-frame id="modal">`.
This means that, right before we render this code, our flash container renders the
flash messages... which *removes* them from the flash system. So the flashes
messages *are* in the HTML that we return from the Ajax call... but because they're
not inside the `<turbo-frame>`, they don't make it onto the page.

The fix is easy: make sure your flash container is *after* the modal:

[[[ code('3984d10144') ]]]

Give this a go. Refresh... and fill in the form. Got it! The Modal closes, then
the `<turbo-stream>` triggers the toast!

And this is really neat! When we redirect, the `<turbo-frame>` is now *not* empty:
it contains the flash `<turbo-stream>`. But remember: as soon as a `<turbo-stream>`
activates, it executes itself and then disappears. Once that happens, the
`<turbo-frame>` becomes empty and the modal closes. I really dig that.

## Stream Extras: Prepending the Table

What I love about the modal system is that it works... and we haven't needed
to make any changes to our controller. But now, we get to think about any optional
*extra* behavior that we might want.

For example, could we prepend the table with the new voyage? Because, right now we
don't see it until after we refresh. Let's try!

In `index.html.twig`, find the `table`. We need to prepend into the `tbody`.
To target this, on the `table`, add an `id="voyage-list"`:

[[[ code('6a8ff202e6') ]]]

Let's think: this is another case where we need to update something that lives
outside the `<turbo-frame>`. So, we need a stream.

Open `new.html.twig` and after the `body` block, add a new block called `stream_success`,
then `endblock`. Inside, we'll add any Turbo streams we need to make the submit
*really* shine. Add a `<turbo-stream>` `action="prepend"` then `targets=""`. The
"s" on targets means we can use a CSS selector: `#voyage-list tbody`. Add the
`<template>` element... and, for now, a `<tr><td>` `{{ voyage.purpose }}`:

[[[ code('f1388b0cca') ]]]

Ok, so we have a new block in our template... that nobody is using. Somehow,
we need to grab this Turbo stream... and, after the redirect, render it on the
*next* page in the modal `<turbo-frame>`.

How do we do that? We have two options - and I'll show the second on Day 24. But
here's the system I like.

First, we only need to worry about prepending the table row when we're submitting
inside a `<turbo-frame>`. If we went to the new voyage page directly - which
doesn't have a frame - and submitted, we wouldn't need any Turbo Stream stuff. This
would navigate the full page and render normally. Nice & simple.

So, in the controller, start with if `$request->headers->has('turbo-frame')`. So
if this form submit is happening inside a `<turbo-frame>`, then we want to
use our stream. Render that block with `$stream` equals then a relatively new
controller method: `$this->renderBlockView()` passing `voyage/new.html.twig`.
Instead of rendering the entire template, to render a single block pass this,
you guessed it, `stream_success`. Actually... I think I'm missing an "s". I am!
Better.

Pass the template a `voyage` variable.

To pass the `<turbo-stream>` string to the next page add it to a new flash called
`stream`:

[[[ code('112be81da0') ]]]

Finally, when we redirect to the index page and this `<turbo-frame>` is
rendered, *output* that flash: `for stream in app.flashes('stream')`, `endfor`
with `{{ stream|raw }}` so it renders the raw HTML elements:

[[[ code('b4d4f9f925') ]]]

I think we're ready! Refresh... add a new voyage and... that's incredible! 
The Ajax call redirected to the index page, where the modal frame had 2 Turbo
streams: one to render the toast and the other to prepend the table.

## Prepending with Real Content

Last step, prepend the real content. What we want is this `tr`. To get that
from inside of `new.html.twig`, we need to isolate it into its own template. Copy
that, delete it, then include `voyage/_row.html.twig`:

[[[ code('c6c1eeea3a') ]]]

Go create that template... then paste:

[[[ code('6458dbd91e') ]]]

Easy.

Copy the `include()` statement and, in `new.html.twig`, use that for the stream:

[[[ code('cd87bdbe3b') ]]]

Let's try this! Create another voyage and... beautiful! Modal closes, toast notification
renders & the page updates. It's everything we want.

Tomorrow we're going to put our new modal system to the test by opening the edit
link inside a modal. I promised it would be reusable, and tomorrow we'll prove it...
with a few curve balls to make it more realistic.
