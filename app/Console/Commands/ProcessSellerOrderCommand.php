<?php

namespace App\Console\Commands;

use App\Jobs\LogSellerOrderJob;
use Illuminate\Console\Command;

class ProcessSellerOrderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:seller-orders {order_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $orderId = $this->argument('order_id');
        LogSellerOrderJob::dispatch($orderId);
    }
}
