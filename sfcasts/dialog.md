# HTML dialog for Modals

Welcome to day 19. Today we have the luck to play around with a little-known HTML
element that absolutely *rocks* when it comes to building modals. The `<dialog>`
element. Honestly, you can probably skip to the end and copy the final markup
and Stimulus controller if you just want a fully-featured, modal system. But I
also promise that today's journey is going to be *fun*.

Open up `templates/voyage/index.html.twig`. For the `h1`, I'm going to paste some
new content... which adds a button to create a "New voyage".

At the bottom, I'll remove this old button. There's nothing special with this new
code: it's just... a button. And when we go to the right page... there it is! It
doesn't even do anything yet.

## Hello `<dialog>`

Back in the template, right after the button, add a `<dialog>` element. Inside I'll
proclaim "I am a dialog". Then also add an `open` attribute.

When we refresh, we see the dialog... and it's... interesting. The `dialog` is
absolutely positioned on the page and even centered horizontally and near, but
not *at* the top vertically. That's because the `<dialog>` element is *designed*
for modals... or really any dialog, like a dismissable alert or any sub window.
It's a normal HTML element, but with a bunch of superpowers that we're going to
experience.

## Making a Pretty dialog

But first, let's make it prettier. Back in the template, I'll paste over that dialog.
This is adapted from Flowbite with some help from an AI. And of course, a designer
could create this no problem. But, there's nothing special: we still have a
`dialog`, it's still `open`... and even the Tailwind classes aren't anything
special. I set a width... and rounded some corners. But things like the absolute
positioning are already built into the element. Most of what we see here is
some hardcoded content to get us started.

The result... is *awesome*. However, the close button doesn't do its job yet!
No worries: this is a *great* opportunity to show off one of the dialog's
superpowers!

Find the close button. Around this, add a `<form method="dialog">`.

This is a normal button. The `<form method="dialog">` is the key.

Now when you click X... it *closes* away.

## Opening with a modal Stimulus Controller

But to *really* make the `<dialog>` element shine, we need a bit of JavaScript. 
Head up to `assets/controllers/` and create a new file called `modal_controller.js`.
I'll cheat, steal some content from another controller... and clear it out. This
controller will be really simple. Start by adding a `static targets ['dialog']`
so we can quickly find the `<dialog>` element from inside here. Next add an
`open` method. Here, say `this.dialogTarget.show()`.

This is another superpower of the `<dialog>` element: it has a `show()` method.
Built *into* the `<dialog>` element is this core idea of showing and hiding.

To use our new controller, over in `index.html.twig`, find the `div` that holds
the `button` and the `dialog` and add `data-controller="modal"`. Then, on the
button, say `data-action="modal#open"`. Finally, we need to set the `<dialog>`
as a target. Remove the `open` attribute so it starts closed and replace it with
`data-modal-target="dialog"`.

I like it! Over here, it starts closed. And when we click, open! Close, open, close.

## Opening as a Modal

Now, a `<dialog>` element has two *modes*: the normal mode that we've been using
and a *modal* mode... which is much more useful. To use the modal mode, instead
of show, it's just `showModal`.

Now when we click, it still opens, but there are some subtle differences. The first
is that we can close it by hitting Esc. Cool. The second is that there's an backdrop.
Watch: when I click this, the screen will get just a little bit darker. Did you see
that? This also *blocks* us from interacting with the rest of the page. And we get
that for *free* thanks to `<dialog>`. That's *huge*.

## Styling the Backdrop

If you inspect and find the `<dialog>` element, there it is, is it has a pseudo
element called `backdrop`. So it takes care of adding that for us... but it's
a *real* element that can *style*. And I do want to style it!

Back in the template, find the `dialog` element. Thanks to Tailwind, we can
style that backdrop pseudo element directly. Add `backdrop:bg-slate-600` and
`backdrop:opacity-80`.

Watch the effect. That is starting to feel really, really good.

## Removing Background Page Scroll

One thing the `dialog` element doesn't handle automatically is... the page in
the background still scrolls. It doesn't hurt anything... but it's not the behavior
we expect.

To fix this, over in the `open()` method, `document.body` to get the body
element, `.classlist.add('overflow-hidden')`.

And now... that's what we want!

## Cleaning up on Close

Though... if we close, I still can't scroll! We need to *remove* that class.

To do that, copy the `open()` method and name it `close()`. To close the dialog,
call a `close()`... then remove the `overflow-hidden`.

I like it! There's just one tiny problem: we're not *calling* the `close()` method!
If we hit X or press Esc, the dialog is closing, yes, but I still can't scroll because
nothing calls this `close` method on our controller.

Fortunately, the `dialog` element has our back. Whenever a `dialog` element closes -
for any reason - it dispatches an event called `close`. We can listen to that.

On the `<dialog>` element, add a `data-action` set to `close->modal#close`.

So no matter *how* the `dialog` closes - I'll press Escape - we can now scroll because
the `close()` method on our controller *was* called.

## Blurring the Background

Ok, I'm excited. What else can we do? How about blurring the background? That would
be cool. You might try to do this by blurring the backdrop. I tried that... but
couldn't get it to work. What we *can* do is blur the body. Add one more class:
`blur-sm` and remove the `blur-sm` in `close()`.

Let's see how this look. That is *really* cool!

## Close on Click Outside

But if I try to click outside of the modal, it doesn't close. That's another thing
the `dialog` element doesn't handle. Fortunately, there's a quick one-time fix.

Up on the root element of our controller. Actually, we can put it down here in
the `dialog`. Add a new action: `click->modal#clickOutside`.

I bet that looks odd... it'll be called whenever we click *anywhere* in the dialog,
so let's go write that method. Say `clickOutside()`, give it an `event` argument,
Then if `event.target ==== this.dialogTarget`, `this.dialogTarget.close`.

So `event.target` will be the *actual* element that received the click. And it turns
out, the only way to click *exactly* on the `dialog` element itself is if you click
the backdrop. If you click anywhere else inside, `event.target` will be one of
these elements. So it's a clever three lines of code, but the result is perfect.
Click in here, no problem. Click out there, closed.

## CSS Animation to Fade In

At this point, I am *happy*. But this tutorial isn't about making good things,
it's about making *great* things. Next up: I want the `dialog` element to fade
in. We *could* do this with a CSS transition. But another option is a CSS
animation. I know, transitions, animations - CSS has a lot.

An animation is something you apply to an element and it'll just... *do* that
animation forever, or you can make it animate just once. Like, we can make this button
animate up and down forever. One of the nice things about animations is that you
can make an animation only happen once... and it won't start until the element
becomes *visible* on the page... like an animation from opacity 0 to opacity 100
as soon as our `dialog` becomes visible.

Tailwind *does* have some built-in animations, but not one for fading in, so we're
going to add it. Down in `tailwind.config.js`, I'll paste over this `theme`.
This is mostly CSS animation stuff: it adds a new one `fade-in` that will go from
opacity zero to 100 in 1/2 a second.

To use this, find the `dialog` element and add `animate-fade-in`.

Try it out. Gorgeous! Could we fade out? Sure, but I actually like that it closes
immediately, so I'm going to skip that.

## Modals & Turbo Page Cache

Ok, I have just *one* last detail before I let you go for the day. When we added
view transitions, in `app.js`, we disabled a feature in Turbo called page cache...
because it apparently doesn't always play nicely with view transitions. When
view transitions come standard in Turbo 8, I'm sure this won't be a problem.

Anyway, when caching is enabled, the moment you click off of a page, Turbo takes
a snapshot of the page before navigating away. When when we click back, it's instant:
boom! Instead of making a network request, it uses the cached version of this page.
There's more to it than that, but you get the idea.

With caching enabled, one thing we need to worry about is removing any temporary
elements from the page *before* the snapshot is taken, like toast messages or modals.
Because, when you click "Back", you don't want a toast notification to be sitting
up here.

The way that we normally solve this, for example in `_flashes.html.twig`, is to
add a `data-turbo-temporary` attribute. That tells Turbo to remove this element before
it takes the snapshot.

Let's try adding this to our `dialog` so it's not on the snapshot. To see what
happens, open the modal and click back. That just snapshotted our page with the
modal. Now click forward. Woh. We're in a strange state. It looks like the dialog
is gone... but we can't scroll and the page is blurred.

That's because we need to do *more* than just hide the `dialog`: we need to remove
these classes from the body. Basically, before Turbo takes the snapshot, we need
something to call the `close()` method!

And we can do that! `in index.html.twig`, on the root controller element - though
this could go anywhere - add a `data-action=""`. Right before Turbo takes its
snapshot, it dispatches an event called `turbo:before-cache`. We can listen to that
and then call `modal#close`. The only detail is that the `turbo:before-cache`
isn't dispatched on any specific element. So listening to it on *this* element
won't work. It's dispatched on the window: it's a global event.

Fortunately, Turbo gives us a simple way to listen to global events by adding
`@window`.

It's a little technical, but it's a one-time fix. Now we can open the modal, go back,
forward, and the page looks beautiful.

Wow! Today was a huge day, but look what we accomplished! A beautiful modal system
that we have total control over. Tomorrow is going to be just as big because as
we bring this modal to life with real dynamic content and forms. See you then.
