<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Repositories\ElasticSearchClient;

class ElasticsearchMapping extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:elasticsearch-mapping';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("::::::::::::::::ELASTIC_SEARCH_SETUP_STARTED:::::::::::::::::::");
        $client = ElasticSearchClient::getClient();
        $indexDocParams['index'] = $indexName = config('database.elasticsearch.hosts.0.index');
        $indexDocParams['settings'] = array(
            "index.mapping.single_type" => true
        );
        // Delete index if it exists
        $response = $client->indices()->exists(['index' => 'products']);
        if ($response->getStatusCode() === 200) {
            $this->info("Index [$indexName] already exists. Deleting...");
            $client->indices()->delete(['index' => 'products']);
        }

        $indexDocParams['body']['mappings'] = $this->getMappingProperties();
        $resp = $client->indices()->create($indexDocParams);
        $response = $resp->asArray();
        // return $resp->getBody();
        if ($response['index'] == $indexName)
            $this->info("Index mapping done successfully!");
        else {
            $this->info("Index mapping error!");
            Log::info($resp->getBody());
        }

        $this->info("::::::::::::::::ELASTIC_SEARCH_SETUP_END:::::::::::::::::::");
    }

    public function getMappingProperties()
    {
        return [
            'properties' => [
                'id' => ['type' => 'keyword'],
                'name' => ['type' => 'text'],
                'description' => ['type' => 'text'],
                'price' => ['type' => 'float'],
                'in_stock' => ['type' => 'boolean'],
                'created_at' => ['type' => 'date'],
                'category' => ['type' => 'keyword'],
                'tags' => ['type' => 'keyword'],
                'location' => ['type' => 'geo_point'],
                'attributes' => [
                    'type' => 'nested',
                    'properties' => [
                        'name' => ['type' => 'keyword'],
                        'value' => ['type' => 'text'],
                    ]
                ],
                'reviews' => [
                    'type' => 'nested',
                    'properties' => [
                        'user_id' => ['type' => 'keyword'],
                        'rating' => ['type' => 'integer'],
                        'comment' => ['type' => 'text'],
                        'created_at' => ['type' => 'date'],
                    ]
                ]
            ]
        ];
    }
}
