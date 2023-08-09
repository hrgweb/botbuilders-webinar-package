<?php

namespace Hrgweb\Botbuilders\Domain\Registrant\Services;

use Illuminate\Support\Facades\Log;

class KartraService
{
    public function __construct(protected array $request = [])
    {
    }

    public static function send(...$request)
    {
        return (new static(...$request))->create();
    }

    protected function setup(array $body = [])
    {
        $ch = curl_init();

        // CONNECT TO API, VERIFY MY API KEY AND PASSWORD AND GET THE LEAD DATA
        curl_setopt($ch, CURLOPT_URL, config('botbuilders.kartra.url'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            http_build_query($body)
        );

        // REQUEST CONFIRMATION MESSAGE FROM API…
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);
        $server_json = json_decode($server_output);

        return $server_json;
    }

    protected function body()
    {
        return [
            'app_id' => config('botbuilders.kartra.id'),
            'api_key' => config('botbuilders.kartra.key'),
            'api_password' => config('botbuilders.kartra.password'),
            'lead' => [
                'email' => $this->request['email'],
                'first_name' => $this->request['first_name'],
                'last_name' => '', // property not exist
                'custom_fields' => [
                    [
                        'field_identifier' => 'Source',
                        'field_value' => $this->request['utm_source'] ??= ''
                    ],
                    [
                        'field_identifier' => 'Campaign',
                        'field_value' => $this->request['utm_campaign'] ??= ''
                    ],
                    [
                        'field_identifier' => 'Medium',
                        'field_value' => $this->request['utm_medium'] ??= ''
                    ],
                    [
                        'field_identifier' => 'Terms',
                        'field_value' => $this->request['utm_term'] ??= ''
                    ],
                    [
                        'field_identifier' => 'Content',
                        'field_value' => $this->request['utm_content'] ??= ''
                    ],
                    [
                        'field_identifier' => 'SiteUrl',
                        'field_value' => $this->request['page'] ??= ''
                    ],
                    // [
                    //     'field_identifier' => 'FB_Click_id',
                    //     'field_value' => $this->request['fbclid'] ??= ''
                    // ],
                    // [
                    //     'field_identifier' => 'fb_event_id',
                    //     'field_value' => $this->request['fbeventid'] ??= ''
                    // ],
                ]
            ],
        ];
    }

    public function create()
    {
        $body = $this->body();
        $body['actions'] = [['cmd' => 'create_lead']];

        $server_json = $this->setup($body);

        switch ($server_json->status) {
            case "Error":
                Log::error('kartra create error: ' . json_encode($server_json));    // process what error was about
                $this->edit(); // edit kartra
                break;
            case "Success":
                info('kartra create success: ' . $this->request['email']);
                break;
        }

        // Assign tags to lead
        $this->assignTags();
    }

    protected function edit()
    {
        $body = $this->body();
        $body['actions'] = [['cmd' => 'edit_lead']];

        $server_json = $this->setup($body);

        switch ($server_json->status) {
            case "Error":
                Log::error('kartra edit error: ' . json_encode($server_json));  // process what error was about
                break;
            case "Success":
                info('kartra edit success: ' . $this->request['email']);
                break;
        }
    }

    protected function assignTags()
    {
        $tags = $this->request['kartra'] ?? [];
        $email = $this->request['email'] ?? '';

        if (count($tags) <= 0) {
            info('no kartra tags on the requests.');
            return;
        }

        foreach ($tags as $tag) {
            $body = [
                'app_id' => config('kartra.id'),
                'api_key' => config('kartra.key'),
                'api_password' => config('kartra.password'),
                'lead' => [
                    'email' => $email
                ],
                'actions' => [
                    [
                        'cmd' => 'assign_tag',
                        'tag_name' => $tag
                    ]
                ]
            ];

            $server_json = $this->setup($body);

            switch ($server_json->status) {
                case "Error":
                    Log::error('kartra assign tag error: ' . json_encode($server_json));    // process what error was about
                    break;
                case "Success":
                    info('kartra assign tag success: ' . $this->request['email']);
                    break;
            }
        }
    }
}
