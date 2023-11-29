# Pagination & Column Sorting

Welcome to Day 13! We're going to tap the breaks on Stimulus and Turbo and only
work with Symfony and Twig today. Our goal is to add pagination and column sorting
to this list.

## Adding Pagination

I like to add pagination with PagerFanta. I *love* this library, though
I do get a bit lost in its documentation. But hey: it's open source, if you're not
happy, go fix it!

To use PagerFanta, we'll install *three* libraries:

```terminal
composer require babdev/pagerfanta-bundle pagerfanta/doctrine-orm-adapter pagerfanta/twig
```

Cool beans! Let's get the PHP side working first. Open `src/Controller/MainController.php`.
The current page will be stored on the URL as `?page=1` or `?page=2`, so we need
to *read* that `page` query parameter. I'll do that with a cool newish
`#[MapQueryParameter]` attribute. And actually, before... I was doing too
much work. If your query parameter matches your argument name, you don't need to
specify it there. So, I'll remove it on those two. It *is* different for `searchPlanet`:
a parameter we'll use later.

Anyway, this will read the `?page=` and we'll default it to 1. Oh, and the order
of these doesn't matter.

Below, copy the `$voyageRepository->findBySearch()` line, and replace it with a Pager
object: `$pager` equals `PagerFanta::createForCurrentPageWithMaxPerPage()`.
The first argument is an adapter: new `QueryAdapter` then paste in the code from
before. But, that's not quite right: this method returns an array of voyages, but
we now need  a `QueryBuilder`. Fortunately, I already set things up so that we
can get this same result, but as a `QueryBuilder` via: `findBySearchQueryBuilder`.
Paste that method name in.

The next argument is the current page - `$page` - then max per page. How about
10? Pass `$pager` to the template as the `voyages` variable.

That... should just work because we can loop over `$pager` to get the voyages.

## Rendering the Pagination Links

Next up, in `homepage.html.twig`, we need pagination links! Down
at the bottom, I already have a spot for this with hardcoded previous and next
links.

The way you're supposed to render PagerFanta links is by saying
`{{ pagerfanta() }}` and then passing `voyages`.

When we try this - let me clear my search out - the pagination looks awful... but
it *is* working! As we click, the results are changing.

So... how can we change these pagination links from "blah" to "ah"? There *is* a
built-in Tailwind template that you can tell PagerFanta to use. That involves
creating a `babdev_pagerfanta.yaml` file and a bit of configuration. I haven't
used this before - so let me know how it goes!

```yaml
babdev_pagerfanta:
    # The default Pagerfanta view to use in your application
    default_view: twig

    # The default Twig template to use when using the Twig Pagerfanta view
    default_twig_template: '@BabDevPagerfanta/tailwind.html.twig'
```

Because... I'm going to be stubborn. I want to *just* have previous & next buttons...
and I want to style them *exactly* like this. So let's go rogue!

The first thing we need to do is render these links conditionally, only if there
*is* a previous page. To do that, say if `voyages.hasPreviousPage`, then render.
And, if we have a next page, render *that*.

For the URLs, use a helper called `pagerfanta_page_url()`. Pass it the pager,
`voyages`, then which page we want to go to: `voyages.previousPage`.

Copy that, then repeat it below with `voyages.nextPage`.

Lovely! Let's give that a try. Refresh. Love it! The previous page is missing, we
click next, and it's there. Click next again. Page 3 is the last one. We got it!

For extra credit, let's even print the current page. Add a div... then print
`voyages.currentPage`, a slash and `voyages.nbPages`. Good job, AI!

And... there we go. Page one of three. Page two of three.

## Column Sorting

What about column sorting? I want to be able to click each column to sort by that.
For this, we need two new query parameters. A `sort` column name and
`sortDirection`. Back to PHP! Add `#[MapQueryParameter]` on a `string` argument
called `$sort`. Default it to `leaveAt`. That's the property name for this
departing column. Then, to go `#[MapQueryParameter]` again to add a string
`$sortDirection` that defaults to ascending.

Inside the  method, I'll paste 2 boring lines that validate that `sort` is
a real column. We could probably do the same for `$sortDirection`, but I'll skip
and go to `findBySearchQueryBuilder()`. This is already set up to expect the sort
arguments. So pass `$sort` and `$sortDirection`... and it should be happy!

Finally, we're going to need this info in the template to help render the sort
links. Pass `sort` set to `$sort` and `sortDirection` set to `$sortDirection`.

## Adding the Column Sorting Links

The most tedious part is transforming each `th` into the proper sort link. Add
an `a` tag and break it onto multiple lines. Set the `href` to this page - the
homepage - with an extra `sort` set to `purpose`: the name of this column.
For `sortDirection`, this is more complex: if `sort` equals `purpose`
and `sortDirection` is `asc`, then we want `desc`. Otherwise, use `asc`.

Finally, in addition to the `sort` and `sortDirection` query parameters, we need
to keep any *existing* query parameters that might be present - like the search query.
And there's a cool way to do this: `...` then `app.request.query.all`.

Done! Oh, but after the word Purpose, let's add nice down or up arrow.
To help, I'll paste a Twig macro. I don't often use macros... but this will
help us figure out the direction, then print the correct svg: a down arrow, an
up arrow, or an up and down arrow.

Down here... use this with `{{ _self.sortArrow() }}` passing `'purpose'`,
`sort` and `sortDirection`.

Phew! Let's repeat all of this for the departing column. Paste, change `purpose`
to `leaveAt`, the text to `Departing`... then use `leaveAt` in the other two spots.
So, all pretty boring code, though it *was* a bit of work to get this set up. Could
we have some tools in the Symfony world to make this all easier to build? Probably.
That would be a cool thing for someone to work on. 

Moment of truth! Refresh. That looks good. And it works *great*! We can sort
by each column... we can paginate. Filtering keeps our page... and keeps the search
parameter. It's everything I want! And it's all happening via Ajax! Life is good!

The only hiccup now? That awkward full page refresh whenever we do anything.
I want this to feel like a standalone app that doesn't jump around. Tomorrow:
we'll polish this thanks to Turbo Frames.
