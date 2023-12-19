# Data Tables with Turbo Frames

Our data tables-like setup is working. And it's *almost* awesome. What I don't love
is how it jumps around. Every time we click a link, it jumps back to the top of the
page. Boo.

That's because Turbo is reloading the full page. And when it does that, it scrolls
to the top... because that's usually what we want! But not this time. I want our
data table to work like a little application: where the content changes without moving
around.

## Turbo 8 Morphing?

There are two great ways to solve this. In Turbo 8 - which is *not* released yet,
it's Beta 1 at the time of recording this - there's a new feature called page
refreshes that leverages morphing. Without nerding out too much - and I want to -
when navigating to the same page - like our search form, column sorting and
pagination links *all* do - we can tell Turbo to only update the content on the
page that *changed*... *and* to preserve the scroll position. So, keep an eye out
for that.

## Adding a Turbo Frame

The second way - which works fantastically today - is to surround this entire table
with a `<turbo-frame>`. In `homepage.html.twig`, find the `table`. Here it is:
this `div` represents the table. Above it, add `<turbo-frame id="voyage-list">`.
Indent this `div`... and also these pagination links: we want those to be inside
the Turbo frame so that when we click on them, they advance the frame & update:

[[[ code('88574e7472') ]]]

Let's try this. Zap that search clear. And oh... beautiful. Look at that! Everything
moves *within* the frame. Try pagination. That too! All of our links point *back*
to the homepage... and the homepage, of course, *has* this frame.

But remember: now that this table lives inside a Turbo frame, if we have any links
inside, they'll stop working. Again, to fix that, on each link, add
`data-turbo-frame="_top"`. Or to be more conservative, go up to the new
`<turbo-frame>` and add `target="_top"`. If you do that, you'll also need to find
the sorting and pagination links that *should* navigate the frame and add
`data-turbo-frame="voyage-list"`.

But I'll remove those... because we don't have any links in the table.

## Targeting the Search on the Form

At this point, the pagination and sorting links work perfectly! But...
the search? The search is still a full page reload. That makes sense! I didn't put
that inside the frame. Why? Because, if we had, when we typed and the frame reloaded,
it would have *also* reloaded the search box... which would *still* reset my cursor
position. So we *don't* want the form to reload.

Can we... *keep* this outside of the frame but *target* the frame when
the form submits? We can! Up on the `form` element that submits, add
`data-turbo-frame="voyage-list"`:

[[[ code('c861024e8a') ]]]

Isn't that cool? Now when we refresh: watch. It's perfect! The table loads,
but I *keep* my input focus. This is gorgeous.

## Adding a Loading Screen

And now we have time to make things extra fancy! What about a loading indicator
on the table while it's navigating? To make this obvious, go to our controller
and add a `sleep()` for one second:

[[[ code('2877140665') ]]]

Now... it's *slow*... and when we click or search, we don't even getting any
feedback that the site is *doing* anything.

How *can* we add a loading indicator? This is a spot where Turbo has
our back. So here's the `<turbo-frame>` element. Watch the attributes at the end
when I navigate. Did you see that? Turbo added an `aria-busy="true"` attribute
while it was loading. That's there for accessibility, but it's also something that
we can leverage within Tailwind!

Over on that `<turbo-frame>` element, here it is, say `class=""` with
`aria-busy:opacity-50`.

This special syntax says that, *if* this element has an `aria-busy` attribute,
apply the `opacity-50`. Add one more `aria-busy:` with `blur-sm` to blur the
background. And for extra points, include `transition-all` so that the opacity
and blur *transition* instead of happening abruptly:

[[[ code('f15c734c69') ]]]

***TIP
For an even nicer effect, you can also change the opacity & blur only if loading
takes longer than, for example, 700ms. Do that by adding an `aria-busy:delay-700` class.
***

Refresh that and watch. Oh, that's lovely! And it all happens thanks to 3 CSS classes.
And, though I love watching that, in `MainController`, remove the sleep.

## Advancing the Frame

Is this mission accomplished? *Nearly*. There's one gigantic, horrible problem...
with an easy solution. When we search or sort or paginate, the URL doesn't change.
That's the default behavior of Turbo frames: when they navigate, they don't update
the URL. However, we *can* tell Turbo that we *want* this. On the Turbo
Frame, add `data-turbo-action="advance"`:

[[[ code('5bb7f7f1df') ]]]

Advance means that it will update the URL and *advance* the browser history so
that if we hit the "Back" button, it'll go the previous URL. You can also
use `replace` to change the URL, but *without* adding to the history.

Watch: this time when we search... the URL updates! And as we navigate to page
two or three... it updates... and we can hit back, back, and forward, forward.

We now have a truly *fantastic* data tables setup... entirely written without
any custom JavaScript inside of Twig and Symfony. What a time to be alive.

The final teensy problem is this "30 results". As we search, that never changes:
it's stuck on whatever number was there when the original page loaded. That's because
this lives *outside* the Turbo frame. The easiest fix would be to move it
*into* the frame... but I don't want it there! Visually, I want it up here!

We're going to leave that for now. But we'll fix it in a few days with Turbo Streams.

Tomorrow, we're going to dive into a brand-new browser feature! It's called
View Transitions, and it'll let us apply visual transitions to any navigation.
