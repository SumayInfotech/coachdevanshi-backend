<?php

/**
 * @return Application|mixed
 * table model
 */
function User(){
    return app(App\Models\User::class);
}

/**
 * @return Application|mixed
 * Auth User with their model
 */
function AuthUser(){
    if(Auth()->user() != null){
        return User()->where('uuid',Auth()->user()->uuid)->first();
    }else{
        return null;
    }
}

/**
 * @return Application|mixed
 * table model
 */
function Role(){
    return app(\App\Models\Role::class);
}
function State(){
    return app(\App\Models\State::class);
}
function UserFcmToken(){
    return app(\App\Models\UserFcmToken::class);
}
function Preference(){
    return app(\App\Models\Preference::class);
}
function UserPreference(){
    return app(\App\Models\UserPreference::class);
}
function OnboardingMaster(){
    return app(\App\Models\OnboardingMaster::class);
}
function UserOnboardingChapter(){
    return app(\App\Models\UserOnboardingChapter::class);
}
function UserConsent(){
    return app(\App\Models\UserConsent::class);
}
function Pathway(){
    return app(\App\Models\Pathway::class);
}
function UserPathwayProtocol(){
    return app(\App\Models\UserPathwayProtocol::class);
}
function UserPlan(){
    return app(\App\Models\UserPlan::class);
}
function PlanRenewalRequest(){
    return app(\App\Models\PlanRenewalRequest::class);
}
function CoachNote(){
    return app(\App\Models\CoachNote::class);
}
function DailyCheckin(){
    return app(\App\Models\DailyCheckin::class);
}
function WeeklyCheckin(){
    return app(\App\Models\WeeklyCheckin::class);
}
function ProgressPhoto(){
    return app(\App\Models\ProgressPhoto::class);
}
function FoodDiaryEntry(){
    return app(\App\Models\FoodDiaryEntry::class);
}
function BloodReport(){
    return app(\App\Models\BloodReport::class);
}
function BloodReportFile(){
    return app(\App\Models\BloodReportFile::class);
}
function DailyNote(){
    return app(\App\Models\DailyNote::class);
}
function ActivitySync(){
    return app(\App\Models\ActivitySync::class);
}
function AppNotification(){
    return app(\App\Models\AppNotification::class);
}
function ChatMessage(){
    return app(\App\Models\ChatMessage::class);
}
