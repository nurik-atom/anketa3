<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeUserAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:make-admin {email : Email пользователя}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Назначить пользователю права администратора';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("Пользователь с email '{$email}' не найден.");
            return Command::FAILURE;
        }
        
        if ($user->is_admin) {
            $this->info("Пользователь '{$email}' уже является администратором.");
            return Command::SUCCESS;
        }
        
        $user->update(['is_admin' => true]);
        
        $this->info("Пользователь '{$email}' теперь является администратором.");
        return Command::SUCCESS;
    }
}
