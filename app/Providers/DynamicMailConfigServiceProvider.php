<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class DynamicMailConfigServiceProvider extends ServiceProvider
{
    public function bootOld(): void
    {
        if (!Schema::hasTable('settings')) {
            return;
        }
        if (!function_exists('setting')) {
            return;
        }

        if (!setting('mail_host')) {
            return;
        }

        config([
            'mail.default' => setting('mail_mailer', 'smtp'),

            'mail.mailers.smtp.host' => setting('mail_host'),
            'mail.mailers.smtp.port' => setting('mail_port'),
            'mail.mailers.smtp.username' => setting('mail_username'),
            'mail.mailers.smtp.password' => setting('mail_password'),
            'mail.mailers.smtp.encryption' => setting('mail_encryption'),

            'mail.from.address' => setting('mail_from_address'),
            'mail.from.name' => setting('mail_from_name', config('app.name')),
        ]);
    }
    public function bootOld2()
    {
        $this->app->resolving('mailer', function () {
            try {
                if (\Schema::hasTable('settings')) {
                    $settings = \App\Models\Setting::first();

                    if ($settings) {
                        config([
                            'mail.mailers.smtp.host' => $settings->mail_host,
                            'mail.mailers.smtp.port' => $settings->mail_port ?? 587,
                            'mail.mailers.smtp.username' => $settings->mail_username,
                            'mail.mailers.smtp.password' => $settings->mail_password,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                // prevent crash
            }
        });
    }
    
    public function boot(): void
    {
        try {
            if (!Schema::hasTable('settings')) {
                return;
            }

            if (!function_exists('setting')) {
                return;
            }

            if (!setting('mail_host')) {
                return;
            }

            config([
                'mail.default' => setting('mail_mailer', 'smtp'),

                'mail.mailers.smtp.transport' => 'smtp',
                'mail.mailers.smtp.host' => setting('mail_host'),
                'mail.mailers.smtp.port' => setting('mail_port', 587),
                'mail.mailers.smtp.username' => setting('mail_username'),
                'mail.mailers.smtp.password' => setting('mail_password'),
                'mail.mailers.smtp.encryption' => setting('mail_encryption'),

                'mail.from.address' => setting('mail_from_address'),
                'mail.from.name' => setting('mail_from_name', config('app.name')),
            ]);
        } catch (\Exception $e) {
            // avoid breaking app
        }
    }
}