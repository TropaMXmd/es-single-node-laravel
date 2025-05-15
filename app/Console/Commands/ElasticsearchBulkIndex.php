<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use App\Repositories\ElasticSearchRepository;

class ElasticsearchBulkIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:elasticsearch-bulk-index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index all products to Elasticsearch From local DB';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("::::::::::::::::ES_PRODUCT_REQUEST_INDEXING_STARTED:::::::::::::::::::");
        $products = Product::with(['attributes', 'reviews'])->get();

        $indexParams = array();
        foreach ($products as $key => $product) {

            $indexParams['body'][] = [
                'index' => [
                    '_index' => config('database.elasticsearch.hosts.0.index'),
                    '_id'    => $product->id
                ]
            ];

            $indexParams['body'][] = $product->toArray();
            $this->info("Indexing for product: " . $product->id);
        }
        if (!empty($indexParams['body'])) {
            $responses = (new ElasticSearchRepository())->bulkIndex($indexParams);
            $resp = $responses->asArray();
            if ($resp['errors'] == false)
                $this->info("Successfully indexed all products");
            else $this->info("Indexing failed");
        }
        $this->info("::::::::::::::::ES_PRODUCT_REQUEST_INDEXING_END:::::::::::::::::::");
    }
}
