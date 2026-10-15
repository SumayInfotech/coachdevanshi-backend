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

<body style="
    margin:0;
    padding:0;
    background-color:#F4F4E9;
    font-family:'Gabarito', Arial, Helvetica, sans-serif;
">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 0;">
    <tr>
        <td align="center">

            <!-- MAIN CONTAINER -->
            <table width="600" cellpadding="0" cellspacing="0" style="
                background:#ffffff;
                border-radius:10px;
                overflow:hidden;
                box-shadow:0 10px 30px rgba(0,0,0,0.08);
            ">

                <!-- HEADER -->
                <tr>
                    <td align="center" style="
                        background:#1E1E1E;
                        padding:30px;
                    ">
                        <img src="{{ setting('app_icon') ? getFileUrl(setting('app_icon')) : asset('assets/sash/images/brand/logo-white.png') }}"
                             alt="Logo"
                             width="150"
                             height="150"
                             style="display:block;">
                    </td>
                </tr>

                <!-- BODY -->
                <tr>
                    <td style="padding:40px 35px 30px;">

                        <h1 style="
                            margin:0 0 15px;
                            color:#1E1E1E;
                            font-size:28px;
                            font-weight:700;
                            text-transform:uppercase;
                            letter-spacing:1px;
                        ">
                            {{ __('auth.reset_password') }}
                        </h1>

                        <p style="
                            margin:0 0 18px;
                            color:#414141;
                            font-size:16px;
                            line-height:1.6;
                        ">
                            {{ __('common.hello') }} <strong>{{ $user->name ?? 'User' }}</strong>,
                        </p>

                        <p style="
                            margin:0 0 20px;
                            color:#414141;
                            font-size:16px;
                            line-height:1.6;
                        ">
                            {{ __('common.We received a request to reset the password for your account. Click the button below to securely set a new password.') }}
                        </p>

                        <!-- CTA BUTTON -->
                        <div style="text-align:center;margin:35px 0;">
                            <a href="{{ $resetUrl }}"
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
                                   letter-spacing:1px;
                               ">
                                {{ __('auth.reset_password') }}
                            </a>
                        </div>

                        <!-- WARNING -->
                        <p style="
                            margin:25px 0 0;
                            color:#9D651E;
                            font-size:14px;
                            line-height:1.5;
                        ">
                            {{__('common.If you did not request a password reset, you can safely ignore this email. Your account will remain secure.')}}
                        </p>

                    </td>
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td style="
                        background:#F4F4E9;
                        padding:20px 30px;
                        text-align:center;
                        border-top:1px solid #e6e6e6;
                    ">
                        <p style="
                            margin:0;
                            color:#414141;
                            font-size:12px;
                            line-height:1.5;
                        ">
                            © {{ date('Y') }} {{ $projectTitle }}. {{ __('common.All rights reserved') }}.
                        </p>

                        <p style="
                            margin:6px 0 0;
                            color:#9D651E;
                            font-size:12px;
                        ">
                            {{ __('common.This is an automated message. Please do not reply.') }}
                        </p>
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
