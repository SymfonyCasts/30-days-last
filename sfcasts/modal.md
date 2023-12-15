# AJAX Modal!

Yesterday we built a killer modal system thanks to the `dialog` element. With just
this markup and a small Stimulus controller, I'm feeling dangerous.

So let me tell you about today's goal, which is big and bold! When I click
"New Voyage", I want to AJAX-load the "new modal form" and pop it into the modal.
But more than that! When I submit the form, validation errors should stay in the
modal, it should close on success & we should see toast notifications. *And*,
maybe most importantly, I want this entire system to be reusable so that we can
quickly load *any* form on our entire site in a modal. We're going to do it, or
die trying. Hopefully we'll do it.

## Adding a modal Frame to the Layout

To get this going, copy the entire modal markup. There we go. Then go into
`base.html.twig` and, all the way at the bottom, before the closing `body` tag,
paste. Back in `index.html.twig`, remove the `dialog`... and we don't need the modal
controller stuff anymore.

This is now a normal `h1` and a normal button... that doesn't do anything. In a
`base.html.twig`, do the opposite: remove the `button`, the `h1` and the class on
the div.

It's now a div that contains a `dialog` that's closed.

Now for the magic touch: remove the guts of the `dialog`: only keep these two
divs, they help give us padding and nice scroll behavior. Inside, add a
`<turbo-frame>` with `id="modal"`.

That was a power move people. On every page, we now have a `<turbo-frame id="modal">`
that we an dynamically load content into. *And*, it lives inside of a dialog!

## Loading Content into the modal Frame

Watch: on the index page, change the new voyage button an `a` tag and set its
`href` to the  `app_voyage_new` route. It's a normal tag that would take us to the
new page. But now add `data-turbo-frame="modal"`.

Check it out. Refresh and click. Instead of changing the page, it loaded the
content into the `modal` frame. But... nothing happened.

Ok, it *did* make an AJAX call to the voyage new page. But if we open that up
in a new tab, there's no `modal` frame on this page. Well, actually there *is*.
Like every other page, at the bottom, it has an empty `modal` frame. So when
we click that link, it *does* work... but the result is that the Turbo frame stays
empty. Not super helpful.

To fix, in `new.html.twig`, add a `<turbo-frame id="modal">` around everything...
with a closing tag at the bottom.

Check this out. When we click now, yes! Inside the `<turbo-frame>`, we have the
form! The modal isn't opening yet, but it's *ready*.

## Adding the modal Base Layout

Now, before we figure out how to open the modal, we have a problem... and an
opportunity. If we went directly to the new voyage page, we would have *two*
`<turbo-frame id="modal">` elements on the page: the one around the form, and the
empty one on the bottom. That's... kind of invalid.

Also, even though I want the ability to load this form inside the modal, I *also*
want it to behave like *normal* if we go to the page directly. Watch: right now,
if I fill this out successfully and save, weird things happen! I submitted that into
a `<turbo-frame id="modal">`... it redirected to the index page... which has that
matching frame... which is empty.

That's not what I want: if I go to this page directly, I want it to act like normal.

We're going to handle this with a trick. In `new.html.twig`, remove the `<turbo-frame>`...
and extend a new base template called `modalBase.html.twig`.

Ooh. Copy that name and in the `templates/` directory, create it: `modalBase.html.twig`.
This will have one line: an extends tag that's dynamic. If
`app.request.headers.get('turbo-frame)` equals `modal` - so if an AJAX request is
being made to this page from the `modal` turbo frame, then extend a new
`modalFrame.html.twig`. Else, extend the normal `base.html.twig`.

So if we go to the page like normal, it will extend `base.html.twig`. There's
no turbo frame on this, it's completely normal and it will submit like normal.

But now, let's create that other base template. Copy its name and, in `templates/`,
create `modalFrame.html.twig`. All this needs is a `<turbo-frame id="modal">`...
with `{% block body %}` `{% endblock %}` inside.

So if we make a request to this page from the `modal` frame, the *entire* response
will be this single frame with the page's content inside.
*That* solves our problem. And it means that if we want a page to be able to load
its form inside a modal... the only line we're going to need to change is right here.
I'll prove that on Day 22.

## Auto-Opening the Modal

But right now, we're back to the situation where we click this link and... if I 
dig into the modal elements, it *is* loading the form into the `turbo-frame`...
but the modal isn't opening. How can we do that?

Well, I have 2 requirements for opening the modal. The first is that I want it
to be super easy to open. If HTML appears inside of this `turbo-frame` - no matter
*how* it's added - I want the system to be smart enough to see that and open the
modal. And second, I want the modal to open instantly. I don't want to click this
button... then have to wait for 500 milliseconds before I see the modal. That's not
a good user experience.

So for part one - opening this modal as soon as there's content in the `turbo-frame`,
we're going to use a trick inside our Stimulus controller. Let me close a few
files. In `base.html.twig`, make this `turbo-frame` a target:
`data-modal-target="dynamicContent"`.

Here's the idea: if a modal has this target and HTML gets inside of this element
for any reason, I want our code to *notice* that and open the modal. To do that,
in `modal_controller.js`, add that target. And then I'm going to copy in the most
complex code that we're going to see in this tutorial.

But, hold on: even if it looks a bit complex, what it's *doing* is simple. If we
have a `dynamicContent` target, this code watches that element for any changes.
And anytime there *is* a change, it calls of function. Then we check to see if
the `dynamicContentTarget` has any HTML. If it does, open it! If it doesn't, close
it. It's that simple.

In `disconnect()`, we're deactivating that system. And also, just in case, if
our modal element is ever removed from the page for any reason, this will close
the dialog and do the cleanup.

The result of this is... pretty incredible. Refresh the page. Let's play. I'm going
to edit the `<turbo-frame>` as HTML and type: "will this open?". Boom! It does!
And if we empty the content... it closes.

And, more importantly, when we click the "New" link, it pops open with the form!
Amazing!

Ok, I think that's enough for today. Tomorrow, we're going to make sure this form
submits. And, because I can't help myself, we'll add a few more goodies: like
opening the modal *instantly* instead of waiting for the AJAX call.
