<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '創建管理員帳號';

    /**
     * Execute the console command.
     */
    public function handle()
    {
      $name = $this->ask('請輸入用戶名稱');
      $email = $this->ask('請輸入電子郵件');
      $password = $this->secret('請輸入密碼');

      $user = \App\Models\User::create([
        'name'      => $name,
        'email'     => $email,
        'password'  => Hash::make($password),
      ]);

      $this->info('管理者建立成功: ' . $user->name . ' 信箱: ' . $user->email);
    }
}
