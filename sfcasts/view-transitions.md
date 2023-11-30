# View Transitions

Day 15! We're already halfway through our adventure. And it only gets cooler from
here.

To celebrate, today we'll work on something sparkly & new: the View
Transitions API. This nifty new feature is supported in most browsers and allows us
to have smooth transitions whenever *any* HTML changes on our page. And if your
user has a browser that *doesn't* support it, that's ok! The transition is just
skipped, but everything keeps working. No biggie.

Oh, and, View Transitions will come Standard in Turbo 8 for full page navigation.
Though, we'll take them even a bit further.

## Evil Martians & Demoing View Transitions

To use View Transitions, you do *not* need any external library. But we're going
to use one called `turbo-view-transitions`. This came out of a blog series
where the author built a neat project called [Turbo Music Drive](https://github.com/palkan/turbo-music-drive).
In two blog posts on Evil Martians, they talk all about morphing and doing
crazy stuff with it in Turbo. They even created a live demo! In the simplest form,
view transitions adds a crossfade as you navigate. But you can also get more specific
and connect elements between pages to give them a special transition. Watch: when
I click this album, it moves up to the left. There's also a nice crossfade for the
rest of the page.

## Installing turbo-view-transitions

So let's try this out! Step one, get the `turbo-view-transitions` library. At
your terminal, run:

```terminal
php bin/console importmap:require turbo-view-transitions
```

Lovely! To activate transitions, we need to do two things. First, in `base.html.twig`,
add a `meta` tag with `name="view-transition"`. That's how you tell your browser
you want these!

Second, in Turbo 7, we need to activate transitions in JavaScript. Head to `app.js`.
This will be a rare time when we write JavaScript that doesn't need to live in a
Stimulus controller. Copy from their example, paste... and move the `import` to the
top.

Done! That should be enough to make the Turbo Drive navigations use view
transitions! This leverages an event from Turbo called `turbo:before-render`. The
`shouldPerformTransition()` function checks to see if the user's browser supports
transitions. If they don't, it's normal behavior. But if it *does*, then
`performTransition()` will transition between the old and new body. There's also
a little code down here to avoid the turbo page cache. I think that's something
that'll work better in Turbo 8 when this is official.

Time for the big reveal! Hit refresh and watch. Oooooh. A smooth
crossfade transition! So not only did we eliminate full page reloads, we now have
a transition between our pages. Watch our Powerpoint, we're coming for you!

## Transition Turbo Frames

But what about Turbo frames? When we click, that still happens instantly. We
activated transitions for full page navigations, but not for frames. Can we?
Sure!

Copy this listener, and paste below. This time, listen to
`turbo:before-frame-render`... and adjust this part. Instead of `document.body`,
use `event.target`. That will be the `<turbo-frame>`. And then the new
element will be `event.detail.newFrame`.

Done! Refresh and.... click. Transition, check!

## Debugging Transitions

And if the transition isn't obvious enough, you can open up your browser tools,
click the little "...", go to "more tools", then Animations. It looks like I already
had it open. Here, you can change the speed of your animations.

Now... it's super obvious. You can even see how it works. If you scroll during
the transition, you can kind of see how it takes a screenshot of the old HTML and
blends it with the new. This weird effect isn't normally a problem because it
happens so fast, but it's cool to see.

## Edge Case: Frames that Advance

To show a problem, let's remove the CSS transition on the table that we just
added. So spin over to that template... and take of the `class`.

Refresh... and try it. Huh. Nothing happens. I mean, it *works*... but there was
no view transition. Why?

This is subtle. The transition breaks when you have a frame that *advances* the URL.
The problem is that, in this situation, Turbo calls `turbo:before-frame-render`...
then *also* calls `turbo:before-render` right after. These two, sort of, collide.

But it's an easy fix. Create a variable: `let skipNextRenderTransition` and set
it to false. Below, if `shouldPerformTransition()` and *not* `skipNextRenderTransition`,
then do it.

Finally, when our frame starts its transition, set this variable to true. Also
include a `setTimeout()`, set that back to `false` and delay this for 100
milliseconds.

It's a bit weird. But hey, that's programming! We set this to true, Turbo triggers
the other listener almost immediately... then 100 milliseconds we reset it. We could
probably also replace the `setTimeout()` by setting `skipNextRenderTransition = false`
up in the `turbo:before-render` listener.

The most important thing is that... *now* we have a transition! Let's set that back
to full speed. I like it!

## Disabling Transitions

Try the planet popover frame. Woh! That was weird. To be fully honest, I do *not*
know what's happening here. For some reason, the view transition causes the popover
to disappear... which is... let's say... *not* ideal. There's probably a way to fix
that, but I couldn't crack it.

That's ok... and I think this weirdness is isolated to the popover behavior.
Instead, we'll add a way to disable the transitions on a frame.

On the homepage, search for `turbo-frame`. Here it is. Add a new attribute called
`data-skip-transition`.

I totally made that up. Over an `app.js`, we can look for that. If
`shouldPerformTransition()` and *not* `event.target.hasAttribute('data-skip-transition')`,
*then* do the transition.

Now... fixed! And we have transitions on... virtually *every* type of navigation
on our site. And in only about 10 minutes! It's crazy!

Now to tomorrow's mission: crafting a dazzling toast notification system that's
easy to activate no matter where and how we need to add them. Seeya then!
