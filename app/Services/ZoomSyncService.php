<?php

namespace App\Services;

use App\Models\MeetingLog;
use App\Models\MeetingLogParticipant;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ZoomSyncService
{
    protected $zoomClient;
 
    public function __construct(\App\Services\Zoom\ZoomClient $zoomClient)
    {
        $this->zoomClient = $zoomClient;
    }

    /**
     * Sync Meeting Details and Participants
     */
    public function syncMeeting(MeetingLog $log)
    {
        Log::info("Starting Zoom Sync for MeetingLog ID: {$log->id}, Zoom ID: {$log->zoom_meeting_id}");

        try {
            // 1. Get Past Meeting Details (Duration, End Time, UUID, Host)
            // Try-catch or check for null. If failed (e.g. scope issue), proceed to participants if possible.
            try {
                $details = $this->zoomClient->getPastMeeting($log->zoom_meeting_id);
            } catch (\Exception $e) {
                Log::warning("Zoom Get Past Meeting Details failed for ID: {$log->zoom_meeting_id}. Proceeding to participants. Error: " . $e->getMessage());
                $details = null;
            }

            if ($details) {
                // Update Log if details found
                $log->zoom_uuid = $details['uuid'] ?? $log->zoom_uuid;
                $log->host_email = $details['host_email'] ?? $log->host_email;
                $log->end_time = isset($details['end_time']) ? Carbon::parse($details['end_time'])->setTimezone('Asia/Tokyo') : null;
                $log->duration_minutes = $details['duration'] ?? 0;
            }

            // 2. Get Participants
            // Use UUID if available (preferred for Metrics API), otherwise ID.
            $searchId = $details['uuid'] ?? $log->zoom_uuid ?? $log->zoom_meeting_id;

            $participants = $this->zoomClient->getParticipants($searchId);

            DB::beginTransaction();

            if ($participants) {
                foreach ($participants as $p) {
                    $joinTime = isset($p['join_time']) ? Carbon::parse($p['join_time'])->setTimezone('Asia/Tokyo') : null;
                    $leaveTime = isset($p['leave_time']) ? Carbon::parse($p['leave_time'])->setTimezone('Asia/Tokyo') : null;

                    // Metrics API fields:
                    // user_name, email, duration (seconds? Docs say Int. Usually seconds in Metrics, Minutes in Reports?)
                    // Let's assume seconds for now, or check if it needs conversion. 
                    // Report API returns 'duration' in seconds (actually docs say "duration in seconds").
                    // Wait, Report API returned 'duration' (seconds). My previous code saved it as is.
                    // But 'attend_minutes' implies minutes.
                    // If DB column is integer, and I want minutes, I should divide by 60.
                    // Let's check previous usage. 
                    // "attend_minutes" => $p['duration'] ?? 0. 
                    // If Zoom returns seconds, I was saving seconds as minutes! 
                    // I should fix this now.
                    // Metrics API 'duration' is also seconds.

                    // Report API fields:
                    // name, user_email, duration (seconds)
                    // Metrics used 'user_name', 'email'. Report uses 'name', 'user_email'.

                    $email = $p['user_email'] ?? null;
                    $name = $p['name'] ?? null;
                    $durationSeconds = $p['duration'] ?? 0;
                    $durationMinutes = $durationSeconds > 0 ? ceil($durationSeconds / 60) : 0;

                    // Improved Matching Logic:
                    $criteria = [
                        'meeting_log_id' => $log->id,
                        'join_time' => $joinTime,
                    ];

                    if (!empty($email)) {
                        $criteria['participant_email'] = $email;
                    } elseif (!empty($name)) {
                        $criteria['participant_name'] = $name;
                    } else {
                        continue;
                    }

                    \App\Models\MeetingLogParticipant::updateOrCreate(
                        $criteria,
                        [
                            'zoom_meeting_id' => $log->zoom_meeting_id,
                            'participant_name' => $name,
                            'participant_email' => $email,
                            'leave_time' => $leaveTime,
                            'attend_minutes' => $durationMinutes,
                            'raw_payload' => $p,
                        ]
                    );
                }
            }

            $log->zoom_sync_status = 'synced';
            $log->zoom_synced_at = now();
            $log->zoom_sync_error = null;
            $log->save();

            DB::commit();
            Log::info("Zoom Sync Completed for MeetingLog ID: {$log->id}");

        } catch (\Exception $e) {
            DB::rollBack();
            $log->zoom_sync_status = 'failed';
            $log->zoom_sync_error = $e->getMessage();
            $log->save();
            Log::error("Zoom Sync Failed for MeetingLog ID: {$log->id}. Error: {$e->getMessage()}");
        }
    }
}
