<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create';

    protected $description = 'Create or update the configured showroom administrator account';

    public function handle(): int
    {
        $name = $this->ask('اسم صاحب المعرض', 'أمجد الشياح');
        $email = (string) config('admin.email');
        $password = $this->secret('كلمة المرور (12 حرفاً على الأقل)');
        $passwordConfirmation = $this->secret('تأكيد كلمة المرور');

        if (mb_strlen((string) $password) < 12) {
            $this->components->error('يجب أن تتكون كلمة المرور من 12 حرفاً على الأقل.');

            return self::FAILURE;
        }

        if (! hash_equals((string) $password, (string) $passwordConfirmation)) {
            $this->components->error('كلمتا المرور غير متطابقتين.');

            return self::FAILURE;
        }

        User::updateOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => $password]
        );

        $this->components->info("تم إعداد حساب الإدارة: {$email}");

        return self::SUCCESS;
    }
}
