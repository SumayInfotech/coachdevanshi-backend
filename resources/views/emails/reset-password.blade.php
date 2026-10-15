<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    @php
        $projectTitle = setting('project_title', 'Admin Panel');
    @endphp

    <title>{{ $projectTitle }} | Reset Password</title>

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
                            Reset Your Password
                        </h1>

                        <p style="color:#414141;font-size:16px;line-height:1.6;">
                            Hello <strong>{{ $user->name ?? 'User' }}</strong>,
                        </p>

                        <p style="color:#414141;font-size:16px;line-height:1.6;">
                            We received a request to reset your password.
                            Click the button below to set a new password.
                        </p>

                        <!-- CTA BUTTON -->
                        <div style="text-align:center;margin:35px 0;">
                            <a href="{{ route('customer.password.reset', ['token' => $token]) }}"

                               style="
                                   display:inline-block;
                                   background:#F29217;
                                   color:#ffffff;
                                   text-decoration:none;
                                   padding:14px 34px;
                                   border-radius:6px;
                                   font-size:16px;
                                   font-weight:600;
                                   text-transform:uppercase;
                               ">
                                Reset Password
                            </a>
                        </div>

                        <p style="color:#9D651E;font-size:14px;">
                            This link is valid for <strong>30 minutes</strong>.
                            If you didn’t request this, please ignore this email.
                        </p>

                    </td>
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td style="background:#F4F4E9;padding:20px;text-align:center;">
                        <p style="margin:0;font-size:12px;color:#414141;">
                            © {{ date('Y') }} {{ $projectTitle }}. All rights reserved.
                        </p>
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
