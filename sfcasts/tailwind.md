# Tailwind CSS

I love using Tailwind for CSS. If you've never used it before, or maybe only heard
of it, you might... hate it at first. That's because you use classes inside of
HTML to define *everything*. And so your HTML can end up looking, well, a bit
crazy. But give it a chance. I've absolutely fallen in love with it. And, instead
of this looking ugly to me, it looks descriptive.

## Tailwind Requires Building!

Tailwind isn't your traditional CSS behemoth where you download a giant CSS file
and include it. Instead, Tailwind has a binary that parses all of your templates,
finds the classes you're using, and then dumps a final CSS containing *just* those
classes. So it keeps your final CSS as small as possible.

But to do this, duh duh duh! Tailwind requires a *build* step. And that's okay. Just
because we don't have a build step for our *entire* JavaScript system doesn't mean
we can't opt *into* a small one for a specific purpose.

## Installing symfonycasts/tailwind-bundle

And there's a super-easy bundle to help us do this with AssetMapper. It's called
`symfonycasts/tailwind-bundle`. Hey, I've heard of them!

Head down here, go to the documentation... and I'll copy the `composer require` line.
Spin over and run that:

```terminal-silent
composer require symfonycasts/tailwind-bundle
```

This bundle will help us run the build command in the background and
serve up the final file. We point it at a real CSS file, then it'll sneak in
the dynamic content. You'll see.

While we're here, run:

```terminal
php bin/console debug:config symfonycasts_tailwind
```

to see the default configuration for the bundle. By default, the file that it
"builds" is `assets/styles/app.css`... which is *great* because we already
have an `assets/styles/app.css` file!

To get things set up, run a command from the bundle:

```terminal
php bin/console tailwind:init
```

This downloads the Tailwind binary in the background, which is awesome. That binary
is standalone and doesn't require Node. It just works. The command also did two
other things. First: it added the three lines needed inside of `app.css`:

[[[ code('e4b6a191cd') ]]]

When this file is built, these will be replaced by the actual CSS we need.
Second, this created a `tailwind.config.js` file:

[[[ code('75b1715df7') ]]]

This tells Tailwind *where* to look for all the classes we'll use. This will find
any classes in our JavaScript files or our templates.

To *execute* Tailwind, run:

```terminal
php bin/console tailwind:build -w
```

For *watch*. That builds... then hangs out, waiting for future changes.

So... what did that do? Remember: we're already including `app.css` on our page.
When we refresh, woh! It looks a bit different! The reason is, if you view the page
source, and click to open the `app.css` file, it's full of Tailwind code! Our
`app.css` file is no longer this exact source file! Behind the scenes, the Tailwind
binary parses our templates, dumps a final version of this file, which
is then returned. This file already contains a bunch of code because I filled the
CRUD templates with Tailwind classes before we started the tutorial.

## Using Tailwind

But let's see this in action for real. If we refresh the page, this is my `h1`.
It's small and sad. Open `templates/main/homepage.html.twig`. On the
`h1`, add `class="text-3xl"`:

[[[ code('35feee1337') ]]]

Now, refresh. It works! If that `text-3xl` wasn't in the `app.css` file before,
Tailwind just added it because it's running in the background.

## Pasting in The Layout

So Tailwind is working! To celebrate, let's paste in a proper layout. If you
downloaded the course code, you should have a `tutorial/` directory with a couple
of files. Move `base.html.twig` into templates:

[[[ code('21bde93dda') ]]]

And these other two go into the `main/` directory:

[[[ code('b02ad450ef') ]]]

[[[ code('4e5a7e2a38') ]]]

Refresh. Huh, no difference. That's because, at least on a Mac, because I moved
and overwrote those files, Twig didn't notice that they were updated... so its cache is
out-of-date.

Open a fresh terminal tab and run:

```terminal
php bin/console cache:clear
```

Then... woo! Welcome to Space Inviters! Styled up and ready to go! But there's
nothing special about the new templates. We *do* have a list of voyages...
but it's all boring, normal Twig code with Tailwind classes.

## Referencing Assets Dynamically

We do have some broken planet images though. To fix those, go into the
`tutorial/assets/` directory... and move all of those planets into `assets/images/`.
Delete the `tutorial/` folder.

That broken `img` tag comes from the `_planet_list.html.twig` partial. Here it is:

[[[ code('ea4148e2f8') ]]]

I left it for us to finish! How nice of me! We know that we can do `{{ assets() }}`
then something like `images/planets-1.png`. That would work. But this time, the
`planet-1.png` part is a dynamic property on the `Planet` entity. So, instead
say `~` then `planet.imageFilename`:

[[[ code('f0b3e3e8d6') ]]]

And... pretty! Yea, I know that's not what Earth and Saturn look like - I've
got some randomness in my fixtures - but they're fun to look at!

## Using KnpTimeBundle

Since day 6 is the "making everything look awesome day", let's do two more things.
To start, I don't love this date. It's boring! I want a cool looking date.

So, install one of my favorite bundles:

```terminal
composer require knplabs/knp-time-bundle
```

This gives us a handy `ago` filter. So as soon as this finishes, spin over and open
`homepage.html.twig`. Search for `leaveAt`, here we go. Replace that `date` filter
with `ago`:

[[[ code('d9d5d4b100') ]]]

And... *much* cooler!

What else? Go check out the CRUD areas. These were generated via MakerBundle...
but... I *did* preload them with Tailwind classes so they look good.
Wow, there is a *lot* of celebrating right now. I'm not complaining.

But... if you go to a form, it looks terrible! Why? The form markup comes from
Symfony's form theme... which outputs the fields *without* Tailwind classes.

## Flowbite for Tailwind Examples

So what do we do? Do we need to create a form theme? Fortunately, no. One of the
great things about Tailwind is there's an entire ecosystem set up around it. For
example, Flowbite is a site with a mixture of open source examples and pro, paid
features. On the open source side of things, you can, for example, find an "Alert"
page with different markup to make great-looking alerts. And, you don't need to
install anything with Flowbite. These classes are all native Tailwind. You can
copy this markup into your project and refresh. Nothing special. And I *love* it.

***TIP
Flowbite *does* also come with some JavaScript & a Tailwind Plugin. Check out the
[bonus chapter](https://symfonycasts.com/screencast/last-stack/flowbite) for the details!
***

This also has a forms section where it shows how we can make forms look really nice.
In theory, if we could make our forms output like this, they would look great.

## Adding a Tailwind Form Theme

And fortunately, there's a bundle that can help us. It's called
`tales-from-a-dev/flowbite-bundle`. Click "Installation" and copy the `composer
require` line. Then run it:

```terminal
composer require tales-from-a-dev/flowbite-bundle
```

We're getting a lot of stuff installed today! This asks if we want to install the
contrib recipe. Let's say yes, permanently. Cool!

Back on the installation instructions, we don't need to register the bundle - that
happens automatically - but we *do* need to copy this line for the tailwind
configuration file.

Open up `tailwind.config.js`, and paste that:

[[[ code('f9fbd3886e') ]]]

This bundle comes with its own form theme template with its own Tailwind classes.
So we want to make sure that Tailwind *sees* those and outputs the CSS for them.

The last step over on the docs is to tell our system to *use* this form theme by default.
This happens over in `config/packages/twig.yaml`. I'll paste... then fix the indentation:

***TIP
Starting in version 0.4.0, use `@TalesFromADevFlowbite/form/default.html.twig`.
***

[[[ code('53a05536d6') ]]]

That's it. Go back, refresh and eureka! In just over 10 minutes, we installed
Tailwind and started using it *everywhere*.

Tomorrow, we'll turn back to JavaScript and leverage Stimulus to write reliable
JavaScript code that we can love.
