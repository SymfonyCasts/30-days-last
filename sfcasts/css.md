# CSS

Day 5 already? We're flying! It's time to add some CSS to our site. So how does that
work inside AssetMapper?

## Including a Manual link Tag?

Well, we already have an `assets/styles/app.css` file. And... there's nothing
stopping us from going into `base.html.twig`, and adding a link tag: `link`,
`rel="stylesheet"`, `href` then `asset()` and the logical path: `styles/app.css`.

Swell! When we refresh... and look at the page source, there it is! It works great
and it's super boring. The kind of boring I like.

*However*, if we remove this line... and go and refresh the page. Huh, we *still*
have this `blue` background: a blue background that's coming from `app.css`.

Take another peek at the page source. There is *still* a `link` tag pointing to
that file? Back over in `base.html.twig`, hmm, nothing here. Where is that coming
from?

The answer - I bet you guessed - is the `importmap()` function. And it's because
it's being imported from `app.js`.

## How CSS Works

Importing a CSS file from JavaScript is probably something you got used to with
Webpack Encore. You import a CSS file... and ultimately, it's rendered on the page
as a `link` tag. *However*, this is *not* something that ECMAScript modules actually
support. The *only* thing you can import are *JavaScript* files. So this *should*
fail spectacularly: like it should download the CSS file and try to parse it as
JavaScript.

However, as you may have noticed, it doesn't fail! I love mysteries!

This is a *totally* extra feature that we added to AssetMapper. Here's how it
works. In `base.html.twig`, we say `importmap('app')`. The `app` is known as the
entrypoint: the *one* file the browser will execute directly. And we know that
refers to `assets/app.js`.

So what AssetMapper does is, it goes into this file and finds all the `import`
statements for JavaScript and CSS files. For every CSS import it finds, it adds that
as a `link` tag. It's... basically just that simple.

## The CSS Importmap Trick

Well, there *is* one little, fascinating complication. Go to the network tab in
your browser and search for `app`. This is the `app.js` file that's being executed
by the browser. Notice: it *does* still have the import statement to the CSS file!
If you think about it, when our browser executes this line, it should fail! It
should download the CSS file, try to parse it as JavaScript & hit a syntax error.
But it doesn't.

The reason is a trick inside AssetMapper. When you import a CSS file,
AssetMapper adds an importmap entry for it. So even though this starts with `./`,
our browser *does* look to see if there's a matching path inside the importmap.
And there *is*. Because of that, it downloads this file.... which is *not* a real
file. It's a fake file that does.... absolutely nothing. So it makes that line
not error out and... not *do* anything.

## Importing CSS from Other JavaScript Files

To see how powerful this is, let's create a second CSS file to support our alien
greeting. Call it `alien-greeting.css` and make the body background dark green.
Though, personally, I'm hoping aliens are rainbow colored.

Over in `alien-greeting.js`, import that: `../styles/alien-greeting.css`.

Will this work? Try it! Refresh and... green background! In the source, we have
a second `link` tag and a second new item in the `importmap`. So that's awesome!
Because `app.js` imports `alien-greeting.js`, AssetMapper *also* finds any CSS
files that *it* imports.

## Lazy-Loading CSS

Here's where things get *really* spooky-cool. JavaScript modules have a dynamic
import syntax that allows you to import modules asynchronously. That lets us load
a file *later* when we need it, instead of on page load. And we can use this
trick with CSS.

Copy this. Pretend that we only want to load that CSS file when `inPeace` equals
false. So I'll say, if not `inPeace`, then use `setTimeout()` to wait for 4 seconds.
After 4 seconds, import the CSS file. Except, as soon as you need an import to *not*
live at the top of your file, you need to call it like a function.

That's pretty cool. Try it. At first, blue background! 2, 3, 4, green background!
The CSS file loaded *lazily*. How? Well, there's no `alien-greeting.css` link tag
in the page source anymore. Instead, we wait for the browser to execute this
JavaScript line. When it does, it looks for this in the importmap, finds it and
downloads this fake file. But this time, instead of it being a line that does
*nothing*, this fake file adds a new `link` tag to the `head` section with
`rel="stylesheet"` and `href` set to `alien-greeting.css`.

Heck, we can watch this in real time! Over here, under the `head` tag, we see
the stylesheet. If I refresh and quickly open that, it's not there. And...
*then* it gets added. *So* stinkin' cool.

Now that we've conquered *how* CSS works, tomorrow, we'll use it to bring our site
to life! But I want to do it with an extra fun angle: I want to use Tailwind CSS.
