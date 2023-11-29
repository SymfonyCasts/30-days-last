# Toast Notifications

An important part of any functional beautiful site is a notification system. In
Symfony, we often think of flash messages: messages that we render near the
top of the page, for example, after the user submits a form. And yes, that *is*
what I'm talking about. But just rendering them at the top of the page isn't good
enough for us. Instead, I want to render them as rich, toast-style notifications
in the upper right that disappear automatically with CSS transitions and can tie
my shoes for me.

## Rendering Flash Messages

On our CRUD controllers, I'm already setting a `success` flash message... but I'm
not rendering it anywhere. In the `templates/` directory, create a new `_flashes.html.twig`.
To start, just loop over the success messages with `for message in`
`app.flashes.success`... and `endfor`.

Inside, I'll paste a very simple flash message, which will start fixed to the bottom
of the page.

Next, in `base.html.twig`, instead of rendering the flashes somewhere near the top
of the body, put them at the bottom. Say `<div id="flash-container">` then
`{{ include('_flashes.html.twig') }}`.

The `id="flash-container"` isn't important yet, but it *will* be useful later when
we talk about Turbo streams.

So let's see if this works! Hit "Save" and... there we go! It's in a weird spot,
but it shows up.

## Making the Notification Pretty!

To make this look nicer, let's take a trip to Flowbite. Search for "toast". Ah,
this has some great examples for different styles of toast notifications. This
has me feeling dangerous!

Back in `_flashes.html.twig`, I'll paste in some content. This is heavily inspired
by the Flowbite examples. But nothing really changed: we're still looping over the
same collection and still dumping out the message. We've just got nice markup around
this.

I can't want to see this! I'll go to edit and "Save". Oh, that is wonderful!
In the upper right where I want it and all done with CSS.

## Making the Toast Closeable

Though, it doesn't auto close yet. Heck, it doesn't close at all! Since "closing"
things will be a common problem, let's create a reusable Stimulus controller that
can do that.

In `assets/controller/`, add a new `closeable_controller.js`. I'll cheat and
grab the code from another controller... clear it out... then add a `close()` method.
When this is called, it'll remove the entire element that the controller is attached
to.

To use this, in `_flashes.html.twig`, attach the controller to the top level element
because that's what should be removed on close. Then, down on the button, say
`data-action="closeable#close"`.

We don't need the `click` because this is a `button`, so Stimulus already knows that
we want this to trigger on the `click` event.

Let's try it! Hit edit and Save. It's there... it's gone.

In just a few minutes of work, we created a beautiful and functional toast
notification system! But, darn it, this is *not* cool enough for our 30 Days of
LAST Stack mission! So tomorrow, we'll fancy-ify this with auto-close, CSS
transitions and an animated timer bar.
