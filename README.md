# ebooks

## Docker

1. Клонировать проект
1. Проверить `.env`, если нужно создать `.env.local` или заменить
1. Запустить `docker-compose up --build` или `docker-compose up -d`
1. Запустить
    * Для `dev` окружения, запустить `composer install`
    * `bin/console doctrine:database:create`
    * `bin/console  doctrine:migrations:migrate`
    
## Примеры книг
`./samples/`
