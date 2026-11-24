{{--
    Shared branded layout for every transactional email.

    Sections:
      @section('title')      <title> and browser tab text
      @section('preheader')  hidden inbox preview text
      @section('content')    the card body
    Uses table layout + inline styles only, so it renders in Gmail, Outlook and Apple Mail.
    Colours follow public/assets/brand/admin-theme.css (maroon / blush / sage / cream).
--}}
@php
    $projectTitle = setting('project_title') ?: 'Devanshi Doshi';
    $footerText   = setting('footer_text');
    $logoUrl      = asset('assets/brand/logo.png');
    $serif        = "'Playfair Display', Georgia, 'Times New Roman', serif";
    $sans         = "'Inter', -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif";
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>@yield('title', $projectTitle)</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        @media only screen and (max-width: 620px) {
            .dd-container { width: 100% !important; }
            .dd-pad { padding-left: 24px !important; padding-right: 24px !important; }
            .dd-h1 { font-size: 26px !important; }
            .dd-otp-cell { font-size: 24px !important; width: 40px !important; height: 52px !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#f8f2ef;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;">

{{-- Hidden preheader: the grey preview line shown next to the subject in the inbox --}}
<div style="display:none;max-height:0;overflow:hidden;opacity:0;mso-hide:all;font-size:1px;line-height:1px;color:#f8f2ef;">
    @yield('preheader')
    &#8199;&zwnj;&#8199;&zwnj;&#8199;&zwnj;&#8199;&zwnj;&#8199;&zwnj;&#8199;&zwnj;&#8199;&zwnj;&#8199;&zwnj;
</div>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f8f2ef;">
    <tr>
        <td align="center" style="padding:32px 12px;">

            <table role="presentation" class="dd-container" width="600" cellpadding="0" cellspacing="0" border="0"
                   style="width:600px;max-width:600px;">

                {{-- Card --}}
                <tr>
                    <td style="background-color:#ffffff;border-radius:20px;overflow:hidden;border:1px solid #eee3df;box-shadow:0 12px 40px rgba(43,34,36,0.08);">

                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">

                            {{-- Brand accent bar --}}
                            <tr>
                                <td height="6" style="height:6px;line-height:6px;font-size:0;background-color:#8b4a4f;background-image:linear-gradient(90deg,#8b4a4f 0%,#c46e77 55%,#d9959b 100%);">&nbsp;</td>
                            </tr>

                            {{-- Header / logo --}}
                            <tr>
                                <td align="center" class="dd-pad" style="background-color:#fdf8f5;padding:34px 40px 26px;border-bottom:1px solid #eee3df;">
                                    <img src="{{ $logoUrl }}" width="200" alt="{{ $projectTitle }}"
                                         style="display:block;width:200px;max-width:100%;height:auto;border:0;outline:none;text-decoration:none;">
                                </td>
                            </tr>

                            {{-- Content --}}
                            <tr>
                                <td class="dd-pad" style="padding:40px 44px 36px;font-family:{!! $sans !!};color:#4a3f40;">
                                    @yield('content')
                                </td>
                            </tr>

                            {{-- Sign-off --}}
                            <tr>
                                <td class="dd-pad" style="padding:0 44px 36px;font-family:{!! $sans !!};">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-top:1px solid #eee3df;">
                                        <tr>
                                            <td style="padding-top:22px;font-size:15px;line-height:1.6;color:#4a3f40;">
                                                {{ __('emails.warm_regards') }}<br>
                                                <span style="font-family:{!! $serif !!};font-size:18px;font-weight:600;color:#8b4a4f;">{{ $projectTitle }}</span><br>
                                                <span style="font-size:12px;letter-spacing:1.5px;text-transform:uppercase;color:#7d6b6d;">{{ __('emails.tagline') }}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                        </table>
                    </td>
                </tr>

                {{-- Footer (outside the card) --}}
                <tr>
                    <td align="center" class="dd-pad" style="padding:24px 24px 8px;font-family:{!! $sans !!};">
                        @if ($footerText)
                            <p style="margin:0 0 6px;font-size:13px;line-height:1.5;color:#4a3f40;">{{ $footerText }}</p>
                        @endif
                        <p style="margin:0 0 6px;font-size:12px;line-height:1.5;color:#7d6b6d;">
                            {{ __('emails.automated_message') }}
                        </p>
                        <p style="margin:0;font-size:12px;line-height:1.5;color:#a8999b;">
                            &copy; {{ date('Y') }} {{ $projectTitle }}. {{ __('emails.all_rights_reserved') }}
                        </p>
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
