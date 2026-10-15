<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    @php
        $projectTitle = setting('project_title', 'Admin Panel');
        $footer = setting('footer_text');
    @endphp

    <title>{{ $projectTitle }} | Verify OTP</title>

    <link href="https://fonts.googleapis.com/css2?family=Gabarito:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Gabarito', sans-serif !important;
        }
    </style>
</head>

<body style="margin:0;padding:0;background-color:#F4F4E9;">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 0;">
    <tr>
        <td align="center">

            <table width="600" cellpadding="0" cellspacing="0" style="
                background:#ffffff;
                border-radius:10px;
                overflow:hidden;
                box-shadow:0 10px 30px rgba(0,0,0,0.08);
            ">

                <!-- HEADER -->
                <tr>
                    <td align="center" style="background:#1E1E1E;padding:30px;">
                        <img src="{{ setting('app_icon') ? getFileUrl(setting('app_icon')) : asset('assets/sash/images/brand/logo-white.png') }}"
                             width="140"
                             alt="Logo">
                    </td>
                </tr>

                <!-- BODY -->
                <tr>
                    <td style="padding:40px 35px 30px;">

                        <h1 style="color:#1E1E1E;font-size:26px;font-weight:700;">
                            {{ __('auth.verify_your_otp') }}
                        </h1>

                        <p style="color:#414141;font-size:16px;line-height:1.6;">
                            {{ __('common.hello') }} <strong>{{ $user->name ?? 'User' }}</strong>,
                        </p>

                        <p style="color:#414141;font-size:16px;line-height:1.6;">
                            {{ __('auth.use_the_otp_below_to_complete_your_signup') }}
                            {{ __('auth.this_otp_is_valid_for') }} <strong>{{ __('auth.10_minutes') }}</strong>.
                        </p>

                        <!-- OTP BOX -->
                        <div style="
                            margin:30px 0;
                            text-align:center;
                            font-size:28px;
                            letter-spacing:6px;
                            font-weight:700;
                            color:#F29217;
                        ">
                            {{ $otp }}
                        </div>

                        <p style="color:#9D651E;font-size:14px;">
                            {{ __('auth.if_you_didn_t_request_this_please_ignore_this_email') }}
                        </p>

                    </td>
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td style="background:#F4F4E9;padding:20px;text-align:center;">
                        <p style="margin:0;font-size:12px;color:#414141;">
                            {{ $footer }}
                        </p>
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
