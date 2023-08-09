<?php

namespace Hrgweb\Botbuilders\Domain\Registrant\Services;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Cog\Laravel\Optimus\Facades\Optimus;
use Hrgweb\Botbuilders\Domain\Registrant\Exceptions\EverWebinarException;
use Hrgweb\Botbuilders\Domain\Registrant\Models\Registrant;

class EverWebinarService
{
    public function __construct(protected array $request = [])
    {
    }

    public static function make(...$request)
    {
        return new static(...$request);
    }

    protected function url(): string
    {
        $url = config('botbuilders.everwebinar.url');
        $url .= '/register';

        return $url;
    }

    protected function body(): array
    {
        return [
            'api_key' => $this->request['api_key'],
            'webinar_id' => $this->request['webinar_id'],
            'first_name' => $this->request['first_name'],
            'email' => $this->request['email'],
            'schedule' => $this->request['schedule_id'],
            'phone_country_code' => $this->request['phone_country_code'],
            'phone' => $this->request['phone'],
            'timezone' => $this->request['gmt'],
            'date' => $this->request['webinar_date'],
        ];
    }

    public function register()
    {
        $this->request['api_key'] = $this->request['webinar_api'] ?? config('botbuilders.everwebinar.api_key');
        $this->request['webinar_id'] = $this->request['webinar_id'] ?? (int)config('botbuilders.everwebinar.webinar_id');

        // Check if there is no mobile then remove phone country code
        if (!$this->request['phone']) {
            $this->request['phone'] = null;
            $this->request['phone_country_code'] = null;
        }

        $data = Http::post($this->url(), $this->body())->throw()->json();

        if (!$data) {
            info('everwebinar failed to register phone is not valid');

            throw new EverWebinarException;
        }

        info('everwebinar: ' . json_encode($data));

        $user = $data['user'];
        $live = explode('/', $user['live_room_url']);

        $training_url = '';
        if ($live[6]) {
            $training_url = 'http://trainingurl.com/' . $live[5] . '/' . $live[6];
        } else {
            $training_url = 'http://trainingurl.com/' . $live[5];
        }

        $webinar = array_merge($this->request, [
            'schedule_id' => $user['schedule'],
            'webinar_id' => $user['webinar_id'],
            'webinar_user_id' => $user['user_id'],
            'live_room_link' => $user['live_room_url'],
            'replay_link' => $user['replay_room_url'],
            'webinar_link' => $training_url,
            'confirmation_link' => $user['thank_you_url'],
            'custom_live_url' => $training_url,
        ]);

        // Persist to registrants
        $registrant = Registrant::create($webinar);

        if (!$registrant) {
            throw new Exception('registration encountered an error');
        }

        $registrant->uuid = Optimus::encode($registrant->id);
        $registrant->custom_thankyou_url = 'https://webclass.ai/thankyou/' . $this->request['affiliate'] . '/' . $registrant->uuid;
        $registrant->save();

        info('registrant created: ' . json_encode($registrant));

        // return [
        //     'registrant' => $registrant->toArray(),
        //     'webinar' => $user
        // ];
        return array_merge($registrant->toArray(), $user, [
            'epoch' => $this->request['epoch'] ?? 0,
            'kartra' => $this->request['kartra'] ?? []
        ]);
    }
}
