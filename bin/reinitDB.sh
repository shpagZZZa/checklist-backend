docker-compose exec db mysql -uroot -proot -e "drop database project";
docker-compose exec db mysql -uroot -proot -e "create database project";
docker-compose exec app bin/console bin/console doctrine:migrations:migrate
