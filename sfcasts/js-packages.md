# 3rd Party JavaScript Packages

Welcome to day 4! Where we're already creating JavaScript modules... a fancy term
that means we're writing import statements and export statements. More importantly,
we're do that entirely *without* a build system. Rejoice!

But what about using third-party packages? Head over to npmjs.com and search for
a very important package called `js-confetti`. This package is all about celebrating,
which... exactly what we're during these 30 days! In the README, it says to use
yarn to install it. We are *not* going to do that. Instead, skip right down to the
usage example. Copy that, head over to our `app.js`... and paste that in.

Side note: `import` statements always go at the *top* of your file. If you *don't*
do that - if you do something weird like this, well, you *can*, but your browser
will move this up to the top when it executes the code anyway.

## Missing JavaScript Module Error

Ok: is this going to work? I mean... probably *not* because we haven't *installed*
anything. But let's live recklessly and try it anyway! Error! A very important error:

> Failed to resolve module specifier `js-confetti`. Relative references must start
> with either `/`, `./` or `../`.

So what this is saying is that your browser found an `import` statement and has no
idea how to load that file. If an import statement starts with `./` or `../`, your
browser knows how to handle that: it looks for a file *relative* to this file.
Easy peasy.

But if there is *no* `./` or `../`, it's called a bare module. In that case, your
browser looks for a match in the importmap. Right now, our importmap looks like it
did before. Notably, we *don't* have a `js-confetti` key. And *that's* why we get
this error.

This is one of the *most* important errors you're going to see when coding with
modules. And it will look a bit different based on which browser you're using.
Firefox, for example, phrases this error differently.

But regardless of the wording, this error almost always means the same thing: you're
trying to use a third party package, but it's not "installed".

## Installing Packages with importmap:require

How *do* we install that package? Glad you asked! Copy the package name, spin over
and run:

```terminal
php bin/console importmap:require js-confetti
```

That's it! Spin over and... celebration! It works! Mad refreshing!

*How* does that work? Karma? Well, not surprisingly, if you view the page source,
we have a new entry inside our `importmap` with the key `js-confetti`. And it points
to a file in an `assets/vendor/` directory. Interesting.

When we ran that command, it really did just one thing. It updated our
`importmap.php` file. It added this entry right here. Behind the scenes, it went
out and found what the latest version was and put that here. And because we have
a `js-confetti` item in `importmap.php`, it means that we're going to have a matching
`js-confetti` item inside of the importmap on the page.

## The assets/vendor/ Directory

Where does that file actually live? It lives up here in a new `assets/vendor/`
directory. If you dig, here, here is the actual file that's being loaded.

Two important things about this `vendor/` directory. The first is: it's ignored from
git: you can see `/assets/vendor`. Just like the composer `vendor/` directory, this is
*not* something that you should commit to your repository.

The second is more of a question: how do I get these files if I clone a project
or update a project and one or more files is missing?

To find out, get crazy and destroy that directory. Muwahahaha. And now, to increase
our reckless streak, try to refresh the page. Error! Awesome error! The
`js-confett` vendor asset is missing: try running the `importmap:install` command.

Lovely idea! Spin over and try that:

```terminal
php bin/console importmap:install
```

Just like `composer install`, that downloads whatever you need into your
`assets/vendor/`... and now it just works.

That's it! Third party packages are
available. By day 4, we've already solved 3rd party packages! We don't even need
a giant `node_modules/` directory! I think can downgrade my hard drive space.

Ok, our site needs some CSS. We'll solve that tomorrow.
