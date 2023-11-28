# Symfony UX Packages

Head over to https://ux.symfony.com. This is the site for the Symfony UX Initiative,
which is a group of PHP and JavaScript packages that give you free Stimulus
controllers. There's a Stimulus controller that can render chart.js, one that
can add an image cropper, and so on.

Today we're going to focus on grabbing a *free* Stimulus controller that will give
us a fancy autocomplete `select` element. You can search, select - it's all very
nice.

On our site, head to the voyages section and hit edit. The form has a planet dropdown,
which is fine... but, let's be honest, this could use a little extra fanciness.

## Installing UX Autocomplete

So let's get this package installed. The UX Autocomplete package is a mixture of
PHP with a Stimulus controller inside. Copy the composer require line and paste:

```terminal-silent
composer require symfony/ux-autocomplete
```

When that finishes... run:

```terminal
git status
```

Oooh: the recipe mdoified two interesting things: `controllers.json` and `importmap.php`
We know that everything in the `assets/controllers/` directory will be available
as a Stimulus controller. In addition to that, anything in our `controllers.json`
file will *also* be registered as a Stimulus controller. It's a way for third-party
PHP packages to add more controllers. The recipe added this entry, which basically
means that it'll grab a some code from the package we just installed and register
it as a Stimulus controller.

The point is, we now have a *third* Stimulus controller in our app! The other change
the recipe ,made is in `importmap.php`: it added a new entry for `tom-select`.
`tom-select` is a JavaScript package, and that's actually what does the heavy lifting
for the autocomplete functionality. This stimulus controller is just a small wrapper
around `tom-select`. And so, since that controller needs `tom-select`, it was added
here!

## UX "autoimport" CSS

But when we refresh the page, we are greeted with a *lovely* error. It says

> The `autoimport` `tomselect.default.css` could not be found in `importmap.php`.
> Try running `importmap:require` and then that path.

Look back into `controllers.json`. Sometimes, these controllers have an extra section
called `autoimport`. The idea is that a Stimulus controller might have a CSS file
that goes along with it. This section allows you to activate or deactivate those
CSS files. For example, with `tom-select`, there's a default CSS file or if you're
using bBootstrap, you can use the Bootstrap 5 CSS file. We could set this to `false`
in set to `true`.

One difference between using JavaScript modules in a browser versus like Node &
Webpack is how *much* of the package you get. With Node, if you `npm add tom-select`,
that will download the *entire* package: the JavaScript files, CSS files and
anything else. But with AssetMapper & the browser environment in general, when
you `importmap:require tom-select`, that downloads a *single* file: just the
JavaScript file. The CSS files are *not* there.

*However*, when you run `importmap:require`, you can, of course, grab a package
with its name, like this. Cool. But you can *also* import a specific file path
*within* that package. And, because AssetMapper support CSS files, that path can
be to a CSS file.

In other words, if we need this vendor CSS file, we can get it with:

```terminal
php bin/console importmap:require tom-select/dist/css/tom-select.default.css
```

Now we have it! Over in the `assets/vendor/` directory... there it is! And in
`importmap.php`, it's there too. This means it's available for our Stimulus
controller to import.

The end result? Error gone! And in the page source, there's the CSS file.

## Applying Autocomplete to a Field

Ok, after one `composer require` call, one `importmap:require` and a ton of *me*
yapping, we have a new autocomplete Stimulus controller ready to go.

We could add a `data-controller` to the `select` element, but UX packages are usually
a mixture of Stimulus controllers and PHP code. In this case, the PHP code allows
us to activate the controller directly in our form. Open up `src/Form/VoyageType.php`.
The `planet` field is an `EntityType`. And, thanks to the new package, any
`EntityType` or `ChoiceType` can now have an `autocomplete` option. Set it to
`true`.

And now... woo! Ok, the styling isn't perfect, but it works! That option activated
the Stimulus controller: you can even see it on the page. Here's the `select`
now with a `data-controller` followed by a long name for that controller.

## Customizing the CSS

How can we make this look better? Thanks to the `autoimport`, the
`tom-select.default.css` at least makes it look okay. If we were using Bootstrap,
I'd change this to `true`, this to false, then `importmap:require` the Bootstrap
file and we'd be good.

Right now, there's no official support for Tailwind, so we'll style it manually.
Over in `assets/styles/app.css`, I'll remove this `body`. In addition to the Tailwind
stuff, you can paste in whatever custom styling you need. These override some
of the default styles to look nice in our space-themed, dark mode.

And now... super nice! Love that.

## Making UX Controllers Lazy

Oh, remember how we can make *our* controllers lazy by adding a special comment?
We can do the same thing with the controllers loaded in `controllers.json` by
setting `fetch` to `lazy`.

Check this out. Go to the voyages page. I'll go to my network tools, refresh and
search for autocomplete... and Tom select. Nothing. But as *soon* as we go to the
edit page where that's being used: search for autocomplete. There it is! `tom-select`
and the CSS file were also loaded lazily, only when we needed it.

We're now done with day 8 - a full week and day into LAST stack! Tomorrow, we turn
things up a notch and transform our app into a single-page-application with Turbo.
Over the next 7 days, things are start getting crazy.
