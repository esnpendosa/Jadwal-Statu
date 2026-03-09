<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use App\Models\User;

class GoogleCalendarService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setDeveloperKey(config('services.google.api_key'));
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect'));
        $this->client->addScope(Calendar::CALENDAR_EVENTS);
        $this->client->setAccessType('offline');
    }

    public function createEvent(User $user, string $title, string $description, string $date): ?string
    {
        if (!$user->google_calendar_token) {
            return null;
        }

        $this->setUserToken($user);

        $service = new Calendar($this->client);

        $event = new Event([
            'summary'     => $title,
            'description' => $description,
            'start'       => new EventDateTime(['date' => $date]),
            'end'         => new EventDateTime(['date' => $date]),
            'reminders'   => [
                'useDefault' => false,
                'overrides'  => [
                    ['method' => 'email',  'minutes' => 24 * 60],
                    ['method' => 'popup',  'minutes' => 60],
                ],
            ],
        ]);

        try {
            $calendarId = config('services.google.calendar_id', 'primary');
            $created = $service->events->insert($calendarId, $event);
            return $created->id;
        } catch (\Exception $e) {
            logger()->error('Google Calendar event creation failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function deleteEvent(User $user, string $eventId): void
    {
        if (!$user->google_calendar_token) return;
        $this->setUserToken($user);

        try {
            $service = new Calendar($this->client);
            $service->events->delete('primary', $eventId);
        } catch (\Exception $e) {
            logger()->warning('Google Calendar event deletion failed', ['error' => $e->getMessage()]);
        }
    }

    public function getAuthUrl(): string
    {
        return $this->client->createAuthUrl();
    }

    public function exchangeCode(string $code): array
    {
        return $this->client->fetchAccessTokenWithAuthCode($code);
    }

    private function setUserToken(User $user): void
    {
        $token = $user->google_calendar_token;
        $this->client->setAccessToken($token);

        if ($this->client->isAccessTokenExpired() && $this->client->getRefreshToken()) {
            $newToken = $this->client->fetchAccessTokenWithRefreshToken();
            $user->update(['google_calendar_token' => json_encode($newToken)]);
        }
    }
}
