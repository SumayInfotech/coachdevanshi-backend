{{-- OTP email: signup / login / forgot_password. Vars: $otp, $user, $type, $minutes --}}
@php
    $type    = in_array($type ?? 'signup', ['signup', 'login', 'forgot_password']) ? $type : 'signup';
    $minutes = $minutes ?? 2;
    $span    = trans_choice('emails.otp_minutes', $minutes, ['count' => $minutes]);
    $digits  = str_split((string) $otp);
@endphp

@extends('emails.layouts.base')

@section('title', __('emails.otp_title'))
@section('preheader', __('emails.otp_preheader', ['otp' => $otp, 'minutes' => $span]))

@section('content')
    <p style="margin:0 0 10px;font-size:12px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:#c46e77;">
        {{ __('emails.otp_eyebrow_' . $type) }}
    </p>

    <h1 class="dd-h1" style="margin:0 0 22px;font-family:'Playfair Display',Georgia,'Times New Roman',serif;font-size:30px;line-height:1.25;font-weight:700;color:#2b2224;">
        {{ __('emails.otp_title') }}
    </h1>

    <p style="margin:0 0 12px;font-size:16px;line-height:1.65;color:#4a3f40;">
        @if (!empty($user->name))
            {{ __('emails.hello') }} <strong style="color:#2b2224;">{{ $user->name }}</strong>,
        @else
            {{ __('emails.hello') }},
        @endif
    </p>

    <p style="margin:0 0 30px;font-size:16px;line-height:1.65;color:#4a3f40;">
        {{ __('emails.otp_intro_' . $type) }}
    </p>

    {{-- OTP code --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center" style="background-color:#fbeeee;border:1px solid #f1d6d6;border-radius:16px;padding:28px 12px 24px;">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center">
                    <tr>
                        @foreach ($digits as $digit)
                            <td class="dd-otp-cell" align="center" valign="middle"
                                style="width:46px;height:58px;background-color:#ffffff;border:1px solid #e2d3ce;border-radius:10px;font-family:'Playfair Display',Georgia,'Times New Roman',serif;font-size:28px;font-weight:700;color:#8b4a4f;">
                                {{ $digit }}
                            </td>
                            @unless ($loop->last)
                                <td style="width:8px;font-size:0;line-height:0;">&nbsp;</td>
                            @endunless
                        @endforeach
                    </tr>
                </table>
                <p style="margin:18px 0 0;font-size:13px;line-height:1.5;color:#7d6b6d;">
                    {{ __('emails.otp_expiry', ['minutes' => $span]) }}
                </p>
            </td>
        </tr>
    </table>

    {{-- Security note --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:28px;">
        <tr>
            <td style="background-color:#edf1e4;border-left:4px solid #6f8a5b;border-radius:8px;padding:16px 18px;font-size:14px;line-height:1.6;color:#4a3f40;">
                <strong style="color:#4f6640;">{{ __('emails.otp_security_title') }}.</strong>
                {{ __('emails.otp_security_text') }}
            </td>
        </tr>
    </table>
@endsection
