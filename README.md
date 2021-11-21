как запустить 

из корня проекта docker-compose up -d

ждем..

когда команда завершилась, переходим на localhost:8000/healthcheck и предположительно видим сообщение что все ок

ежели все действительно ок, выполняем docker-compose exec app bin/console doctrine:migrations:migrate (выполнить нужно ток при самом первом запуске)

на этом халас, пляшем
