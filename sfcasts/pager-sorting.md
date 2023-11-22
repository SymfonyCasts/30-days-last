# Pager Sorting

Coming soon...

Welcome to Day 13, where we take a pause with Stimulus and Turbo. We're just going to
work with Symfony and Twig today to add pagination to this list, as well as column
sorting. Now, the way I like to add pagination is via PagerFonta. I absolutely love
that bundle. Don't love its documentation, but hey, it's open source, so if you don't
love it, go fix it. To use PagerFonta, we're going to install three different
libraries. Babdev PagerFonta Bundle, PagerFonta slash Doctrine ORM, and PagerFonta
slash Twig. Doctrine ORM Adapter, PagerFonta slash Twig. Cool. Let's get this set up
in PHP first. So, go into Source Controller, Main Controller, and the first thing we
need to do is read the query parameter. So, the way this is going to work is we're
going to have ?page="1", ?page="2", so we need to read off the current page. I'm
going to do that with this really cool new map query parameter thing. And actually,
before, I was doing a little too much work. If your query parameter matches your
argument name, you do not need to specify it there. So, I'm going to remove that on
those two. It is different on this search planet, so that's a field we're going to
use a little bit later. Anyway, this is going to be an ?page, and we'll have it
default to 1. And the order of these doesn't matter. Down here, copy the
VoyageRepository find by search, and replace it with a Pager object. So, Pager equals
PagerFonta, colon, colon, create for current page with max per page. And the first
thing this needs is an adapter. So, for this, it's going to be new QueryAdapter,
since we're going to make a Doctrine query. And we're going to pass that a
QueryBuilder. So, I'll paste in what I had before, though that's not quite right,
because this find by search returned an array of voyages, and what we need is a
QueryBuilder. Fortunately, I already set things up so that we can get this same
query, but in QueryBuilder format via this method down here, find by search
QueryBuilder. So, I will paste that method name in, and now it's happy. The next
argument is the current page, that's page, and then max per page, how about 10? Cool.
And then we can pass Pager in as the voyages variable, and that should just work
because we can loop over that to run the voyages. Next up, inside of
homepage.html.twig, we need to render the Pagination links. Down at the bottom, I
already have a spot for this. I have kind of a hard-coded previous and next. The way
you're supposed to render the PagerFonta links is by saying curly curly PagerFonta
and then passing in voyages. So, when we try this, let me clear my search out. The
Pagination looks awful, but it's working. That actually goes through the results, and
the results are changing as we do them. Awesome. So, how do we make these Pagination
links not so ugly? Well, there is a built-in Tailwind template that you can configure
PagerFonta to use. That involves creating a babdev PagerFonta.yaml file, setting the
default view to twig, and then pointing the default twig template at babdev
PagerFonta.html.twig. However, I want to be stubborn. I want to just have a previous
next button. I want to style it exactly how I want to style it, so I'm going to go
rogue and do whatever I want to do. So, the first thing I need to do is render these
links conditionally, only if there is a previous page. So, to do that, we can say if
voyages.hasPreviousPage, then we'll render that. The next thing is, if we have a next
page, we'll render that. It's probably the correct text, but I'll just be careful.
Perfect. Then, for the actual URLs, we can use a little helper called
PagerFontaPageURL. We pass to it the pager, voyages, and then which page we want to
go to. In this case, it's voyages.previousPage. I'll copy that. We'll put it down
here. We'll say voyages.nextPage. Lovely. So, let's give that a try. When we refresh,
awesome. The previous page is missing. We click next. It's there. Click next again.
Page three is the last one. It's working beautifully. For a little extra fanciness
down here, I'll even print the current page. With voyages.currentPage. Then, I'll do
a little slash. It's not number of pages. Actually, that is correct. Number of pages.
Good job, AI. There we go. Page one of three. Page two of three. Love it. What about
sorting? We want to be able to click these columns and have them sort. For this,
we're going to need two new query parameters up here. A sort column name and a sort
direction. Back to PHP to make this happen. Map query parameter. The first thing we
need is going to be a string sort equals. We'll default it to leaveAt. That's the
property name for this departing column. Then, we're going to go map query parameter
again. We need sort direction. We'll default it to ascending. Down below in the
actual method, I'm going to paste in two really boring lines here. This is just
validating that someone's passing in a real sort column and not something we didn't
expect. Then, the findBySearchQueryBuilder, this is actually already set up to expect
some sort arguments. We can pass sort and sort direction. It should be happy.
Finally, we're going to need this information in the template so that we can render
the correct links. I'm going to pass a sort, set to sort, and a sort direction set to
sort direction. Probably the most boring part of this comes next. That's transforming
these th's into proper links. Here, I'm going to add an a tag and immediately break
this onto multiple lines because we have a little bit of work here to do. I'll put
purpose there. We want this to link back to the homepage. I can say pathApp
underscore homepage. Of course, we're going to want sort set to purpose. Then, down
here, sort direction is going to be a little bit complicated. If the sort equals
purpose and the sort direction equals ascending, then we'll want descending.
Otherwise, we want to do ascending by default. However, it's not quite this simple
because in addition to the sort and sort direction query parameters, we want to keep
any existing query parameters that might be on there. The cool thing is there's a
nice way to do that. Dot, dot, dot, app.request.query.all. That's it. Now, down here
for purpose, I also want to have a nice little down arrow or up arrow here. To help
with that, I'm going to use a twig macro. I don't always love twig macros, but in
this case, I have just a quick twig macro that says if the sort direction is
ascending, print a down arrow. Else, print an up arrow. If you're not sorting by this
column, then print both an up and down arrow. Down here... Let's get rid of all that
stuff. Here we go. We can say curly curly self.sortdirection. Nope. self.sortarrow
and let that fill in passing purpose. sort and sort direction. Just like that. Now,
I'm going to repeat all of that down here for departing. You can see this is all
relatively straightforward. It is a bit of work to get this set up. Could we have
some tools in the Symfony world to make this all a lot easier to build? Probably.
That might be a cool thing for someone to work on, but at least we can build this
super straightforward. Change purpose to leave at. I'll say departing. Then we just
need to change leave at here and leave at here. Hopefully, that is it. All right,
moment of truth. Refresh. Okay, that looks good. Ah, love it. We can sort by that. We
can sort by departing. We can paginate to page three. Notice it's keeping our page.
It's keeping our search filter. Life is good, and it's all happening via AJAX.
Really, the only problem now is that it's a little bit jarring to have that full page
refresh every single time. It really wants us to feel like a standalone thing that's
not jumping around. Let's do that next with turbo frames.
