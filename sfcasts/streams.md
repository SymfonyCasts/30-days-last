# Turbo Streams: Update any Element

Today, we're diving headfirst into the finale of the Turbo trilogy: Turbo Streams.
Streams allow us to solve problems that we... just don't have a solution for yet.

Take, for instance, our homepage: we have this really nice data tables system...
with one teeny tiny problem. When we type into this box, that number of results
doesn't change. It's stuck at whatever it was on page load. The Turbo Frame is around
this *table*, so the result count is *outside* of that.

This is where Turbo Streams comes in. When you're dealing with a Turbo Frame and
you need to update something *outside* of it, you need a stream. Streams have a fancy
name, but it's a simple idea. A Turbo Stream is actually a custom HTML element. I could
take this, put it onto my page, and it would instantly *execute*. It would find the
element on the page whose `id` is `messages` and append this content. And there are
actions for everything: prepend, replace, update, etc. We can use a Turbo Stream to
make any change we want to any element on the page... from anywhere. The power!

## Adding a `<turbo-stream>` Right on the Page

To prove this, copy the Turbo Stream that's an update. Back on our site, inspect
element on the "Space Inviters" name. Temporarily, give this an `id` called
`company_name` so we can target it.

Now, *anywhere* else on the page - so how about down here in the footer - edit as
HTML and paste that Turbo Stream. In this case, we want the target to be `company_name`
and inside the template element, say "Space Invaders". Now, check this out. As soon
as I click out of this, the `<turbo-stream>` custom element will become active and
will execute its action. Watch. Boom! It found that element and updated it!

Take a peek back at the footer: that `<turbo-stream>` is gone!
It executes, then self-destructs and removes itself from the page. It's the most
noble of custom elements.

And even if it *were* on the page for a moment, remember: all `<turbo-streams>` have
a `template` element inside. We talked about that element on Day 11: anything
inside a `<template>`... isn't *really* on the page at all: it's completely
hidden and inactive. So even if this *were* on the page for a moment, it would be
invisible.

Streams *just* work.

## Updating the Result Count with a Stream

So let's use them to solve our problem! Open `templates/main/homepage.html.twig`
and search for "results". Here's the element we need to update. To target this,
give it an `id`: how about `voyage-result-count`.

Copy that. When we search on the page, it's actually this `<turbo-frame>` that's navigating.
So *anywhere* inside this - I'll go to the bottom - we can add a `<turbo-stream>`.
Say: `<turbo-stream action="replace"`, `target=""` and paste. Then add the
`<template>` element - don't forget that - and I'll hard-code a message to start.

Ok, watch what happens when I refresh. Boom! Because the `<turbo-stream>` element
exists on page load, it immediately executes and replaces the element with the
custom content.

## Replacing the Real Content with a Block

So *now*... let's put in the *real* content. Essentially, we want to copy this
entire div... and paste it down here. Except... without *actually* duplicating
this.

To do this, we'll use a trick with Twig blocks. Surround the result count with a
new block called `result_count`... then `endblock` below. In Twig, you're free
to add blocks wherever you want. When you do, they don't *do* anything immediately.
When this renders, Twig will see this block.... ignore it... and render
the `div` like normal.

But now, we can go down inside our `<turbo-stream>` and say
`{{ block('result_count') }}`.

I think we're ready! Start by going to the homepage so we see the full 30 results.
And then as we type... ah, beautiful! The count updates as the results reload.
Dang, that was easy!

For those of you that are nerds for details, first, we love you, and second, yes,
on page load, we're rendering the result count twice: here... and, even though we
can't see it, we're *also* rendering it down here inside the Turbo Stream. So it's
being rendered twice inside the HTML. And that's not a problem at all, unless,
for some reason, calculating the result count takes a lot of work. *If* you had
that situation, you could set the count to a Twig variable, then render in both spots.

All right, tomorrow we'll start into the biggest, boldest part of this entire series:
building a reusable modal system that just absolutely rocks. I'm so excited!
