# THIO Backend

### How to setup

Build docker container

```
cd ~/path-to-project/simnal-backend
docker-compose up -d
```

Open terminal inside docker and run:

```
php artisan migrate
```

Seed new user

```
php artisan db:seed --class=UsersSeeder
```

Access phpmyadmin

```
http://localhost:8080
```

API Endpoint

```
http://localhost:8000
```
