<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\SystemNotification;

class TestNotification extends Command
{
    protected $signature = 'test:notification {user_id}';
    protected $description = 'Send a test notification to a user';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);

        if (!$user) {
            $this->error('User not found!');
            return;
        }

        $user->notify(new SystemNotification(
            'تنبيه تجريبي',
            'هذا تنبيه للتأكد من عمل نظام التنبيهات والبريد الإلكتروني بنجاح.',
            route('analysis')
        ));

        $this->info('Test notification sent successfully to ' . $user->email);
    }
}
