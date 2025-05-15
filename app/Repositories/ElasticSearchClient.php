<?php

namespace App\Repositories;

use Elastic\Elasticsearch\ClientBuilder;


class ElasticSearchClient
{
    private static $elasticsearch;

    public static function getClient()
    {
        if (!is_object(ElasticSearchClient::$elasticsearch)) {

            $hostConfig = config('database.elasticsearch.hosts.0');
            $host = [$hostConfig['host'] . ':' . $hostConfig['port']];

            ElasticSearchClient::$elasticsearch = ClientBuilder::create()
                ->setSSLVerification(false)
                ->setHosts($host)
                ->setBasicAuthentication($hostConfig['user'], $hostConfig['pass'])
                ->build();
        }

        return   ElasticSearchClient::$elasticsearch;
    }
}
