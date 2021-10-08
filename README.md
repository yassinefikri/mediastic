# Installation
## Setting up containers & Accessing packages managers
### For Linux
```
HOST_UID=${UID} docker-compose up
source commands.sh
```
### For Windows
```
docker-compose up
cmd.exe /K commands.cmd
```

## Installing dependencies
```
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
