<?php

namespace App\Services\Zoom;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ZoomClient
{
    private ?string $accountId = null;
    private ?string $clientId = null;
    private ?string $clientSecret = null;
    private ?string $hostUserId = null;
    private string $baseUrl = 'https://api.zoom.us/v2';

    public function __construct()
    {
        // Try config/services.php first, then fallback to config/zoom.php if needed (transitional)
        $this->accountId = config('services.zoom.account_id') ?? config('zoom.account_id');
        $this->clientId = config('services.zoom.client_id') ?? config('zoom.client_id');
        $this->clientSecret = config('services.zoom.client_secret') ?? config('zoom.client_secret');
        $this->hostUserId = config('services.zoom.host_user_id') ?? config('zoom.host_user_id', 'me');
    }

    /**
     * Get Access Token (S2S OAuth)
     */
    public function getAccessToken(): ?string
    {
        if (empty($this->accountId) || empty($this->clientId) || empty($this->clientSecret)) {
            return null;
        }

        return Cache::remember('zoom_access_token', 3500, function () {
            try {
                $response = Http::asForm()
                    ->withOptions([
                    'curl' => [CURLOPT_SSL_VERIFYPEER => false],
                    'crypto_method' => null
                ])
                    ->withBasicAuth($this->clientId, $this->clientSecret)
                    ->post('https://zoom.us/oauth/token', [
                        'grant_type' => 'account_credentials',
                        'account_id' => $this->accountId,
                    ]);

                if ($response->failed()) {
                    Log::error('Zoom OAuth Failed', ['body' => $response->body(), 'status' => $response->status()]);
                    return null;
                }

                return $response->json('access_token');
            } catch (\Exception $e) {
                Log::error('Zoom OAuth Exception: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Create Meeting
     */
    public function createMeeting(string $topic, string $startTime, int $durationMinutes): ?array
    {
        $token = $this->getAccessToken();

        // Check if token exists (if not, we are likely in MOCK MODE or credentials failed)
        if (!$token) {
            Log::warning('Zoom Credentials missing or invalid. Running in MOCK MODE.');

            // Mock Response
            $mockId = rand(1000000000, 9999999999);
            return [
                'id' => $mockId,
                'topic' => $topic,
                'join_url' => "https://zoom.us/j/{$mockId}?pwd=mock",
                'start_url' => "https://zoom.us/s/{$mockId}?pwd=mock",
                'password' => 'mock123',
            ];
        }

        $userId = $this->hostUserId;

        try {
            $response = Http::withToken($token)
                ->withOptions([
                    'curl' => [CURLOPT_SSL_VERIFYPEER => false],
                    'crypto_method' => null
                ])
                ->post("{$this->baseUrl}/users/{$userId}/meetings", [
                    'topic' => $topic,
                    'type' => 2, // Scheduled Meeting
                    'start_time' => $startTime, // ISO 8601
                    'duration' => $durationMinutes,
                    'timezone' => 'Asia/Tokyo',
                    'settings' => [
                        'host_video' => true,
                        'participant_video' => true,
                        'join_before_host' => false,
                        'mute_upon_entry' => true,
                        'waiting_room' => true,
                        'auto_recording' => 'cloud',
                    ],
                ]);

            if ($response->failed()) {
                Log::error('Zoom Create Meeting Failed', ['body' => $response->body(), 'status' => $response->status()]);
                // If it fails with real creds, we return null to let controller handle the error
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Zoom Create Meeting Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete Meeting
     */
    public function deleteMeeting(string $meetingId): bool
    {
        $token = $this->getAccessToken();
        if (!$token) {
            Log::warning('running in MOCK MODE: Meeting deleted.');
            return true;
        }

        try {
            $response = Http::withToken($token)
                ->withOptions([
                    'curl' => [CURLOPT_SSL_VERIFYPEER => false],
                    'crypto_method' => null
                ])
                ->delete("{$this->baseUrl}/meetings/{$meetingId}");

            if ($response->failed() && $response->status() !== 404) {
                Log::error('Zoom Delete Meeting Failed', $response->json());
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Zoom Delete Meeting Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get Participants (merged from old ZoomService)
     */
    public function getParticipants(string $meetingId): array
    {
        $token = $this->getAccessToken();
        if (!$token) return [];

        try {
            // Double encode if it contains '/' (UUID)
            if (str_contains($meetingId, '/') || str_contains($meetingId, '+')) {
                $meetingId = urlencode(urlencode($meetingId));
            }

            $response = Http::withToken($token)
                ->withOptions([
                    'curl' => [CURLOPT_SSL_VERIFYPEER => false],
                    'crypto_method' => null
                ])
                ->get("{$this->baseUrl}/report/meetings/{$meetingId}/participants?page_size=300");

            if ($response->failed()) {
                Log::error('Zoom Get Participants Failed', ['body' => $response->body(), 'status' => $response->status()]);
                return [];
            }

            return $response->json('participants') ?? [];
        } catch (\Exception $e) {
            Log::error('Zoom Get Participants Exception: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get Past Meeting Details (merged from old ZoomService)
     */
    public function getPastMeeting(string $meetingId): ?array
    {
        $token = $this->getAccessToken();
        if (!$token) return null;

        try {
            // Double encode if it contains '/' (UUID)
            if (str_contains($meetingId, '/') || str_contains($meetingId, '+')) {
                $meetingId = urlencode(urlencode($meetingId));
            }

            $response = Http::withToken($token)
                ->withOptions(['curl' => [CURLOPT_SSL_VERIFYPEER => false]])
                ->get("{$this->baseUrl}/past_meetings/{$meetingId}");

            if ($response->failed()) {
                if ($response->status() === 404) return null;
                Log::error('Zoom Get Past Meeting Failed', ['body' => $response->body(), 'status' => $response->status()]);
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Zoom Get Past Meeting Exception: ' . $e->getMessage());
            return null;
        }
    }
}
