<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use App\Events\ElasticsearchIndexEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Repositories\ElasticSearchRepository;

class ElasticsearchIndexListner
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ElasticsearchIndexEvent $event): void
    {
        $response = (new ElasticSearchRepository())->create($event->product);
        Log::info($response->getBody());
    }
}
