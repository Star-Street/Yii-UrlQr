<h1>Yii Simple Url Reduce with QR generation</h1>
<br>

Simple project on Yii2 - link shortener + QR generation images.


Structure
-------------------

      ├─ docker/
      │  ├── nginx/              # config nginx
      │  ├── php/                # config php (with xdebug)
      │  ├── yii2/               # config Yii2
      ├─ logs/                   # nginx logs
      ├─ web/                    # projects
      ├─ .env.example            # params (.env)
      ├─ docker-compose.yml


Require
------------

- PHP 8.1
- Composer 2.8
- MySQL 5.7


Features
------------

- [x] Validate URL
- [x] Check URL for availability
- [x] Generate QR image
- [x] Generate short URL
- [x] Internal URL redirect to address
- [x] Logs (IP, qty)


Run
------------

1. Clone project:
```shell
  git clone https://github.com/Star-Street/Yii-UrlQr.git
```
2. Rename file `.env.example` to `.env`
3. Configure file `.env`
4. Start the container:
```shell
  docker-compose up -d --build
```
5. Start the console command (migrations):
```shell
  docker exec -it yii2-php sh -c "chmod +x /var/www/html/init.sh && /var/www/html/init.sh"
```
6. Following URL: http://localhost:8080