# Asset Mapper

Okay, so how are we going to bring CSS and JavaScript into our app? Are we going
to use a build system like Vite or Webpack? Definitely not. That's one of the fun
things about this! We're going to create something *amazing* with *zero* build system.
And to do that, we're going to install a new Symfony component called asset-mapper.

## Installing AssetMapper

Spin over to our terminal and run:

```terminal
composer require symfony/asset-mapper
```

This is the new alternative to Webpack Encore. It can do everything pretty much that
Encore can do, but better and it's *way* simpler. You should definitely use it on
new projects.

When I run

```terminal
git status
```

We see that its Fkex recipe did a number of different things. For example, `.gitignore`
is ignoring a `public/assets/` directory and `assets/vendor/` directory. We'll
talk more about those later. But on production, this is where your assets will be
be written to and, when we install third-party JavaScript assets, they'll live in
that `vendor/` directory.

It also updated `base.html.twig` and added an `importmap.php` file. Ignore both
of those for today. We'll talk about them tomorrow.

## The "Mapped Paths"

For today, pretend that, when we installed this, all it gave us was a new
`asset_mapper.yaml` file and an `assets/` directory. Let's go check out that config
file: `config/packages/asset_mapper.yaml`.

The idea behind AssetMapper couldn't be simpler: you define paths here - like the
`assets/` directory - and AssetMapper makes every file inside available publicly...
as *if* they lived in the `public/` directory.

## Referencing an Asset File

Let me show you. If you download the course code, you should have a `tutorial/`
directory, which I added so we can copy a few things out of it. Copy `logo.png`.
Inside `assets/`, we can make this look however we want. So let's create a new
directory called `images` and paste that in.

Since this new files lives inside the `assets/` directory, we should be able to
reference it publicly. Let's do that in our base layout: `templates/base.html.twig`.
Anywhere, say `<img src="">`, `{{` and then use the normal `asset()` function. For
the argument, pass the path *relative* to the `assets/` directory. This is called
the logical path: `images/logo.png`.

Before we try this, an easy way to see all of the assets that are available is via: 

```terminal
php bin/console debug:asset
```

Very simply: this looks through all of your mapped paths - just `assets/` for us -
finds all of the files, and lists them with their logical path. So I can be lazy
and copy that, paste that here.... and done.

Now, when we try this, it doesn't work! The `asset()` function is still its *own*
component, so let's get it installed:

```terminal
composer require symfony/asset
```

And now.... it works!

## Instant Asset Versioning

To see the *really* neat thing, inspect the image and look at the filename. It's
`/assets/images/logo-` and then this long hash. This hash is comes from the file's
*contents*. If we updated `logo.png`, it would automatically generate a new hash.
And that is *super* important for two, related, reasons. First, because when we
deploy, the new filename will bust the browser cache for our users so they instantly
see the new file. And second, because of this, we can configure our production
web server to serve all of our assets with long-lived Expiration headers, which
will *maximize* that caching & performance.

## Serving Assets in Dev vs Prod

Now in the `dev` environment, there *is* no physical file with this filename.
Instead, the request for this assets is processed through Symfony and intercepted
by a core listener. That listener looks at the URL, finds the matching logo.png
inside of the `assets/images` directory and returns it.

But on production though, that's not fast *enough*. So, when you deploy, you'll
run:

```terminal
php bin/console asset-map:compile
```

Very simply: this writes all of the files into the `public/assets/` directory. Look:
in `public/assets/`, we now have real, physical files. So when I go over and
refresh, this file isn't being processed by Symfony, it's loading a real physical
file.

Now, if you ever run this command locally, make sure to delete that directory
after... so that it stops using the compiled versions.

Wow! Day 2 is already done and we now have a way to service images, CSS or *any*
file publicly with automatic file versioning. The second part of `AssetMapper` is
all about JavaScript modules. That's the topic for tomorrow.
