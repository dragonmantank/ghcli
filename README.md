# ghcli
Github CLI Interface that works with hosted Github Enterprise or real Github.

Currently it just allows you to very crappily view and read issues in a specific repository. Eventually this will allow
you to view, respond, and work with issues in projects on both hosted Github Enterprise as well as the normal public
Github.

## Developing

Docker Compose is recommended for working on this project, as it has a bunch of helpers for building the PHAR and stuff.

1. Clone this repository
2. Run `export UID` to fix an issue with Docker Compose not finding your User ID
3. Run `docker-compose run composer install` to install PHP dependencies
4. Generate an access token on [https://github.com/settings/tokens](https://github.com/settings/tokens) and give it full `repo` permissions
5. Copy `ghcli.dist` to `ghcli`, and edit it with the access token and the repo you are testing against
6. Run `docker-compose run cli` to run the app

### Building the PHAR

You can build a phar by running `docker-compose run build_phar`, and it will generate a `ghcli.phar` file. 