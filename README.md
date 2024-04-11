Laravel 11
PHP 8.3

Steps how to run project

Make .env file and add your database credentials
```sh
cp .env.example .env
```

Add following to .env file (change APP_URL)
```
APP_URL=http://0.0.0.0:8081
APP_PORT=8081 to .env file
```

Do composer install
```sh
composer install
```

Run project via Laravel Sail
```sh
./vendor/bin/sail up
```

Run migrations
```sh
./vendor/bin/sail artisan migrate
```
Run DB seed
```sh
./vendor/bin/sail artisan db:seed
```

Project api url
```
http://0.0.0.0:8081/api/v1
```

To see generated docs go to http://0.0.0.0:8081/docs

Here are api routes

User register:
```
/api/v1/register route

name: '',
last_name: ',
email: '',
password: '',
```

Login need and get access token:
```
/api/v1/login route

email: '',
password: '',
```

For below routes need to pass access token

Get users list:
```
/api/v1/users
```

Create chat:
```
/api/v1/chats/create

user_id: ''
name: ''
```

Get chats:
```
/api/v1/chats/get_chats
```

Send message:
```
/api/v1/chats/send_message

body: ''
chat_id: ''
```

Get messages by chat:
```
/api/v1/chats/get_messages/5
```
