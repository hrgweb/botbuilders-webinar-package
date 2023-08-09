<?php

namespace Hrgweb\Botbuilders\Domain\Registrant\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class ZapierService
{
    public function __construct(protected array $request)
    {
    }

    public static function send(...$request)
    {
        return (new static(...$request))->handle();
    }

    protected function url()
    {
        return config('botbuilders.zapier.url');
    }

    protected function handle()
    {
        try {
            $data = Http::retry(3, 100)->post($this->url(), $this->request)->throw()->json();

            if (!$data) {
                throw new Exception('zapier encountered an error');
            }

            if ($data && $data['status'] === 'success') {
                info('zapier created: ' . json_encode($this->request));

                return $this->request;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
