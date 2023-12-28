# Live Components

Happy Day 27 of Last Stack! We've accomplished a lot during the first 26 days with
just *three* letters of LAST Stack: Asset Mapper, Stimulus, and Turbo. Today
we crack the code on the L of LAST Stack: Live components. Live components
let us take a Twig component... then re-render it via Ajax as the user interacts
with it.

Our goal is this global search. When I click nothing happens! What I *want* to
do is open a modal with a search box that, as we type, loads a live search.

## Opening the Search Modal

Start inside `templates/base.html.twig`. Search for search! Perfect: this
is the fake search input we just saw. Add a `<twig:Modal>` with `:closeButton="true"`,
then a `<twig:block>` with `name="trigger"`. Put the fake input inside that.
To make this open the modal, we need `data-action="modal#open"`:

[[[ code('72856ac5a5') ]]]

Cool! If we refresh, nothing changes: the only visible part of the modal is the
trigger. For the modal content, after the Twig block, I'll paste in a div:

[[[ code('2a7ef09201') ]]]

Nothing special here: just a *real* search input.

Back at the browser, when I click... uh oh. Nothing happens! Debugging always
starts in the console. No errors, but when I click, watch: there's no log that
says that the action is being triggered. We've got something wrong with that and
maybe you saw my mistake? We added the `data-action` to a `div`. Unlike a `button`
or a `form`, Stimulus doesn't have a default event for a `div`. Add `click->`:

[[[ code('56d17b0986') ]]]

And now... got it!

Oh, and it auto-focused the input! That's.... just a feature of dialogs! They work
like a mini page within a page: it autofocuses the first tabbable element... or
you can use the normal `autofocus` attribute for more control. It just works how
you want it to.

## Modal: Control the Padding

Anyway, I'm picky: this is more padding than I want. But that's ok! We can make
our Modal component just a *bit* more flexible. In `components/Modal.html.twig`,
the extra padding is this `p-5`. On top, add a third `prop`: `padding='p-5'`.
Copy that. And down here, render `padding`:

[[[ code('25868ace78') ]]]

Over in `base.html.twig`, on the modal, add `padding` equals empty quotes:

[[[ code('9d705f4063') ]]]

Let's check it! And... much neater.

## Creating the Twig Component

To bring the results to life, we *could* repeat the data-tables setup from the homepage.
We could add a `<turbo-frame>` with the results right here and make the input
autosubmit *into* that frame.

Another option is to build this with a live component. But before we talk about that,
let's *first* organize the modal contents into a *twig* component.

In `templates/components/`, create a new file called `SearchSite.html.twig`. I'll
add a div with `{{ attributes }}`. Then go steal the entire body of the modal,
and paste it here:

[[[ code('ecf565684e') ]]]

Over in `base.html.twig`, it's easy, right? `<twig:SearchSite />` and done:

[[[ code('0e94612f0f') ]]]

At the browser, we get the same result.

## Fetching Data with a Twig Component

The site search is really going to be a *voyage* search. To render the results,
we have two options. First, we could... *somehow* get the voyages that we want to
show inside of `base.html.twig` and pass them into `SearchSite` as a prop.
But... fetching data from our base layout is tricky... we'd probably need a custom
Twig function.

The second option is to leverage our Twig component! One of its superpowers is
the ability to fetch its own data: to be standalone.

To do that, this Twig component now needs a PHP class. In `src/Twig/Components/`,
create a new PHP class called `SearchSite`. The only thing that this needs to
be recognized as a Twig component is an attribute: `#[AsTwigComponent]`:

[[[ code('102fd407d6') ]]]

This is exactly what we saw inside the `Button` class. A few days ago, I quickly
mentioned that Twig component classes are *services*, which means we can
autowire *other* services like `VoyageRepository`, `$voyageRepository`:

[[[ code('f055aa7e33') ]]]

To provide the data to the template, create a new method called `voyages()`!
This will return an array... which will really be an array of `Voyage[]`. Inside
`return $this->voyageRepository->findBySearch()`. That's the same method we're using
on the homepage. Pass `null`, an empty array, and limit to 10 results:

[[[ code('ce79365f0b') ]]]

The search query isn't dynamic yet, but we *do* now have a `voyages()` method that
we can use in the template. I'll start with a bit of styling, then it's
normal twig code: `{% for voyage in this` - that's our component object -
`.voyages`. Add `endfor`, and in the middle, I'll paste that in:

[[[ code('912f03e122') ]]]

Nothing special: an anchor tag, an image tag, and some info.

Let's try it. Open! Sweet! Though, of course, when we type, nothing updates!
Lame!

## Installing & Upgrading to a LiveComponent

*This* is where live components comes in handy. So let's get it installed!

```terminal
composer require symfony/ux-live-component
```

To upgrade our Twig component to a Live component, we only need to do two things.
First, it's `#[AsLiveComponent]`. And second, use `DefaultActionTrait`:

[[[ code('3e501d5fe5') ]]]

That's an internal detail... but needed.

So far, nothing will change. It's still a Twig component... and we haven't added
any *live* component superpowers.

## Adding a Writable Prop

One of the key concepts with a Live Component is that you can add a property and
allow the user to *change* that property from the frontend. For example, create
a `public string $query` to represent the search string:

[[[ code('e19ebad1d5') ]]]

Below, use that when we call the repository:

[[[ code('eea18c7ca1') ]]]

To allow the user to modify this property, we need to give it an attribute:
`#[LiveProp]` with `writeable: true`:

[[[ code('f61f86f139') ]]]

Finally, to *bind* this property to the input - so that the `query` property changes
as the user types - add `data-model="query"`:

[[[ code('c0aa812925') ]]]

That's it! Check out the result. We start with everything, but when we type...
it filters! It even has built-in debouncing.

Backstage, it makes an AJAX request, populates the `query` property with
this string, re-renders the Twig template and pops it right here.

Now that this is working, I don't think we need to load all the results at first.
And, look, it's just PHP, so this is easy. If not `$this->query`, then return an
empty array:

[[[ code('66a0d28e44') ]]]

And in `SearchSite.html.twig`, add an if statement around this: `if this.voyages`
is not empty, render that... with the `endif` at the bottom:

[[[ code('8f5ef0938d') ]]]

For those of you that are sticklers for details, yes, with `this.voyages`, we're
calling the method *twice*. But there *are* ways around this - and my favorite is
called `#[ExposeInTemplate]`. I won't show it, but it's a quick change.

## Fixing the Modal to the Top

So, I'm happy! But, this isn't *perfect*... and I want that. One thing that
bothers me is the position: it looks low when it's empty. And as we type,
it jumps around. That's the native `<dialog>` positioning, which is normally
*great*, but not when our content is changing. So in this one case, let's fix
the position near the top.

In `Modal.html.twig`, add one last piece of flexibility to our component: a prop
called `fixedTop = false`:

[[[ code('730d1c52e4') ]]]

Then, at the end of the `dialog` classes, if `fixedTop`, render `mt-14` to
set the top margin. Else do nothing:

[[[ code('7bef2b660c') ]]]

Over in `base.html.twig`, on the modal... it's time to break this onto multiple
lines. Then pass `:fixedTop="true"`:

[[[ code('2e55e72236') ]]]

And now, ah. Much nicer and no more jumping around.

## Setting the Search as Turbo Permanent

What else? Pressing up and down on my keyboard to go through the results *is* needed,
though I'll save that for another time. But watch this. If I search, then click out
and navigate to another page, not surprisingly, when we open the search modal,
it's empty. It would be *really* cool if it *remembered* the search.

And we can do that with a trick from Turbo. In `base.html.twig`,
on the modal, add `data-turbo-permanent`:

[[[ code('627a9d9f00') ]]]

That tells Turbo to *keep* this on the page when it navigates. When you use this,
it needs an id.

Let's see how this feels. Open the search, type something, click off, go to the
homepage and open it again. So darn cool!

## Opening Search on Ctrl+K

Ok, *final* thing! Up here, I'm advertising that you open the search with a keyboard
shortcut. That's a lie! But we *can* add this... and, again, it's easy.

On the modal, add a `data-action`. Stimulus has built-in support for doing things
on `keydown`. So we can say `keydown.`, then whatever key we want, like
`K`. Or in this case, `Ctrl`+`K`.

If we stopped now, this would only trigger if the modal were focused and then
someone pressed `Ctrl`+`K`. That's... not going to happen. Instead, we want this to
open no matter *what* is focused. We want a *global* listener. Do that by adding
`@window`.

Copy that, add a space, paste and also trigger on `meta+k`. Meta is the command
key on a Mac:

[[[ code('97700817a0') ]]]

Testing time! I'll move over and... keyboard! I love it! Done!

## Lazy-Loading Live Component

Oh, and Live Components can also be loaded lazily via AJAX! Watch: add a `defer`
attribute. When we refresh, we won't *see* any difference... because that component
is hidden on page load anyway. But in reality, it just loaded *empty* then
immediately made an Ajax call to load for real. We can see that down here in the web
debug toolbar! This is a great way to defer loading something heavy, so it doesn't
slow down your page.

It's not particularly useful in *our* case because the SearchSite component is
so lightweight, so I'll remove it.

Tomorrow, we'll spend one more day with Live Components - this time to give a form
real-time-validation superpowers *and* solve the age-old pesky problem of dynamic
or dependent form fields.
