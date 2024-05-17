# Asset Mapper

Okay, so how are we going to bring CSS and JavaScript into our app? Are we going
to add a build system like Vite or Webpack? Heck no! That's one of the fun
things about all of this! We're going to create something *amazing* with *zero*
build system. To do that, let's install a new Symfony component called AssetMapper.

## Installing AssetMapper

Spin over to our terminal and run:

```terminal
composer require symfony/asset-mapper
```

This is the new alternative to Webpack Encore. It can do pretty much everything that
Encore can do and more... but it's *way* simpler. You should definitely use it on
new projects.

When I run:

```terminal
git status
```

We see that its Flex recipe made a number of changes. For example, `.gitignore`
is ignoring a `public/assets/` directory and `assets/vendor/`:

[[[ code('2b0d764e8d') ]]]

We'll talk more about those later. But on production, this is where your assets will be
written to and, when we install third-party JavaScript libraries, they'll live in
that `vendor/` directory. 

It also updated `base.html.twig` and added an `importmap.php` file. But put
those on the back burner for now: we'll talk about them tomorrow.

## The "Mapped Paths"

For today's adventure, pretend that, when we installed this, all it gave us was a new
`asset_mapper.yaml` file and an `assets/` directory. Let's go check out that config
file: `config/packages/asset_mapper.yaml`:

[[[ code('ea53167677') ]]]

The idea behind AssetMapper couldn't be simpler: you define paths - like the
`assets/` directory - and AssetMapper makes every file inside available publicly...
as *if* they lived in the `public/` directory.

## Referencing an Asset File

Let's see it in action you. If you downloaded the course code, you should have a `tutorial/`
directory, which I added so we can copy a few things out of it. Copy `logo.png`.
Inside `assets/`, we can make this look however we want. So let's create a new
directory called `images/` and paste that in.

Since this new files lives inside the `assets/` directory, we should be able to
reference it publicly. Let's do that in our base layout: `templates/base.html.twig`.
Anywhere, say `<img src="">`, `{{` and then use the normal `asset()` function. For
the argument, pass the path *relative* to the `assets/` directory. This is called
the logical path: `images/logo.png`:

[[[ code('bbb066562f') ]]]

Before we try this, an easy way to see *every* asset that's available is via: 

```terminal
php bin/console debug:asset
```

Very simply: this looks through all of your mapped paths - just `assets/` for us -
finds every file then lists them with their logical path. So I can be lazy
and copy that, paste it here.... and done.

Now, when we try this, it doesn't work! The `asset()` function is still its *own*
component, so let's get that installed:

```terminal
composer require symfony/asset
```

And now.... cool logo!

## Instant Asset Versioning

To see the *really* neat thing, inspect the image and look at the filename. It's
`/assets/images/logo-` and then this long hash. This hash comes from the file's
*contents*. If we updated `logo.png`, it would automatically generate a new hash.
And that is *super* important for two, related, reasons. First, because when we
deploy, the new filename will bust the browser cache for our users so that they
see the new file immediately. And second, because of this, we can configure our
production web server to serve all the assets with long-lived Expiration headers.
That *maximizes* that caching & performance.

## Serving Assets in Dev vs Prod

Now in the `dev` environment, there is *no* physical file with this filename.
Instead, the request for this asset is processed through Symfony and intercepted
by a core listener. That listener looks at the URL, finds the matching `logo.png`
inside the `assets/images/` directory and returns it.

But on production, that's not fast *enough*. So, when you deploy, you'll
run:

```terminal
php bin/console asset-map:compile
```

Very simply: this writes all the files into the `public/assets/` directory. Look:
in `public/assets/`, we now have real, physical files! So when I go over and
refresh, this file isn't being processed by Symfony, it's loading one of those
real files.

Now, if you ever run this command locally, make sure to delete that directory
after... so it stops using the compiled versions:

```terminal-silent
rm -rf public/assets/
```

Wow! Day 2 is already done! We now have a way to serve images, CSS or *any*
file publicly with automatic file versioning. The second part of `AssetMapper` is
all about JavaScript modules. And that's tomorrow's topic.
