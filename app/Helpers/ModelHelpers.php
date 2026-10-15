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