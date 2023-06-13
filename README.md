# [MTEC](http://mtec.by/ru/) Library app

This is a diploma for [Mark Laurenchikas](https://vk.com/lemurlaur)

## Project overview

This project is built with [`Symfony`](https://symfony.com/) and [`Vue`](https://vuejs.org/) powered by [`Vite`](https://vitejs.dev/https://vitejs.dev/) and [`Docker`](https://www.docker.com/).

Documentation:

* [ERD Diagram](./docs/ERD.png)

## Requirements

* [`Docker`](https://www.docker.com/) - to run the project.
* [`npm`](https://www.npmjs.com/) - for syntax highlight and to run frontend in `dev` environment
* [`Composer`](https://getcomposer.org/) - for syntax highlight
* [`GNU Make`](https://www.gnu.org/software/make/) - to run some commands

## Install dependencies

```sh
make vendor install # Install npm and composer dependencies
```

## Run the project (for dev)

```sh
make start # Build and start docker image
npm run dev # Run vite dev (Vite dev was not containerised because of performance issues related to wsl2)
```

## Run the project (for staging)

```sh
make serve # Build and start docker image for staging env
make serve-load-fixtures # Load some mock data
```

## Stop the project

```sh
make down # Stop the docker hub
```

----------------

You can see more commands by running `make` command without arguments.
