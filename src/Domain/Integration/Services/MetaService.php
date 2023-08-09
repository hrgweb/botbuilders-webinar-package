<?php

namespace Hrgweb\Botbuilders\Domain\Integration\Services;

use Exception;

class MetaService
{
    public function __construct(protected array $request = [])
    {
    }

    public static function send(...$request)
    {
        return (new static(...$request))->handle();
    }

    protected function url()
    {
        $url = 'https://graph.facebook.com/v17.0/' . config('botbuilders.meta.pixel_id') . '/' . 'events' . '?access_token=' . config('botbuilders.meta.access_token');

        return $url;
    }

    protected function setup()
    {
        $url = $this->url();
        $body = $this->body();
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "content-type: application/json",
        );

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

        $resp = curl_exec($curl);

        info($resp);

        curl_close($curl);

        // $this->store(); // save conversion

        return $resp;
    }

    protected function formatFacebookClickId($fbclid)
    {
        return 'fb.1.' . time() . '.' . $fbclid;
    }

    protected function userData()
    {
        $user = [
            "fn"  => hash('sha256', $this->request['first_name'] ??= null),
            "ln"  => hash('sha256', $this->request['last_name'] ??= null),
            "em"  => hash('sha256', $this->request['email'] ??= null),
        ];

        return json_encode($user);
    }

    protected function data()
    {
        return [[
            "event_name" => $this->request['eventName'] ??= null,
            "event_time" => time(),
            "action_source" => "website",
            "event_id" => $this->request["uuid"] ??= 0,
            "event_source_url" => $this->request['page'] ??= null,
            "user_data" =>  $this->userData(),
            'custom_data' => [
                "currency" => $this->request['currency'] ?? 'USD',
                "value" => $this->request['amount'] ?? 0,
            ]
        ]];
    }

    protected function body()
    {
        // Local
        if (config('app.env') === 'local') {
            // info('local');

            return json_encode([
                "data" => $this->data(),
                "test_event_code" => $this->request['test_event_code'] ??= 'TEST40215'
            ]);
        }

        // info('production');

        // Production
        return json_encode(["data" => $this->data()]);
    }

    protected function exec()
    {
        info($this->body());

        $this->setup();

        info('meta conversion api send successfully.');
    }

    protected function handle()
    {
        if (!count($this->request['eventNames'])) {
            throw new Exception('encountered an issue event names not set.');
        }

        foreach ($this->request['eventNames'] as $eventName) {
            $this->request['eventName'] = $eventName;

            $this->exec();
        }
    }
}
