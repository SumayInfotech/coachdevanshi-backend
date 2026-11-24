{{-- Reset-password email, used by the customer mailable and the admin notification. Vars: $user, $resetUrl --}}
@php
    $minutes = (int) config('auth.passwords.users.expire', 60);
    $span    = trans_choice('emails.reset_minutes', $minutes, ['count' => $minutes]);
@endphp

@extends('emails.layouts.base')

@section('title', __('emails.reset_title'))
@section('preheader', __('emails.reset_preheader'))

@section('content')
    <p style="margin:0 0 10px;font-size:12px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:#c46e77;">
        {{ __('emails.reset_eyebrow') }}
    </p>

    <h1 class="dd-h1" style="margin:0 0 22px;font-family:'Playfair Display',Georgia,'Times New Roman',serif;font-size:30px;line-height:1.25;font-weight:700;color:#2b2224;">
        {{ __('emails.reset_title') }}
    </h1>

    <p style="margin:0 0 12px;font-size:16px;line-height:1.65;color:#4a3f40;">
        @if (!empty($user->name))
            {{ __('emails.hello') }} <strong style="color:#2b2224;">{{ $user->name }}</strong>,
        @else
            {{ __('emails.hello') }},
        @endif
    </p>

    <p style="margin:0 0 32px;font-size:16px;line-height:1.65;color:#4a3f40;">
        {{ __('emails.reset_intro') }}
    </p>

    {{-- Bulletproof button --}}
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" style="margin:0 auto 12px;">
        <tr>
            <td align="center" bgcolor="#8b4a4f" style="border-radius:12px;background-color:#8b4a4f;">
                <a href="{{ $resetUrl }}" target="_blank"
                   style="display:inline-block;padding:16px 40px;font-family:'Inter',-apple-system,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;font-size:16px;font-weight:600;line-height:1;color:#ffffff;text-decoration:none;border-radius:12px;">
                    {{ __('emails.reset_button') }}
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 28px;text-align:center;font-size:13px;line-height:1.5;color:#7d6b6d;">
        {{ __('emails.reset_expiry', ['minutes' => $span]) }}
    </p>

    {{-- Fallback link --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td style="background-color:#fdf8f5;border:1px solid #eee3df;border-radius:10px;padding:14px 16px;font-size:13px;line-height:1.6;color:#7d6b6d;">
                {{ __('emails.reset_fallback') }}<br>
                <a href="{{ $resetUrl }}" target="_blank" style="color:#8b4a4f;word-break:break-all;text-decoration:underline;">{{ $resetUrl }}</a>
            </td>
        </tr>
    </table>

    {{-- Security note --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:24px;">
        <tr>
            <td style="background-color:#edf1e4;border-left:4px solid #6f8a5b;border-radius:8px;padding:16px 18px;font-size:14px;line-height:1.6;color:#4a3f40;">
                {{ __('emails.reset_ignore') }}
            </td>
        </tr>
    </table>
@endsection
