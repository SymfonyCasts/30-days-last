# More with fun Modals! Editing & Deleting

Welcome to day 23 - the grand finale in our modal system saga. Though, we will
revisit it in a few days when we talk about Twig components.

So if our new modal system is as reusable as I've promised, we should be able to
easily open the edit form in a modal too, right?

## Opening the Edit Form in a Modal

To opt into the modal system, the only thing we need to change - in
`edit.html.twig` - is to extend `modalBase.html.twig`. And while we're here,
take out the extra padding with `modal:m-0` and `modal:p-0`.

Next, make the edit link *target* the `modal` frame. This lives in `_row.html.twig`.
I'll break this onto multiple lines.... then add `data-turbo-frame="modal"`.

Moment of truth. Refresh. And... darn it! It just works! Even if we save successfully,
*that* works. We get the toast, the modal closes, my goodness!

This works because, in `VoyageController`, the `edit` action, like `new`, redirects
to the `index` page. That has an empty modal frame, so the modal closes.

## When the Modal Doesn't Close

But... I want to be tricky. The edit form now appears in two contexts, the modal,
but also on its standalone page. What if, when we're on this page, on
success, we want to redirect right back here so we can keep editing.

Easy! Change the route to `app_voyage_edit` and set `id` to `$voyage->getId()`.

Cool. Now when we save, it works! But... how did that affect the form in the
modal? When we edit and save... nothing happens. The modal is still here and
no toast notification.

## Rendering the "Frame Streams" in all Frames

Let's work on the missing toast notification first. In `base.html.twig`,
inside of the `modal` frame, we render the flash messages in a `<turbo-stream>`.
The problem is... when we redirect to the edit page, because it extends
`modalBase.html.twig`, the frame that's returned is *this* one. And this
`<turbo-frame>` does *not* render these streams.

It turns out, these lines should really live inside *any* `<turbo-frame>` that
might be rendered after a form submit.

To help with that, copy this and, inside the `templates/` directory, create a new
file called `_frameSuccessStream.html.twig`. Paste inside. But before we use
this, I want to add one other detail: if
`app.request.headers.get('turbo-frame')` equals a new `frame` variable, then
render this, else, do nothing.

I'm coding for an edge-case, so let me explain. Imagine we have *two*
`<turbo-frame>` elements on the same page: `id="login"` and
`id="registration"`. And they both include this partial. In this case,
the `<turbo-frame id="login">` would always render the flash messages...
leaving nothing for the poor `registration` frame. And so, when we *are* submitting
inside the `registration` Turbo Frame... we wouldn't see the toast notifications.

To fix this, when we use this partial - `include('_frameSuccessStream.html.twig')` -
pass the name of the frame you're inside: `modal`. That way, if the current
frame is something *else*, this won't eat the flash messages.

Copy this, and in `modalFrame.html.twig`, paste that here too.

Let's do this! Refresh, Edit... and save. The modal still stays open, but look back
there: we see the toast!

## Closing the Modal when it wants to stay open

Now: how can we close this pesky modal. When we put a form inside a frame, our Symfony
controller might not need to change. Flash messages will work and, depending on where
you redirect, the modal might even close.

But you *do* need to ask yourself: where are all the places my form will be used?
And: am I returning the right response for each situation? Right now, in the modal
situation, our response *isn't* what we want: it *doesn't* cause the modal to close.

And that's okay! Remember: in addition to letting the Turbo frame update with the
content after the redirect, we can *also* use streams to do anything extra.

In `new.html.twig`, steal the `stream_success` from the bottom. In `edit.html.twig`,
paste. This time, we want to update the `<turbo-frame id="modal">` element to *empty*
its content so the modal will close. Do that with `action="update"`,
`target="modal"`, and set the `<template>` to nothing.

In the controller, to add the "extra stuff", copy the if statement from
`new`... paste it down here, change the template to `edit.html.twig` and... we should
be good!

Ok, hit "Edit" and save. Hmm, I saw the toast, but the modal didn't close. Let me
look at the stream to make sure I have everything. Ah! With `targets`, you use
a CSS selector. But with `target`, it's just the id. So the Turbo Stream was
executing... but wasn't matching anything.

Let's try that again. When we hit save, that will redirect back to the edit page,
and that *is* going have a `<turbo-frame id="modal">` *with* content:
it *won't* be empty. But then, our *stream* should empty it and the modal should
close.

And... gorgeous!

## Updating the Row in Edit

Can I add one last polishing detail to edit? It would be *so* cool if, when
we change a voyage, it updated the row instantly. This is another "extra",
and... it's going to be easy.

First, to target this, in `_row.html.twig`, add an `id`:
`voyage-list-item-` `{{ voyage.id }}`.

Copy that, head over to `edit.html.twig` and add one more Turbo Stream:
`action="replace"` and `target="voyage-list-item-"` `voyage.id`. Add the
`<template>` and then include `voyage/_row.html.twig`.

This is where things *really* start to shine. Edit, remove those exclamation
points and... the page updates instantly. Our edit modal - even with all the
complications I threw in - is done!

## Handling Delete

With our last 3 minutes, let's make sure the "delete" button is working. Oh...
it is! The modal closes and the toast renders! Like the other actions, after
deleting, it redirects to the `index` page and the empty `modal` frame
closes the modal. It's brilliant!

Except... that the row I deleted is *still* there until we refresh.

But hold up. The delete button is a form that submits. And the only reason this
submits into a `<turbo-frame>` is because it happens to live inside the modal frame.

But the delete action doesn't *need* to submit into a frame. We're never going
to click "Delete" then want to *show* something in the modal. A full page
navigation would be *fine*.

To do that, in `_delete_form.html.twig`, on the frame, add `target="_top"`.

Now, edit, delete, and... the redirect causes a full page navigation, which
is *fine*.

## Extra-Fancy Delete

Though, yes, it *could* be smoother. Scroll down a bit...
and delete one. The page scrolls back to the top.

Like with anything, if this is important to us, we can improve it. Remove
the `target="_top"`.

When a form - even our delete form - exists inside a `<turbo-frame>`, we need to
ask: where is this being used and what do I need to update to make the page
*perfect* after success? In this case, we need to remove the row. So we need to do
something *extra*, outside the frame. And we know how to do that!

In `edit.html.twig`, steal the `stream_success` block. Then create a new template
called `delete.html.twig`. Delete doesn't normally have its own template... and we're
going to use this just for the `stream_success`. Use this one,
change `action` to `remove` and `target` `voyage-list-item-` but just
use an `id` variable. And for remove, we don't need the `<template>` at all.

In `VoyageController`, scroll up, steal the if statement.... and down in delete,
paste that. Change the template to `delete.html.twig` and pass an `id` variable
set to `$id`. We can't use `$voyage->getId()` because it'll already be empty since
we deleted it. Instead, pass `$id`... and before we delete, set that:
`$id = $voyage->getId()`.

Let's do this! Scroll way down here and delete ID 22. Watch. Boom. The row
is gone, we get the toast notification and the page doesn't budge.

Ok, the last few days have been... wow. Tomorrow, we're going to take it easier
and learn one other way we can use Turbo Streams. See you then!
