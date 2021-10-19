# Laravel command line finit state machine project

You can clone this repository and simply create any finit state machine app of your own by using the sample code provided.

Sample code is a QA console app made with Laravel + Artisan

# Prerequisites

You have to have `docker` and `docker-composer` installed on your system in order to run this project.

---
# Up and Running
1. clone the repository
    ```shell script
    git clone git@github.com:rasadeghnasab/QA
    ```

2. cd to the repository directory
    ```shell script
    cd QA
    ```
    
3. up and run the project
    ```shell script
    make install
    ```
    - Note: You can use `sudo make install` if it gives you any permission error

4. wait for all the services to be up. You can check them by the command below
    ```shell script
    make status
    ```

5. if all the services were up, now you can migrate tables
    ```shell script
    make migrate
    ```
   
6. you can run the Q&A by using these two commands. pass `--with-password` flag if you need full authentication.
    ```shell script
    # login with email
    ./vendor/bin/sail artisan qanda:interactive

    # login with email and password
    ./vendor/bin/sail artisan qanda:interactive --with-password
    ```

---
### Existing user:

- test@test.com
- password

---
# How to use

- default behavior: only ask for email. It will create a user for your if it doesn't exist already.

```shell script
vendor/bin/sail artisan qanda:interactive
```

- full credential required. It will ask for your email and password.
```shell script
vendor/bin/sail artisan qanda:interactive --with-password
```
