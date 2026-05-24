<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; }
        .header { margin-bottom: 20px; }
        .button { display: inline-block; padding: 10px 20px; background-color: #2563eb; color: white !important; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .footer { margin-top: 30px; font-size: 12px; color: #718096; border-top: 1px solid #edf2f7; padding-top: 20px; }
        .message-preview { background-color: #f8fafc; border-left: 4px solid #cbd5e1; padding: 15px; margin: 20px 0; font-style: italic; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>AI Craft Reskilling - 新着メッセージ</h2>
        </div>
        <p>{{ $recipientName }} 様</p>
        <p>{{ $senderName }} さんから新しいメッセージが届いています。</p>
        
        <div class="message-preview">
            {{ Str::limit($messageContent, 100) }}
        </div>
        
        <p>詳細は以下のボタンよりチャット画面にてご確認ください。</p>
        
        <a href="{{ route('chats.show', $threadId) }}" class="button">チャットを確認する</a>
        
        <div class="footer">
            <p>※ 本メールはシステムより自動送信されています。<br>
            ※ 通知設定はマイページの「設定」から変更いただけます。</p>
            <p>&copy; {{ date('Y') }} AI Craft Reskilling</p>
        </div>
    </div>
</body>
</html>
