# Laravel command line Q&A

Q&A app made with Laravel + Artisan

# Prerequisites

You have to have `docker` and `docker-composer` installed on your system in order to run this project.

---
# Up and Running
1. clone the repository
    ```bash
    git clone git@github.com:rasadeghnasab/QA
    ```

2. cd to the repository directory
    ```bash
    cd QA
    ```
    
3. up and run the project
    ```bash
    make install
    ```
    - Note: You can use `sudo make project` if it gives you any permission error
---
### Existing user:

- test@test.com
- password

---
# How to use

- default behavior: only ask for email. It will create a user for your if it doesn't exist already.

```bash
vendor/bin/sail artisan qanda:interactive
```

- full credential required. It will ask for your email and password.
```bash
vendor/bin/sail artisan qanda:interactive --with-password
```

---
# A quick note for the reviewers

- I wrote test functions name in a snake_case type to be more readable despite that breaks the PSR-12 rules.
