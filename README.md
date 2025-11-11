# Deep Links (/b/{id})

### Run commands to populate the database and run tests.
Sail \(preferred\):
- Start Sail: `./vendor/bin/sail up -d`
- Recreate DB and seed: `./vendor/bin/sail artisan migrate:fresh --seed`

Inside the app container:
- Open a shell: `docker exec -it deep-link-resolver bash`
- Recreate DB and seed from inside the container: `php artisan migrate:fresh --seed`

If not using Sail:
- Ensure your local environment is set up with a running MySQL instance.
- Recreate DB and seed: `php artisan migrate:fresh --seed`

### Running Tests

Run tests against MySQL (inside the Docker app container)
- Preferred (container running): run PHPUnit inside the container so the DB host `mysql` resolves:
    - Enter shell:
        - `./vendor/bin/sail up -d`  (Laravel Sail)
        - `docker exec -it deep-link-resolver bash`
        - `./vendor/bin/phpunit -c phpunit.mysql.xml --filter DeepLinkTest --testdox`
    - Or one‑liner (when container is running):
        - `docker exec deep-link-resolver ./vendor/bin/phpunit -c phpunit.mysql.xml --filter DeepLinkTest --testdox`

Run tests locally using SQLite (no Docker)
- Use the local PHPUnit binary; ensure your PHPUnit config or `.`env.testing` selects sqlite:
    - `./vendor/bin/phpunit --filter DeepLinkTest --testdox`

Notes
- Use `-c` to select a non-default PHPUnit config file (for example `phpunit.mysql.xml`).
- Running inside the container is required when `DB_HOST` is set to `mysql`.
