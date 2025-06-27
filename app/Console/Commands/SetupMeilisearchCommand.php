<?php

namespace App\Console\Commands;

use App\Models\Organization;
use Illuminate\Console\Command;
use MeiliSearch\Client;

class SetupMeilisearchCommand extends Command
{
    protected $signature = 'meilisearch:setup {--fresh : Удалить и пересоздать индекс}';

    protected $description = 'Настройка индексов Meilisearch для организаций';

    public function handle()
    {
        $this->info('Настройка Meilisearch индекса для организаций...');

        try {
            // Получаем клиент Meilisearch
            $client = new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));

            $indexName = (new Organization())->searchableAs();

            // Если указан флаг --fresh, удаляем существующий индекс
            if ($this->option('fresh')) {
                try {
                    $client->deleteIndex($indexName);
                    $this->info("Индекс {$indexName} удален.");
                    sleep(1); // Даем время на удаление
                } catch (\Exception $e) {
                    $this->warn("Не удалось удалить индекс (возможно, он не существует): " . $e->getMessage());
                }
            }

            // Создаем или получаем индекс
            $index = $client->index($indexName);

            // Настраиваем поисковые атрибуты
            $searchableAttributes = (new Organization())->meilisearchSettings()['searchableAttributes'];

            $index->updateSearchableAttributes($searchableAttributes);
            $this->info('Поисковые атрибуты настроены: ' . implode(', ', $searchableAttributes));

            // Настраиваем фильтруемые атрибуты
            $filterableAttributes = (new Organization())->meilisearchSettings()['filterableAttributes'];

            $index->updateFilterableAttributes($filterableAttributes);
            $this->info('Фильтруемые атрибуты настроены: ' . implode(', ', $filterableAttributes));

            // Настраиваем сортируемые атрибуты
            $sortableAttributes = (new Organization())->meilisearchSettings()['sortableAttributes'];

            $index->updateSortableAttributes($sortableAttributes);
            $this->info('Сортируемые атрибуты настроены: ' . implode(', ', $sortableAttributes));

            // Переиндексируем все организации
            if ($this->confirm('Переиндексировать все организации?', true)) {
                $this->info('Запуск переиндексации...');

                Organization::withoutSyncingToSearch(function () {
                    // Сначала очищаем поисковый индекс
                    $this->call('scout:flush', ['model' => Organization::class]);
                });

                // Затем импортируем все записи
                $this->call('scout:import', ['model' => Organization::class]);

                $this->info('Переиндексация завершена.');
            }

            // Показываем информацию об индексе
            $stats = $index->stats();
            $this->table(
                ['Параметр', 'Значение'],
                [
                    ['Количество документов', $stats['numberOfDocuments']],
                    ['В процессе индексации', $stats['isIndexing'] ? 'Да' : 'Нет'],
                ]
            );

        } catch (\Exception $e) {
            $this->error('Ошибка при настройке Meilisearch: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
