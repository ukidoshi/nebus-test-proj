## REST API приложения для справочника Организаций, Зданий, Деятельности

------

**Реализованы методы:**
 - список всех организаций находящихся в конкретном здании +
 - список всех организаций, которые относятся к указанному виду деятельности +
 - список организаций, которые находятся в заданном радиусе/прямоугольной области относительно указанной точки на карте. список зданий +
 - вывод информации об организации по её идентификатору +
 - искать организации по виду деятельности. Например, поиск по виду деятельности «Еда», которая находится на первом уровне дерева, и чтобы нашлись все организации, которые относятся к видам деятельности, лежащим внутри. Т.е. в результатах поиска должны отобразиться организации с видом деятельности Еда, Мясная продукция, Молочная продукция. +
 - поиск организации по названию + 
 - ограничить уровень вложенности деятельностей 3 уровням + 

Поиск и фильтрация(+геопоиск) реализованы через движок meilisearch.

------

**Развернуть проект:**

Скопировать .env.example в .env(прописать в API_KEY рандомный ключ)

Запустить эти команды:
1. `composer update`
2. `./vendor/bin/sail up`
3. `sail artisan migrate`
4. `sail artisan db:seed`
4. `sail artisan meilisearch:setup --fresh`

Дока доступна по ссылке `api/documentation`
