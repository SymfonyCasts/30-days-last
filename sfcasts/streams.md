# Turbo Streams: Update any Element

Today we're going to learn about the third and final part of Turbo: Turbo Streams.
Streams will allow us to solve certain problems that we... just don't have a solution
for yet.

For example, on the homepage we have this really nice data tables system. However,
we have a teeny tiny problem: when we type into this box, that number of results
doesn't change. It's stuck at whatever it was on page load. The Turbo Frame is around
this *table*, so the result count is *outside* if it.

This is where Turbo Streams comes in. When you're dealing with a Turbo Frame and
you need to update something *outside* of it, you need a stream. Streams have a fancy
name, but it's a simple idea. A Turbo Stream is actually custom HTML element. I could
take this, put it onto my page and it would instantly *execute*. It would find the
element on the page whose `id` is `messages` and append this content. And there are
actions for everything: prepend, replace, update. We can use a Turbo Stream to
make any change we want to any element on the page from anywhere. The power!

## Adding a `<turbo-stream>` Right on the Page

To prove this, copy the Turbo Stream that's an update. Back on our site, inpspect
element on the Space Inviters name. Temporarily, give this an `id` called
`company_name` so we can target it. Cool.

Now, *anywhere* else on the page - so how about down here in the footer - edit as
HTML and paste that Turbo Stream. In this case, we want the target to be `company_name`
and inside the template element, say "Space Invaders". Now, check this out. As soon
as I click out of this, the `<turbo-stream>` custom element will become active and
it will execute its action. Watch. Boom. It found that element and updated it!

If you look back on the footer, that `<turbo-stream>` isn't even there anymore.
So it renders then self-destructs and removes itself from the page. It's the most
noble of custom elements.

And even if it *were* on the page for a moment, remember: all `<turbo-streams>` have
a `template` element inside. We talked about the `template` HTML element on Day 11.
Anything inside of a `<template>`... isn't *really* on the page at all: it's completely
hidden an inactive. So even if this *were* on the page for a moment, it would be
invisible.

So streams *just* work.

## Updating the Result Count with a Stream

So let's use them to solve our problem! Open `templates/main/homepage.html.twig`
and search for "results". Here's the element we need to update. To target this,
give it an `id`: how about `voyage-result-count`.

Copy that. When we search, it's actually this `<turbo-frame>` that's navigating.
So *anywhere* inside this - I'll go to the bottom - we can add a `<turbo-stream>`.
Say: `<turbo-stream action="replace"`, `target=""` and paste. Then add the
`<template>` element - don't forget that - and I'll hard-code a message to start.

Ok, watch what happens when I refresh. Boom! Because the `<turbo-stream>` element
exists in page load, it immediately executes and it replaces the element with the
custom content.

## Replacing the Real Content with a Block

So *now*... let's put in the *real* content. Essential,y we want to do copy this
entire div... and paste it down here. Except we don't want  duplication!

Instead we're going to use a trick with blocks. Surround the result count with a
new block called `result_count`... then `endblock` below it. In Twig, you're free
to add blocks wherever you want. When you do, they don't *do* anything immediately.
When this renders, Twig will see this block.... ignore it... and then render
the `div` like normal.

But now, we can go down inside our `<turbo-stream>` and say
`{{ block('result_count') }}`.

I think we're ready! Start by going to the homepage so we see the full 30 results.
And then as we type... ah, beautiful! The count updates as the results reload.
Dang, that was easy!

For those of you that are nerds for details, first, we love you, and second, yes,
on page load, we're rendering the result count here... and, even though we can't
see it, we're *also* rendering that same result count down here inside of the Turbo
Stream. So it's being rendered twice on the page. That's not an *actual* problem,
unless, for some reason, calculating the result count takes a lot of work. *If*
you had that situation, you could set it to a Twig variable, then just render in
both spots.

All right, tomorrow we'll start into the biggest, boldest part of this entire series:
building  a reusable modal system that just absolutely rocks. I'm so excited!
