@echo off
doskey composer=docker exec -it --user www-data mediastic_php_apache2 sh -c "COMPOSER_MEMORY_LIMIT=-1 composer $*"
doskey console=docker exec -it --user www-data mediastic_php_apache2 sh -c "php bin/console $*"
doskey mysql=docker exec -it mediastic_mysql sh -c "mysql --user=root --password=root --database symfony-vue"
doskey yarn=docker exec -it --user node mediastic_webpack sh -c "yarn $*"
doskey phpstan=docker exec -it --user www-data mediastic_php_apache2 sh -c "vendor/bin/phpstan analyse src tests --level 8"
doskey dump-routes=docker exec -it --user www-data mediastic_php_apache2 sh -c "php bin/console fos:js-routing:dump --format=json --target=public/js/fos_js_routes.json"
