@echo off
doskey composer=docker exec -it --user www-data symfony-vue_php-apache2_1 sh -c "COMPOSER_MEMORY_LIMIT=-1 composer $*"
doskey console=docker exec -it --user www-data symfony-vue_php-apache2_1 sh -c "php bin/console $*"
doskey mysql=docker exec -it symfony-vue_mysql_1 sh -c "mysql --user=root --password=root --database symfony-vue"
doskey yarn=docker exec -it --user node symfony-vue_webpack_1 sh -c "yarn $*"
doskey phpstan=docker exec -it --user www-data symfony-vue_php-apache2_1 sh -c "vendor/bin/phpstan analyse src tests --level 8"
doskey dump-routes=docker exec -it --user www-data symfony-vue_php-apache2_1 sh -c "php bin/console fos:js-routing:dump --format=json --target=public/js/fos_js_routes.json"
