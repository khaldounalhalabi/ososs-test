# Backend Laravel Developer Position Technical Test

Hello there, I am Khaldoun Alhalabi

## requirements:

php 8.2 , composer , sqlite or mysql

## How to set up the project

1. install dependencies via :
    ```bash
   composer install
   ```
2. copy .env.example to .env file
    ```bash 
   cp .env.example .env
    ```

3. configure your database connection within the .env file (by default it is **sqlite** and it can do the job)

4. replace the default mail server config in the .env file with yours (this step is optional so you can test the reset
   password functionality)

5. generate encryption key:
    ```bash
   php artisan key:generate
    ```
6. generate jwt encryption key:
    ```bash
   php artisan jwt:secret
    ```
7. run the project migrations and seeders
    ```bash
   php artisan migrate:fresh --seed
   ```
8. import the postman collection and start testing (use admin@email.com|123456789 for an admin account and
   customer@email.com|123456789 for a customer account)

Thanks for your time.
