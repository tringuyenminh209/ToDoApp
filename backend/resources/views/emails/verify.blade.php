<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メールアドレス確認</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2196F3;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .button {
            display: inline-block;
            padding: 14px 28px;
            background-color: #2196F3;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
        }
        .button:hover {
            background-color: #1976D2;
        }
        .note {
            background-color: #e3f2fd;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>メールアドレス確認</h1>

        <p>こんにちは、</p>

        <p>DOL LEAFへご登録いただき、ありがとうございます！</p>

        <p>以下のリンクをクリックして、メールアドレスを確認してください：</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $verificationUrl }}" class="button">メールアドレスを確認</a>
        </div>

        <p>リンクがクリックできない場合は、以下のURLをブラウザにコピー＆ペーストしてください：</p>

        <p style="word-break: break-all; background-color: #f5f5f5; padding: 10px; border-radius: 4px;">
            {{ $verificationUrl }}
        </p>

        <div class="note">
            <strong>注意:</strong> このリンクは <strong>60分間</strong> 有効です。
        </div>

        <div class="footer">
            <p>このリクエストをしていない場合は、このメールを無視してください。</p>
            <p>© 2025 DOL LEAF. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
