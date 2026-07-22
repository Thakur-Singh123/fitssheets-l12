<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Password Reset OTP</title>
        <style>
            body {
                margin: 0;
                padding: 0;
                background:#ffffff !important;
                font-family: Arial, Helvetica, sans-serif;
            }
            .container {
                max-width: 600px;
                padding: 25px 30px;
                background:#ffffff;   
                border-radius: 0;
                margin-left: 0;
            }
            .brand {
                font-size: 22px;
                font-weight: bold;
                color: #111;
                margin-bottom: 28px;
                letter-spacing: 0.5px;
            }
            .title {
                font-size: 18px;
                font-weight: 600;
                color: #222;
                margin-bottom: 6px;
            }
            .subtitle {
                font-size: 14px;
                color: #555;
                margin-bottom: 20px;
                line-height: 1.5;
            }
            .otp {
                display: inline-block;
                padding: 8px 16px;
                font-size: 18px;
                font-weight: 700;
                color: #000;
                background: #f5f5f5;
                border-radius: 4px;
                letter-spacing: 3px;
                border:1px solid #ddd;
                margin-bottom: 14px;
            }
            .expire {
                font-size: 13px;
                color: #444;
                margin-bottom: 18px;
            }
            .note {
                font-size: 12px;
                color: #666;
                line-height: 1.5;
            }
            .team {
                font-size: 12px;
                color: #aaa;
                margin-top: 25px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="brand">Fitssheets</div>
            <div class="title">Password Reset Code</div>
            <div class="subtitle">Use the OTP below to reset your password.</div>
            <div class="otp">{{ $otp }}</div>
            <div class="expire">This OTP is valid for <strong>5 minutes</strong>.</div>
            <div class="note">
                If you did not request this password reset, you can safely ignore this email.
            </div>
            <div class="team">Fitssheets Support Team</div>
        </div>
    </body>
</html>