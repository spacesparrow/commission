# Commission Task

## System requirements
* Docker

## Installation
1. Execute:
```shell
docker-compose build
docker-compose up -d
docker-compose exec app composer install
docker-compose exec app composer run post-root-package-install
```
2. Set API URL at `.env` file with value from the task description.
```shell
CURRENCY_API_URL=https://api.example.com
```

## Execution
```shell
docker-compose exec app php index.php input.example.csv
```

## Testing
### For running PHPUnit and PHP CS Fixer
```shell
docker-compose exec app composer test
```
### For running only PHPUnit
```shell
docker-compose exec app composer phpunit
```
### For running only PHP CS FIXER
```shell
docker-compose exec app composer test-cs
```
