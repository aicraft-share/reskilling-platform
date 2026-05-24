<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #22c55e; padding-bottom: 15px; }
        .button { display: inline-block; padding: 10px 20px; background-color: #22c55e; color: white !important; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .footer { margin-top: 30px; font-size: 12px; color: #718096; border-top: 1px solid #edf2f7; padding-top: 20px; }
        .info-box { background-color: #f0fdf4; border: 1px solid #bbf7d0; padding: 15px; border-radius: 6px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="color: #15803d;">AI Craft Reskilling - 課題提出のご案内</h2>
        </div>
        <p>{{ $teacherName }} 講師</p>
        <p>担当生徒の {{ $studentName }} さんから課題が提出されました。</p>
        
        <div class="info-box">
            <strong>対象レクチャー:</strong> {{ $lectureTitle }}<br>
            <strong>提出日時:</strong> {{ $submittedAt }}
        </div>
        
        <p>内容を確認し、レビューまたは採点をお願いいたします。</p>
        
        <a href="{{ route('teacher.submissions.index') }}" class="button">課題のレビューを行う</a>
        
        <div class="footer">
            <p>※ 本メールはシステムより自動送信されています。<br>
            ※ 通知設定はマイページの「設定」から変更いただけます。</p>
            <p>&copy; {{ date('Y') }} AI Craft Reskilling</p>
        </div>
    </div>
</body>
</html>
