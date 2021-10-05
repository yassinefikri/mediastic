# Installation
## Setting up containers
```
HOST_UID=${UID} docker-compose up
```

## Accessing packages managers && installing dependencies
```
source commands.sh
composer install
console doctrine:migrations:migrate
yarn
dump-routes
yarn encore production
```

# Usage
## Links

> Application => http://localhost:8082  
> PhpMyAdmin => http://localhost:8083  
> MercureHub => http://localhost:8081  

You can access to mysql through cli by
```
mysql
```

For dev env, you can use this command to recompile assets automatically when files change
```
yarn encore dev --watch
```
