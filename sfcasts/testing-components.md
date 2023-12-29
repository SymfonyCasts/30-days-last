# Testing Twig & Live Components

All these cool things we've been building are just goodies, unless we can test them.
So, that's today's mission! Lots to do, so let's get started.

Run:

```terminal
composer require phpunit
```

That installs the `symfony/test-pack`, will give us all the packages we need put
them into `require-dev`.

## Testing a Twig Component

For our first act, let's test a Twig component. This is pretty cool: we can create
the component object, call methods on it and assert how it's rendered, all in isolation.
It's pretty simple, but we'll test the `Button` component.

In the `tests/` directory, create an `Integration/` directory - because this will
be an integration test - then `Twig/Components`. If you're new to integration tests,
check our [Integration Testing tutorial](https://symfonycasts.com/screencast/phpunit-integration).

Inside, add new `Button` class and extend the normal `KernelTestCase` for integration
tests. To help us work with the component, use a trait called
`InteractsWithTwigComponents`, then add a new function:
`testButtonRendersWithVariants`.

## Mounting the Component

The trait gives us two methods. The first lets us *create* the component object.
Say `$this->mountTwigComponent()` passing the component name `Button` and any
props, like `variant` set to `success`.

This should give us a `Button`: `assertInstanceOf`, `Button::class`, `$component`.

Behind the scenes, this creates the component object using the normal process:
it mounts the props we pass onto properties and autowires any services. I'll
dump `$component` then `assertSame` `success` is equal to `$component->variant`.

Cool! To try this, run:

```terminal
./vendor/bin/simple-phpunit tests/Integration
```

That'll download PHPUnit, and... it passes! We have some deprecation notices, but
we'll ignore those.

## Rendering the Component

The second thing we can do is *render* a component. Copy the top, paste on the
bottom, rename this to `$rendered` and call `renderTwigComponent()`. This has almost
the same arguments, but you can also pass blocks. The third argument is a shortcut
to pass the `content` block.

Ok, dump `$rendered`... and let's see what this looks like!

```terminal-silent
./vendor/bin/simple-phpunit tests/Integration
```

Awesome! An object with the HTML inside. With this, we can get the raw string...
*or* we can access a `Crawler` object. This is cool,:
`$this->assertSame()` that `Click Me!`, is equal to `$rendered->crawler()`
`->filter()` to find the `span` then `->text()`.

Pretty sweet! My editor think there's a syntax error here, but it's crazy. Watch:

```terminal-silent
./vendor/bin/simple-phpunit tests/Integration
```

It passes!

## Testing a Live Component

So how about integration testing a live component... like our fancy `SearchSite`
component? In the same directory, create a new class called `SearchSiteTest`,
extend `KernelTestCase` and... this time use `InteractsWithLiveComponents`.
Create a method called `testCanRenderAndReload()`.

This this trait, we can say `$testComponent` equals `$this->createLiveComponent()`.
Pass the name - `SearchSite` - and  can also pass any props, but I won't: we'll
let the `$query` start empty. `dd($testComponent)`.

When we run this:

```terminal-silent
./vendor/bin/simple-phpunit tests/Integration
```

The object is *humongous*... but it's a `TestLiveComponent`. And this has a
*ton* of goodies. We can say `$testComponent->component()` to get the underlying
component object, we can render it and we can even mimic user behavior, like changing
a model value, calling live actions, emitting events or even logging in.

## Test Database Setup

Ok, to test the search, we need to add some voyages to the database. On top,
`use ResetDatabase` and `use Factories`.

Down here, use `VoyageFactory::createMany()` to create 5 voyages... and give them
all the same `purpose` so we can easily search for them. Then create one more
`Voyage` with any other random `purpose`.

Before we take advantage of these, try the test again:

```terminal-silent
./vendor/bin/simple-phpunit tests/Integration
```

A database connection error! I'm running the database via Docker & using the `symfony`
binary to set the `DATABASE_URL` environment variable. To inject that variable
when running the test, prefix the command with `symfony php`:

```terminal-silent
symfony php vendor/bin/simple-phpunit tests/Integration
```

And... we're back! One risky test because we don't have any assertions. Let's
add those!

Remember: if there is no `query`, our component returns no voyages. And in the
template: `templates/components/SearchSite.html.twig`, when we *do* have results,
each is an `a` tag.

In the test, `$this->assertCount()` that 0 is equal to
`$testComponent->render()`, then use that same `->crawler()` to filter for `a`
tags.

Here's the *really* cool part: call `$testComponent->set()` `query` to `first 5`
to mimic the user typing into the search box. And now we should have 5 results.

Do it!

```terminal-silent
symfony php vendor/bin/simple-phpunit tests/Integration
```

And... green! Ok, today is a bit unorthodox because... we're out of time but I
have more to say! So join me for part *two* where we take on some functional tests
for our JavaScript-powered frontend.
