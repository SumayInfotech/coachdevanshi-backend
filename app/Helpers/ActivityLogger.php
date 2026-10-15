<?php

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;

if (!function_exists('activity_log')) {

    /**
     * Common Activity Logger
     *
     * @param string $log
     * @param string $event
     * @param Model|null $subject 
     * @param array $properties
     * @param string|null $description
     * @return void
     */
    function activity_log(string $log,string $event,Model $subject = null,array $properties = [],string $description = null, Model $explicitCauser = null): void {
        try {
            /*explicitCauser Used When Signup,login time do not have token then pass the userobj*/
            // $causer = auth()->user();
            // echo "<pre>";print_r($causer);"</pre>";die;
            // $causer = auth()->user() ?? Auth::guard('sanctum')->user()?? Auth::guard('api')->user()?? Auth::user(); // fallback
            $causer = $explicitCauser ?? auth()->user();

            // echo "<pre>";print_r($causer);die;
            $activity = activity()
                ->useLog($log)
                ->event($event);

            if ($causer) {
                $activity->causedBy($causer);
            }

            if ($subject) {
                $activity->performedOn($subject);
            }

            //STORE UUIDS EXPLICITLY FOR FUTURE REPORTING
            // if ($causer) {
            //     $properties['causer_uuid'] = $causer->uuid;
            // }

            // if ($subject) {
            //     $properties['subject_uuid'] = $subject->uuid;
            // }

            if (!empty($properties)) {
                $activity->withProperties($properties);
            }

            $activity->log(
                $description ?? ucfirst($event).' '.$log
            );

        } catch (\Throwable $e) {
            \Log::error('Activity Log Error', [
                'message' => $e->getMessage(),
                'log'     => $log,
                'event'   => $event,
            ]);
        }
    }
}
