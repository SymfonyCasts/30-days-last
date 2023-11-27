# JavaScript Modules

Inspect element on this page and head over to the browser console. Ah, we've got
a console log that says it's coming from `assets/app.js`. And sure enough, if we
spin over and open that file... there it is!

But how is this file being loaded? To answer that, view the page source. There's
some interesting stuff going on here, but I want you to zoom in to this
`<script type="module">`, `import 'app'` part.

## ECMAScript Modules

It turns out that all modern browsers - basically everything except for IE 11...
and you should *not* be supporting IE 11 anymore - all modern browsers support
JavaScript modules also known as ECMAScript modules or ESM. But it's nothing too
fancy: you're inside of a JavaScript module if that file uses the `import` or `export`
statements, which you probably got used to with Webpack Encore.

The big news is that: browsers understand `import` and `export`! If you open any
HTML page and say `<script type="module">`, you the code inside is allowed to
use import and export statements. 

## Importmaps

So... the second question is: what the heck is `app`? How does `app` ultimately
refer to `assets/app.js`? This is *also* a new trick of browsers called importmaps.
And this has *nothing* to do with Symfony or AssetMapper. If, on your page, you have
a `<script type="importmap">`, this becomes a key value map that's used by your
browser when it loads modules. So if we say `import 'app'`, it looks inside of this
list, sees `app` and ultimately loads *this* file... which is served by AssetMapper.
It's a nice bit of teamwork!

Importmaps are supported in all modern browsers... though it has slightly less support
than the JavaScript modules. Fortunately, there's a shim or polyfill so that if
your user happens to have a browser that *doesn't* support import maps, that shim
will *add* it and everything will work.

## The importmap() Function

The final question on my mind is: where the heck is this all coming from? To answer
that, open `templates/base.html.twig`. It's entirely coming from this one line right
here: `{{ importmap('app') }}`.

Because we passed `app`, this will generate a `<script type="module">` with
`import 'app'` inside. But this also dumps the polyfill, some preloads - those
are good for performance, but not required - and, of course, the importmap itself.
The importmap is *primarily*, though not entirely (we'll get to that) generated from
this `importmap.php` file.

## The importmap.php File

When we installed AssetMapper, its recipe gave us this file. And *this* is the reason
that we have an `app` key in the importmap on the page that points to
`assets/app.js`.

## Writing Some JavaScript Modules

So I want to play a bit with this new system. Inside the `assets/` directory - we
can organize this however we want - create a `lib/` directory with an
`alien-greeting.js` file. Inside, I'm going to write some awesome new JavaScript:
`export default` a function, give it `message` and `inPeace` arguments... then I'll
log a message with some emojis using those arguments.

So, great! This new file lives inside `assets/` so, technically, it's publicly
available to the user. But... nobody is *using* it yet.

To prove that we can, go into the base layout and, anywhere, say `<script type="module">`
module. Inside, `import alien greeting`... and Ill hit tab.

Notice it uses `../assets` for the path. That's not going to work. Instead, we
can use the `asset()` function and the logical path: `lib/alient-greeting.js`.
Then below, try to use that: `alienGreeting()`, a message and we will *not* come
in peace!

Let's see if it works! Close that, and... it *doesn't*. i actually thought that
*would* work! We get a 404 for `lib/alien-greeing.js` - with no "s"...! Boop!
Now it works! No build, nice code, nothing special. 

If you view the page source, we, of course, have this nice version filename
in the `import`. So you can import simple things like `app` and rely on the
`importmap` to poing to the true filename or you can include full paths.

## Importing from JS Files

Ok, as fun as it was to hack this into the HTML, in reality, we're not usually going
to be write code in-line like this. Copy this code, get rid of that
`<script type="module">`, then go into `app.js`. Paste the code here.

And now that we're inside of JavaScript, when we refer to a path, we can write it
with normal, relative paths: `./alien-greeting.js`.

This is the exact code that we would have in Webpack Encore, with one small
difference. In Webpack, you don't need to have the `.js` on the end. It turns out
that leaving *off* the extension is a Node-specific thing. In real JavaScript, you
*do* need to have the extension. So you *do* need to add the `.js`.

And...  it works!

## PhpStorm: Auto-add Extension

By the way, if you let PHPStorm auto-complete the path to the imported JavaScript
file, by default, it will *not* include the `.js` on the end. To fix that, open
the settings... and search for "extensions". There we go: Editor => Code Style =>
JavaScript. And right here, change this "use file extension" to "always".

Ok, day 3 is in the books! Tomorrow, we'll make our JavaScript set up much more
powerful by learning how to install 3rd-party packages!
