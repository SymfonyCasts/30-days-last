# Data Tables

Coming soon...

So our data tables setup is working. It's almost awesome. What I don't love is how it
jumps around. Every time we click a link, it's... jumping back to the top of the
page. And that's because Turbo is making a full page reload, and whenever Turbo makes
a full page reload, it scrolls to the top of the page, because that's usually what we
want. But not in this case. I really want our data table to almost work like a little
application, where it just changes right here without moving the screen around. A
really great way to do this is just to surround this entire table area with a Turbo
frame. So check this out. In homepage.html.twig, let's find the table. Here we go. So
this div right here really represents the entire table. So above the div, we're going
to say Turbo-frame ID equals voyage-list. Then I will indent this div. And then also
these pagination links down here. We also want those to be inside of the Turbo frame
as well, so that when we click on them, it moves the... it advances the frame. All
right, let's try this out. I'll clear my search. And oh, beautiful. Look at that.
Everything moving within the frame. Try pagination. Beautiful. The reason this works
is that all of our links are going back to the homepage and the homepage, of course,
has this frame. Now, just remember, now that we have this inside of a Turbo frame, if
we had any links inside of here, they might stop working. So again, the way to fix
that is on those links to add data-Turbo-frame equals top. Or if you want to be a
little bit more conservative, you can go up to the new Turbo frame we added and add
target equals underscore top. And then if you do that, you just need to go down to
your sorting links and your pagination links and add data-Turbo-frame equals
voyage-list right there, so that those do target the frame. But I will remove those
because we do not have that problem in this case. So at this point, the pagination
links, the sorting links, all working perfectly. But watch this. When I search here,
the search is still a full page reload. And that makes sense. I didn't put that
inside of the Turbo frame. Why? Because it wouldn't have fixed our problem. Because
when we type into it, it still would have reloaded the form, which still would have
reset my cursor position every time I type. So I don't want my form to reload. I want
it to just stay on the page statically. But when my form submits, I do want to
trigger the table to navigate. And we can do that. Up here on the form element that
we submit, add data-Turbo-frame equals voyage-list. This is a really powerful
concept. Even though this isn't inside of that Turbo frame, we can still target that
Turbo frame to navigate it. So now when we refresh, watch this. Beautiful. It
navigates and it's no longer taking away my control because that's not being
reloaded. So I love this. This is gorgeous. And now we have time to make things extra
fancy. So what about adding some sort of a loading indicator as we're navigating
between pages? Well, to make this super obvious, let's go to our controller and add a
sleep for one second. So now you can see it's really not very responsive. It's slow,
but we're also not even getting any user feedback that it's doing anything. So how
can we add some sort of loading indication? This is a spot where Turbo has our back.
So here's the Turbo frame element, and I want you to watch the attributes at the end
when I navigate. Boom. See that? There was an area-busy equals true that Turbo adds
while it's navigating. That is there for accessibility, but it's also something that
we can leverage very easily with Tailwind to add some loading indicator. So over on
that Turbo frame element, here we go. Say class equals, and here we can say area-busy
colon opacity 50. So what's happening here is this is a special thing that says only
if this element has an area-busy attribute should we apply the opacity 50. So we can
also do another one, area-busy colon, and let's say blur-sm. That's going to blur the
background. That's really cool. And I'll also say transition all to transition the
opacity and the blur so it's not too abrupt. Abrupt. All right. So I'll refresh that
and watch. Oh, beautiful transitions just by adding a couple of classes. Now, as
beautiful as that is, it's a little slow. So in main controller, let's remove our
sleep. Now, at this point, there's only one big, gigantic, horrible problem, and that
is that when we search or when we sort or when we paginate, there is nothing
happening up here on the URL. This is just static. So if I just go to the page like
this, the URL is never changing. And that's the default behavior of Turbo frames.
When they navigate, they don't update the URL. However, we can tell Turbo that we do
want to update the URL. To do that on the Turbo frame, we're going to add
data-turbo-action equals advance. So what advance means is do update the URL and
update it in such a way that if we hit the back button later, it will go to the
previous search. So watch this. This time if I do AU, great, that works. I can clear
that out. Let's go to page two, page three. Now, if I go back, you can see it goes
back to page two, back to page one, exactly how we want. And now this is a truly
sweet data tables setup, entirely written with no JavaScript inside of Twig and
Symphony. Really, the last problem here is this 30 results. So notice as we search,
that never changes. It's just stuck on whatever was there when the original page
loaded. That's because this lives outside of our Turbo frame. The easiest way to fix
that would be just to move this into the Turbo frame. But I don't really want it
there. I want it visually up here. So we're going to leave that for now. But that's
something that can be fixed by Turbo streams, a topic we'll talk about in a few days.
And we will eventually get that updating. Alright, next tomorrow, we're going to dive
into a brand new feature of browsers in general called view transitions. These allow
smooth visual transitions that we can apply to any navigation or content change of
any kind. It's super sweet.
