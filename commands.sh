#!/bin/bash

unalias composer 2>/dev/null >/dev/null || true
composer() {
  docker exec -it symfony-vue_php-apache2_1 sh -c "COMPOSER_MEMORY_LIMIT=-1 composer $*"
}
export -f composer

unalias console 2>/dev/null >/dev/null || true
console() {
  docker exec -it symfony-vue_php-apache2_1 sh -c "php bin/console $*"
}
export -f console

unalias mysql 2>/dev/null >/dev/null || true
mysql() {
  docker exec -it symfony-vue_mysql_1 sh -c "mysql --user=root --password=root --database symfony-vue"
}
export -f mysql

unalias yarn 2>/dev/null >/dev/null || true
yarn() {
  docker exec -it symfony-vue_webpack_1 sh -c "yarn $*"
}
export -f yarn

unalias phpstan 2>/dev/null >/dev/null || true
phpstan() {
  docker exec -it symfony-vue_php-apache2_1 sh -c "vendor/bin/phpstan analyse src tests --level 8"
}
export -f phpstan

unalias dump-routes 2>/dev/null >/dev/null || true
dump-routes() {
  docker exec -it symfony-vue_php-apache2_1 sh -c "php bin/console fos:js-routing:dump --format=json --target=public/js/fos_js_routes.json"
}
export -f dump-routes
