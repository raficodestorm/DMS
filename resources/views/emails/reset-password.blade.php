<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset your password</title>
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            width: 100% !important;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        .wrapper {
            background-color: #f1f5f9;
            padding: 40px 20px;
        }

        .container {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            max-width: 540px;
            margin: 0 auto;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.03);
        }

        .header {
            background: linear-gradient(135deg, #3131ff, #ae04f1);
            padding: 35px 40px;
            text-align: center;
        }

        .header h1 {
            color: #ffffff;
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .content {
            padding: 40px;
        }

        .greeting {
            color: #0f172a;
            font-size: 18px;
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 16px;
        }

        .body-text {
            color: #475569;
            font-size: 15px;
            line-height: 1.6;
            margin-top: 0;
            margin-bottom: 24px;
        }

        .btn-wrapper {
            text-align: center;
            margin: 32px 0;
        }

        .btn {
            background-color: #3131ff;
            background: linear-gradient(90deg, #3131ff, #3d60fc);
            border-radius: 10px;
            color: #ffffff !important;
            display: inline-block;
            font-size: 15px;
            font-weight: 600;
            line-height: 50px;
            text-align: center;
            text-decoration: none;
            width: 220px;
            -webkit-text-size-adjust: none;
            box-shadow: 0 4px 15px rgba(49, 49, 255, 0.25);
            transition: all 0.2s ease;
        }

        .info-box {
            background-color: #f8fafc;
            border-left: 4px solid #ae04f1;
            padding: 16px;
            border-radius: 0 8px 8px 0;
            margin-bottom: 28px;
        }

        .info-box p {
            color: #64748b;
            font-size: 13px;
            line-height: 1.5;
            margin: 0;
        }

        .divider {
            border-top: 1px solid #e2e8f0;
            margin: 32px 0 24px 0;
        }

        .fallback {
            color: #94a3b8;
            font-size: 12px;
            line-height: 1.5;
            word-break: break-all;
        }

        .fallback a {
            color: #3131ff;
            text-decoration: none;
        }

        .footer {
            padding: 24px 40px 40px 40px;
            text-align: center;
        }

        .footer p {
            color: #94a3b8;
            font-size: 12px;
            line-height: 1.5;
            margin: 0 0 8px 0;
        }

        @media only screen and (max-width: 600px) {
            .wrapper {
                padding: 20px 10px;
            }

            .content {
                padding: 24px;
            }

            .header {
                padding: 24px;
            }
        }
    </style>
</head>

<body>
    <table cellpadding="0" cellspacing="0" border="0" width="100%" class="wrapper">
        <tr>
            <td align="center">
                <table cellpadding="0" cellspacing="0" border="0" width="100%" class="container">
                    <!-- Header -->
                    <tr>
                        <td class="header">
                            <h1>{{ config('app.name') }}</h1>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td class="content">
                            <p class="greeting">Hello {{ $user->fullname ?? 'there' }},</p>
                            <p class="body-text">
                                We received a request to reset the password for your account. You can reset your
                                password by clicking the button below:
                            </p>

                            <!-- Button CTA -->
                            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr>
                                    <td class="btn-wrapper">
                                        <a href="{{ $url }}" class="btn">Reset Password</a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Info block -->
                            <div class="info-box">
                                <p>This password reset link will expire in <strong>{{ $expire }} minutes</strong>.</p>
                                <p style="margin-top: 4px;">If you did not request a password reset, no further action
                                    is required.</p>
                            </div>

                            <p class="body-text" style="margin-bottom: 0;">
                                Regards,<br>
                                <strong>{{ config('app.name') }} Operations Team</strong>
                            </p>

                            <div class="divider"></div>

                            <!-- Fallback Link -->
                            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr>
                                    <td class="fallback">
                                        If you're having trouble clicking the "Reset Password" button, copy and paste
                                        the URL below into your web browser:<br>
                                        <a href="{{ $url }}">{{ $url }}</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <!-- Footer -->
                <table cellpadding="0" cellspacing="0" border="0" width="100%" class="footer">
                    <tr>
                        <td>
                            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                            <p>This is an automated system email. Please do not reply directly to this message.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>