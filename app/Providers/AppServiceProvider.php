<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verifikasi Alamat Email Anda - LSPro BBPM SDLP')
                ->greeting('Yth. Bapak/Ibu dari ' . ($notifiable->nama_perusahaan ?? 'Instansi') . ',')
                ->line('Terima kasih telah mendaftar pada portal layanan sertifikasi Lembaga Sertifikasi Produk (LSPro).')
                ->line('Demi keamanan dan kelancaran komunikasi, mohon lakukan verifikasi alamat email Anda dengan menekan tombol di bawah ini:')
                ->action('Verifikasi Alamat Email', $url)
                ->line('Tautan verifikasi ini dikirimkan secara otomatis oleh sistem. Apabila Anda tidak merasa melakukan pendaftaran akun pada portal kami, mohon abaikan surat elektronik ini.')
                ->salutation("Hormat kami,\n\nTim Layanan LSPro BBPM SDLP");
        });
    }
}
