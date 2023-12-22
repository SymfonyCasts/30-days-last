# Turbo Stream Responses

For day 24, strap in for a quick adventure. We've learned that Turbo Streams are
custom HTML elements that you can throw onto the page *anywhere*... and they execute!
But there's another way to use Streams that's actually more commonly-documented,
even if I'm using it a bit less lately.

In `VoyageController`, scroll up to find the `new()` action. Instead of redirecting,
like we normally do for a form submit, the other option is to return a response
that is *entirely* filled with Turbo streams.

## Returning a Response of Streams

Watch: remove the flash and *return* `$this->renderBlockView()`... except change
it to `renderBlock()`. That does the same thing, but returns a `Response` object
instead of a string. The last detail is `$request->setRequestFormat()`
`TurboBundle::STREAM_FORMAT`:

[[[ code('d8dd6bfcaa') ]]]

It's a bit techy, but this will set a `Content-Type` header on the response that
tells Turbo:

> Hey! This is not a normal full page response. I'm returning *just* a set of
> Turbo Streams that I want you to process.

Drumroll, please. Refresh, go to New Voyage... fill out the fields... and
save. What happened? The modal is still open and no Toast notification. But if you
were watching closely, the row in the table *did* prepend!

In the network tools, find the POST request. Look at that! The response is
nothing more than the `<turbo-stream>`: that's the only thing our app returned.

## Returning All the Streams Needed

The takeaway is: because we're not redirecting to another page, we no
longer get the normal `<turbo-frame>` behavior where it finds the frame on the
next page and renders that. In our case, the empty `<turbo-frame>` is what
closed the modal *and* rendered the flash messages.

When you decide to return a stream response, you are 100% responsible for
updating *everything* on the page. So, in `new.html.twig`, down here,
we need a couple more streams! Open `edit.html.twig` and steal the one that closes
the modal. Pop that here.... then, from `_frameSuccessStreams.html.twig`, steal the
stream that appends to the flash container:

[[[ code('79afcfa8f1') ]]]

I think that's all we need! Give this another shot. Here's our toast notification
finally from the *previous* submit. Create a new voyage... and ... save. That's it!
Toast notification, modal closed, row prepended.

## Turbo Mercure

This idea of returning *just* a `<turbo-stream>` is similar to how the Turbo and
Mercure integration works. If you don't know, Mercure is a tool that allows you
to get real-time updates on your front end... kind of like web sockets, but cooler.
And Mercure pairs really well with Turbo. From inside your controller, you publish
an `Update` to Mercure... which will be sent to the frontends of every browser
that's listening to this `chat` topic.

The content of that `Update` is a set of Turbo Streams. I'll scroll down to that template.
So we publish streams... those streams are sent to frontend via Mercure, and Turbo
processes them.

On the frontend, it might look like this. We edit a voyage, add a few
exclamation points and hit save. Of course, *our* page updates thanks to the normal
Turbo mechanisms we've talked about. But, if we were using Mercure, we could make
it so that anyone *else* on this page could receive a Stream update that *also*
says to prepend this row. So I add the exclamation points, and *you* suddenly
*also* see them on your screen, without refreshing.

It's *super* cool and powered via Streams.

Ok, even though this is working nicely, let's go back to our old way... which was
*also* working nicely. Remove the new Turbo Streams... and undo the code in the
controller.

Tomorrow, we move on to one of my *favorite* parts of LAST Stack - and the key to
organizing your site into reusable chunks: Twig Components.
