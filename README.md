# 30 Days with LAST Stack

Well hi there! This repository holds the code and script for the
[30 Days with LAST Stack](https://symfonycasts.com/screencast/30-days-last)
course on SymfonyCasts.

## Setup

If you've just downloaded the code, congratulations!!

To get it working, follow these steps:

### Download Composer dependencies

Make sure you have [Composer installed](https://getcomposer.org/download/)
and then run:

```
composer install
```

### Database Setup

The code comes with a `compose.yaml` file (for Docker) and we recommend using
Docker to boot a database container. You will still have PHP installed
locally, but you'll connect to a database inside Docker. This is optional,
but I think you'll love it!

First, make sure you have [Docker installed](https://docs.docker.com/get-docker/)
and running. To start the container, run:

```
docker compose up -d
```

Next, build the database and the schema with:

```
# "symfony console" is equivalent to "bin/console"
# but its aware of your database container
symfony console doctrine:database:create --if-not-exists
symfony console doctrine:schema:create
symfony console doctrine:fixtures:load
```

If you're using something other than Postgresql, you can replace
`doctrine:migrations:migrate` with `doctrine:schema:update --force`.

If you do *not* want to use Docker, just make sure to start your own
database server and update the `DATABASE_URL` environment variable in
`.env` or `.env.local` before running the commands above.

### Start the Symfony web server

You can use Nginx or Apache, but Symfony's local web server
works even better.

To install the Symfony local web server, follow
"Downloading the Symfony client" instructions found
here: https://symfony.com/download - you only need to do this
once on your system.

Then, to start the web server, open a terminal, move into the
project, and run:

```
symfony serve
```

(If this is your first time using this command, you may see an
error that you need to run `symfony server:ca:install` first).

Now check out the site at `https://localhost:8000`

Have fun!

## Have Ideas, Feedback or an Issue?

If you have suggestions or questions, please feel free to open an issue
on this repository or comment on the course itself. We're watching both :).

## Thanks!

And as always, thanks so much for your support and letting us do what we love!

<3 Your friends at SymfonyCasts
