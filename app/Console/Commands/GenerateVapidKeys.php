<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

class GenerateVapidKeys extends Command
{
    protected $signature = 'vapid:generate';
    protected $description = 'Generate VAPID keys for web push notifications';

    public function handle()
    {
        $keys = VAPID::createVapidKeys();
        
        $this->info('VAPID Keys generated successfully:');
        $this->line('Public Key: ' . $keys['publicKey']);
        $this->line('Private Key: ' . $keys['privateKey']);
        
        $this->newLine();
        $this->info('Add these to your .env file:');
        $this->line('VAPID_PUBLIC_KEY=' . $keys['publicKey']);
        $this->line('VAPID_PRIVATE_KEY=' . $keys['privateKey']);
        $this->line('VAPID_SUBJECT=mailto:your@email.com');
    }
}