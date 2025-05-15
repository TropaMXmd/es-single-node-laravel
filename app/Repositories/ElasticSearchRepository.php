<?php

namespace App\Repositories;

use App\Repositories\ElasticSearchClient;
use App\Repositories\RepositoryInterface;

class ElasticSearchRepository implements RepositoryInterface
{

    private $esClient = null;
    private $indexDocParams = array();

    public function __construct()
    {
        if (!is_object($this->esClient)) {
            $this->connectElasticSearch();
        }
        return $this;
    }

    public function all() {}

    public function delete($id) {}

    public function find($id) {}

    private function connectElasticSearch()
    {
        if (!is_object($this->esClient)) {
            $this->esClient = ElasticSearchClient::getClient();
        }
        $this->indexDocParams['index'] = config('database.elasticsearch.hosts.0.index');
    }

    public function create($parcel)
    {
        $this->indexDocParams['id'] = $parcel['id'];
        $this->indexDocParams['body'] = $parcel;
        return $this->esClient->index($this->indexDocParams);
    }

    public function update($parcel, $id) {}

    public function bulkIndex($data)
    {
        return $this->esClient->bulk($data);
    }

    public function getById($id)
    {
        $this->indexDocParams['id'] = $id;
        $this->indexDocParams['body'] = array();
        $result = $this->esClient->get($this->indexDocParams);
        return $result['_source'];
    }

    public function findMany($request)
    {
        $response = $this->esClient->search([
            'index' => 'products',
            'body' => [
                'size' => 100, // return up to 100 products
                'query' => [
                    'match_all' => (object)[] // return everything
                ]
            ]
        ]);

        // Extract only the _source field
        $products = collect($response['hits']['hits'])->pluck('_source');
        return $products;
    }

    public function findByKey($request) //by cltp
    {
        // $searchIn = [
        //     'index' => 'parcel_requests',
        //     'body' => [
        //         'query' => [

        //             "terms" => [
        //                 'cltp_token.keyword' => ['CT2000004012405143319', 'CT0014012405064404']
        //             ]


        //         ]
        //     ]
        // ];
        $parentTokens = array();
        foreach ($request->get("cltp_token") as $cltpToken) {
            $cltp = explode("-", $cltpToken);
            array_push($parentTokens, $cltp[0]);
        }

        $this->indexDocParams['body']['query']['terms']['cltp_token.keyword'] = $parentTokens;
        $this->indexDocParams['body']['_source']['include'] = [
            "id",
            "cltp_token",
            "cust_name",
            "cust_mobile",
            "cust_alter_mobile",
            "cust_delivery_address",
            "created_at",
            "ecommerce.org_name",
            "logistic.org_name",
            "items.cltp_sub_token",
            "items.ecom_product_id",
            "items.delivery_status",
            "items.status.cltp_sub_token",
            "items.status.status",
            "items.status.comments",
            "items.status.created_at"
        ];
        $results = $this->esClient->search($this->indexDocParams);
        $response = array();
        foreach ($results['hits']['hits'] as $item) {
            array_push($response, $item['_source']);
        }

        return  $response;
    }

    public function searchParcelByCltpSubToken($cltpToken)
    {

        $searchNested = [
            'index' => 'parcel_requests',
            'body'  => [
                'query' => [
                    'nested' => [
                        'path' => 'items',
                        'query' => [

                            // 'query_string' => [
                            //     'default_field' => 'items.cltp_sub_token',
                            //     'query' => 'CT0014012405064404-01'
                            // ]
                            'bool' => [
                                'must' => [
                                    [
                                        // 'match' => [
                                        //     'items.cltp_sub_token' => "CT0014012405064404\\-\\01"
                                        // ]
                                        'match' => [
                                            "items.cltp_sub_token" => [
                                                "query" => "CT0014012405064404-01",
                                                "fuzziness" => 1
                                            ]
                                        ]

                                    ],
                                ]
                            ]
                        ],
                        "inner_hits" => new \stdClass(),
                        'score_mode' => 'avg'
                    ]
                ]
            ]
        ];
    }
}
