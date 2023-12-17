# Fantastic Modal UX with a Loading State

Let's pick up where we left off yesterday. The Ajax-powered modal loads!
Try to submit it. Uh oh - something went wrong. It went to some page that didn't
have a `<turbo-frame id="modal">`... which is odd, because *every* page now has one.
That's because... the response was an error. If we look down on the web debug toolbar,
there was a 405 status code. Open that up. Interesting:

> no route found for POST /voyage

That's weird because we're submitting the new voyage form... so the URL should
be `/voyage/new`.

## Adding action Attributes to the Forms

Here's the problem: when I generated the voyage crud from MakerBundle, it created
forms that *don't* have an `action` attribute. That's fine when the form lives
on `/voyage/new` because no `action` means it submits back to the current URL. But
as soon as we decide to embed our forms on other pages, we need to be responsible
and always set the `action` attribute.

To do that, open up `src/Controller/VoyageController.php`. At the bottom, I'll
paste in a simple private method. Hit Okay to add the `use` statement. We can pass
a voyage or not... and this creates the form but sets the `action`. If the voyage
has an id, it sets the action to the edit page, else it sets it to the new page.

Thanks to this, up in the `new` action, we can say `this->createVoyageForm($voyage)`.
Copy that... because we need the exact line down in `edit`.

Lovely. Back over, we don't even need to refresh. Open the modal, save and...
that is *absolutely* lovely! It submitted and we got the response *right* back inside
the modal. Because... of course! That's the whole point of a Turbo frame. It keeps
the navigation inside itself.

## Loading the Modal Instantly

Before we talk about what happens on success, I want to *perfect* this. My second
requirement for opening the modal was that it needs to open immediately. Over
in the `new` action, add a `sleep(2)`... to pretend our site is getting slammed
by aliens planning their spring break trips.

When we click the button now... nothing happens. No user feedback at *all* until
the Ajax request finishes. That is *not* good enough. Instead, I want the modal to
open immediately with a loading animation.

Over in the modal controller, add a new target called `loadingContent`. Here's my
idea: if you want some loading content, you'll define what that looks like in
Twig and set this target on it. We'll do that in a moment.

At the bottom, create a new method called `showLoading`. If `this.dialogTarget.open`,
so if the dialog is already open, we don't need to show the loading, so return.
Otherwise, say `this.dynamicContentTarget` - for us, that's the `<turbo-frame>`
that the Ajax content will eventually be loaded into - `.innerHTML` equals
`this.loadingContentTarget.innerHTML`.

Finally, add that target. In `base.html.twig`, after the `dialog`, I'll add a
`template` element. Yes, my beloved `template` element: it's perfect for this
situation because anything inside won't be visible or active on the page. It's
a template we can steal from. Add a `data-modal-target="loadingContent"`. I'll
paste some content inside.

Nothing special here: just some Tailwind classes with a cool pulse animation.

If we try this now... no loading content! That's because nothing is calling the
new `showLoading()` method. Over in `base.html.twig`, find the frame. I'll break
this onto multiple lines. Let's think: as soon as the `turbo-frame` starts loading,
we want to call `showLoading()`. Fortunately, Turbo dispatches an event when it
starts an AJAX request. And we can listen to that.

Add a `data-action` to listen to `turbo:before-fetch-request` - that's the name of
the event - then `->modal#showLoading`.

All right, let's check out the effect! Refresh the page and... oh, it's wonderful!
It opens instantly, we see that loading content... and it's replaced when the
frame finishes!

I love how this works. When this calls `showLoading()`, that method puts content
into `dynamicContentTarget`. And... do you remember what happens the moment any
HTML goes into that? Our controller notices it, and opens the dialog. That's
some great teamwork!

## Loading Indication on Form Submit

We're nearly there to making this perfect, but I'm not satisfied! While we still
have the `sleep`, submit the form. Nothing happens! There's no feedback while that's
loading.

Lucky for us, we've been down this road before with a different Turbo frame. Add class
`aria-busy:opacity-50`, and `transition-opacity`.

I'll reload... click, loading animation and submit. Yes! The low opacity tells
us that something is happening.

And with that, I will happily remove our `sleep`.

## Conditional Modal Styling

Ok, one final detail that I want to get right: this extra padding. This exists
because the content from the `new` page has an element with `m-4` and `p-4`. So
the modal has some padding... and then extra padding comes from that page.

*On* the page, the margin and padding make sense. It comes from over here in
`new.html.twig`. So we *do* want this on the full page... but not in the modal.

To help us do this, we're going to use a Tailwind trick. In `tailwind.config.js`,
add one more variant. Call this `modal`, and activate it whenever we are inside
a `dialog` element.

Now, in `new.html.twig`, keep the margin and padding for the normal situation.
But if we're in a modal, use `modal:m-0`, and `modal:p-0`.

Back on the new page, this shouldn't change. Looks good! But in the modal...
*that* is what we want.

Our modal system now opens instantly, AJAX-loads content, we can submit
it and even closes itself on success! Watch: fill in a purpose, select a planet...
and... the modal closed!

How? It's cool! The `new` action redirects to the index page. And because
`index.html.twig` extends the normal `base.html.twig`, it *does* have a
`modal` frame... but it's that empty one at the bottom. That causes the
`turbo-frame` on the *page* to become empty. And thanks to our modal controller,
we notice that and close the dialog.

The only thing we're missing now, if you were watching closely, is the toast
notification! Tomorrow, we'll talk all about handling success when a form is
submitted inside a frame... *including* doing cool things like
automatically adding the new row to the table on this page. See ya tomorrow.
