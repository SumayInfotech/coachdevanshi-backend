<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    @php    
    $projectTitle = setting('project_title', 'Admin Panel');
    @endphp
    <title> {{ $projectTitle }} | Reset Password</title>
</head>
<body style="background:#f4f6f9;padding:30px;font-family:Arial,Helvetica,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center">

            <table width="600" style="background:#ffffff;border-radius:6px;padding:30px;">
                <tr>
                    <td align="center" style="padding-bottom:20px;">
                        <img src="{{ setting('app_icon') ? getFileUrl(setting('app_icon')) : asset('assets/sash/images/brand/logo-white.png') }}"
                     class="header-brand-img" width="150" height="150" 
                     alt="Logo">
                    </td>
                </tr>

                <tr>
                    <td>
                        <h2 style="margin-bottom:10px;">Hello {{ $user->name ?? 'User' }},</h2>

                        <p style="color:#555;font-size:14px;line-height:1.6;">
                            We received a request to reset the password for your account.
                        </p>

                        <p style="color:#555;font-size:14px;line-height:1.6;">
                            Click the button below to reset your password.
                        </p>

                        <p style="text-align:center;margin:30px 0;">
                            <a href="{{ $resetUrl }}"
                               style="background:#6259ca;color:#ffffff;
                                      padding:12px 25px;
                                      text-decoration:none;
                                      border-radius:4px;
                                      font-weight:bold;">
                                Reset Password
                            </a>
                        </p>

                        <p style="color:#777;font-size:13px;">
                            If you did not request a password reset, no further action is required.
                        </p>

                        <p style="color:#999;font-size:12px;margin-top:30px;">
                            Regards,<br>
                            {{ config('app.name') }}
                        </p>
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>

</body>
</html>
