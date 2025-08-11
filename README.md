<h1>Yii Simple Url Reduce</h1>
<br>

Simple project on Yii 2 - link shortener + tracking of transitions by url.


Structure
-------------------

      ├─ docker/
      │  ├── nginx/         # config nginx
      │  ├── php/           # config php (with xdebug)
      │  ├── yii2/          # config Yii2 (db.php, params.php)
      ├─ logs/              # nginx logs
      ├─ web/               # projects
      ├─ .env.example       # params (.env)
      ├─ docker-compose.yml


Require
------------

- PHP 8.1
- Composer 2.8


Run
------------

1. Clone project: 
```shell
  git clone https://github.com/Star-Street/Yii-UrlReduce.git
```
2. Rename file ".env.example" to ".env"
3. Configure file ".env"
4. Start the container:
```shell
  docker-compose up -d --build
```
5. Start the console command (migrations + queue worker):
```shell
  docker exec -it yii2-php sh -c "chmod +x /var/www/html/init.sh && /var/www/html/init.sh"
```
6. Following URL: http://localhost:8080


SQL query
------------

```mysql
SELECT month, 
       url, 
       visit_count, 
       rank_in_month 
FROM ( 
    SELECT month, 
           url, 
           visit_count, 
           @rank := IF(@current_month = month, @rank + 1, 1) AS rank_in_month, 
           @current_month := month AS dummy 
    FROM ( 
        SELECT DATE_FORMAT(v.visited_at, '%Y-%m') AS month, 
               s.original_url AS url, 
               COUNT(*) AS visit_count 
        FROM link_visits v JOIN short_links s ON v.short_link_id = s.id 
        GROUP BY month, url 
        ORDER BY month DESC, visit_count DESC ) AS subquery 
    CROSS JOIN (SELECT @rank := 0, @current_month := '') AS vars ) AS ranked;
```