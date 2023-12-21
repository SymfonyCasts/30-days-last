# Turbo Stream Responses

For day 22, we've got a nice and quick one. We've learned that Turbo Streams are
custom HTML elements that you can throw onto the page anywhere and they execute.
But there's another way to use Streams that's actually more commonly documented,
even if I am using it a bit less lately.

In `VoyageController`, scroll up to find the `new()` action. Instead of redirecting,
like we normally do for a form submit, the other option is to return a response
that is *entirely* filled with Turbo streams.

## Returning a Response of Streams

Watch: remove this flash and return `$this->renderBlockView()`... except change
it to `renderBlock()`. That does the same thing, but returns a `Response` object
instead of a string. The last detail is `$Request->setRequestFormat()`
`TurboBundle::STREAM_FORMAT`.

That's a little technical but will set a `content-type` header on the response that
tells Turbo:

> Hey! This is not a normal full page response. I am *just* giving you Turbo Streams
> to process.

Let's check the result. Refresh, go to New Voyage... fill out the fields... and
Save. What happened? The modal is still open and no Toast notification. But if you
were watching closely, the row in the table *was* prepended!

And, in the network tools, find the POST request. Look at that! the response is
nothing more than the `<turbo-stream>`: that's the only thing that our app returned.

## Returning All the Streams Needed

The moral of the story is: because we're not redirecting to another page, we no
longer get the normal `<turbo-frame>` behavior where it finds the frame on the
redirected page and renders that. In our case, the empty `<turbo-frame>` was
closing our modal *and* rendering the flash messages.

Instead, when you decide to return a stream response, you are 100% responsible for
updating *everything* on the page. In other words, in `new.html.twig`, down here,
we need a couple more streams! Open `edit.html.twig` and steal the stream that closes
the modal. Pop that here.... then, from `_frameSuccessStreams.html.twig`, steal the
stream that appends to the flash container.

I think that's what we need! Give this another shot. Here's our toast notification
finally from the *previous* submit. Create a new voyage... save... and that's it!
Toast notification, modal closed, row prepended.

## Turbo Mercure

This idea of returning *just* a `<turbo-stream>` is similar to how the Turbo and
Mercure integration happens. If you don't know, Mercure is a way to get real-time
updates on your front end. And it pairs really well with Turbo. From inside your
controller, you publish an `Update` to Mercure... which will be sent to the frontends
of all browsers listening to this `chat` topic.

The contents of that `Update` are Turbo Streams. I'll scroll down to that template.
So we publish streams... those streams are sent to frontend via Mercure, it Turbo
processes them.

On the frontend, it might look like this. Suppose we edit a voyage, add a few
exclamation points and hit save. Of course, *our* page updates thanks to the normal
Turbo mechanisms we've talked about. But, if we were using Mercure, anyone *else*
on this page could be listening for updates and could receive a Stream update
that *also* says to prepend this row. So I add the exclamation points and *you*
suddenly *also* see them on your screen, without refreshing.

It's *very* powerful and powered via Streams.

Ok, even though this is working nicely, let's go back to our old way... which was
*also* working lovely. Undo the new Turbo Streams... and the code in the controller.

Tomorrow, we move on to one of my *favorite* parts of LAST Stack, and the key to
organizing your site into reusable chunks: Twig Components.
