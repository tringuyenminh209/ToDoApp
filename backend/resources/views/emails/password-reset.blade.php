<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>パスワードリセット</title>
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
            color: #4CAF50;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .token-box {
            background-color: #f8f9fa;
            border: 2px solid #4CAF50;
            border-radius: 6px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .token {
            font-size: 32px;
            font-weight: bold;
            color: #4CAF50;
            letter-spacing: 4px;
            font-family: 'Courier New', monospace;
        }
        .note {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
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
        <h1>パスワードリセット</h1>

        <p>こんにちは、</p>

        <p>パスワードリセットのリクエストを受け取りました。以下の6桁のトークンをアプリに入力してください。</p>

        <div class="token-box">
            <div class="token">{{ $token }}</div>
        </div>

        <p>このトークンは <strong>60分間</strong> 有効です。</p>

        <div class="note">
            <strong>注意:</strong> このリクエストをしていない場合は、このメールを無視してください。パスワードは変更されません。
        </div>

        <div class="footer">
            <p>ご質問がございましたら、サポートチームまでお問い合わせください。</p>
            <p>© 2025 DOL LEAF. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

