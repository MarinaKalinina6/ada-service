## database

* to start `docker compose up -d`
* to stop `docker compose down` (always stop before switching to a new project)

* see not applied schema changed `./bin/console doctrine:schema:update --complete --dump-sql`
* generate migration from not applied schema changes `./bin/console doctrine:migrations:diff`
* apply all not applied migrations `./bin/console doctrine:migrations:migrate -n`
