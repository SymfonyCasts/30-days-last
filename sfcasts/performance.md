# Performance

We've made it to our final day of Last Stack! And today I want to talk about
performance, starting with the things that are are *not* doing.

## No File Combining or Minifying

For example, we are *not* combining files to reduce requests. And, we are *not*
minifying our files. Nope, we're serving up raw source files from our `assets/`
directory.

And yet, our app is fast! Open your debugging tools and go to Lighthouse. Let's
profile this for performance on the desktop to keep things simple. Give this a few
seconds to run and... boom! 99! That's amazing!

## On Production: Compression & Caching

But let's scroll down to see what we could improve. The number one problem is missing
compression. There are really only two things that you need to worry about when you
deploy your app with AssetMapper.

One: on your web server, enable compression, like
gzip or Brotli. Or you can proxy your site through Cloudflare and can do compression
for you. That's what we do. This is why we don't need to worry about minification:
if you just compress your CSS and JavaScript files, that does almost as good of a
job as minification.

The second thing you need to do - which should be mentioned down here, ah yes:
serve static assets with an efficient cache policy. Because all of our files have
an automatic version hash in the filename, you should configure your web server to
cache *everything* from your `assets/` directory... *forever*. This means that when
your user downloads a file, they'll cache it forever: they'll never need to download
it again. That's great for performance.

## Unused CSS?

Let's see what else we have. Reduce unused CSS. That's probably *not* a problem.
In fact, it's one of the *benefits* of Tailwind: it only builds the CSS that we're
*actually* using. My guess is that the rest of the CSS is used on different pages.
And the difference is even smaller than it looks. This is 38 kilobytes, but again,
we're not compressing yet. In production, the difference would be much smaller.

## Unused JavaScript

Under reduce unused JavaScript, there's one main item: it's the live components
JavaScript, which *is* fairly large. We *are* using it, but it's true that we're
not using a lot of its features yet. On production, due to compression, this would
be smaller... and we *are* going to optimize this a bit.

Next is eliminate render blocking resources. This *is* important and it lists our
CSS file. We'll come back to this in a few minutes.

But really... there's nothing major here. We *could* minify CSS, but it would make
barely any difference. Minifying JavaScript - 68 kilobytes looks good, but again,
that's before it's compressed. And remember our score of 99! Our frontend is fast!

Oh, though apparently my images are *way* too big. There *are* still some things
you need to handle on your own.

## Preloading

One of the main reasons that our app is already so fast is preloading. Look
at the page source. We have the importmap, a bunch of preloads, then the most
important: `<script type="module">`, `import 'app'`.

When our browser sees this, it connects `app` to he real filename and starts
downloading it. Module script tags are not "render blocking". This means that
the browser starts downloading this file, but continues to render the page visually
while it's doing that. But, of course, it can't execute our JavaScript until it's
done downloading `app.js` file.

And there's a problem hiding. Only *after* it finishes downloading `app.js` does
it realize that it also needs to download this file, and this file, and this file, and
this file, and this file. And it's only *after* downloading `bootstrap.js` that it
realizes it needs to download *this* file. You can imagine a big waterfall of
finishing one JavaScript file, then starting a few more, finishing those, then
starting even more. It could take a really long time for our JavaScript to finally
execute.

*This* is where these preloads come in. This tells our browser:

> you don't realize it yet, but you should start downloading these files immediately.

The way these are generated is *really* cool. Open `templates/base.html.twig`. All
of this is rendered thanks to `importmap('app')`. By passing `app`, the main
result is that it adds the script tag at the bottom that imports `app`.

But this *also* tells AssetMapper to parse `app.js`, find all the files that *it*
imports and adds them as preloads. And it does it recursively: it goes into
`bootstrap.js` and finds *its* import. It finds *all* of the JavaScript that's
needed on page load and make sure that every file is preloaded. It just works.

And we can see this visually. In `alien-greening.js`: comment-out the import for
the CSS file: the delay just makes waterfall harder to see.

Then go to the Network tab, look just at JavaScript and do a force refresh.
Check it out! All the JavaScript files start at the same time! It's not waiting for
anything to download: they all start immediately. *That's* what we want to see.

The only file that starts later is `celebrate-controller.js`... because we set
this up to be lazy. This means our JavaScript initializes, *then* it downloads
this controller only when it's needed... which is *always* because it's on every
page, but it still delays it a bit.

## Lazy-Loading Live Components

Sort this by filesize. Notice the biggest file is the JavaScript for Live Components.
This 123 kilobytes isn't compressed, so it'll be smaller on production. But since
we only need this in the global search, we could choose to delay loading it.

To do that, inside `assets/controllers.json` file, find the live component controller
and set `fetch` to `lazy`.

Do a force refresh. It's still there, but check out the initiator: it's from
a JavaScript file and starts much later. In the source, I'll search for
`live_controller`. Previously, it was preloaded. When we refresh now, it's still
in the importmap, but no longer preloaded. We preload the really important stuff,
and then the live controller loads itself later.

## Preloading CSS with WebLink

Ok on last thing, magical thing. The most important thing that we saw inside of
Lighthouse was the render-blocking resource for our CSS file. When your browser
sees a `link rel="stylesheet"` tag, it freezes rendering your page until it finishes
downloading the file. And that's a good thing. We don't want our page to render
unstyled for a second.

But this is why we put our CSS `link` tags up in the `head` of our page: we want
the browser to notice that it needs to download the file as early as possible.
*However*, there *is* a way to tell our browser even *earlier* that it needs to
download this file.

Find a terminal and run:

```terminal
composer require symfony/web-link
```

This is a small package that helps add different hints to your browser about what
it needs to download.

*Just* by installing that, go to the Network tab, filter all, refresh and go to the
top for the main request for the page. Look down here at the Response headers.
There it is! Our app just added a new response header called `link` that points to
the CSS file with `rel="preload"`.

This tells the browser that it needs to download this file. And it sees this header
even *earlier* than it sees line 11 of the HTML. This helps performance just a
*little* bit more.

Now that we've made a few changes, let's run Lighthouse again. There *is* some
variability in these runs, so if your score doesn't change or even goes down
a little, no worries. But... a perfect 100! Woo!

More importantly.... we still have text compression... but we don't see the
render-blocking resource warning.

The moral of the story is this: using AssetMapper is fast out of the box. Other
than adding compression and caching to your web server, you can code in peace
without worrying. And sure, later, it *is* helpful to run Lightouse and see how
you can improve, but it doesn't need to be something you think about day-by-day.
Get your real work done instead.

And... we're done! Thank you for spending these wild 30 days with me! It has been
an absolute pleasure and a heck of a ride. Please, go build things and let us
know what they are! And if you have any questions, comments or doubts, we're
always here for you down in the comments section.

Seeya next time!
