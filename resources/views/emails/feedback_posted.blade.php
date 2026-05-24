<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #3b82f6; padding-bottom: 15px; }
        .button { display: inline-block; padding: 10px 20px; background-color: #3b82f6; color: white !important; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .footer { margin-top: 30px; font-size: 12px; color: #718096; border-top: 1px solid #edf2f7; padding-top: 20px; }
        .info-box { background-color: #eff6ff; border: 1px solid #bfdbfe; padding: 15px; border-radius: 6px; margin: 20px 0; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 14px; font-weight: bold; }
        .status-passed { background-color: #dcfce7; color: #166534; }
        .status-revision { background-color: #fef9c3; color: #854d0e; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="color: #1e40af;">AI Craft Reskilling - 判定・フィードバックのご案内</h2>
        </div>
        <p>{{ $studentName }} 様</p>
        <p>提出いただいた課題に対して、講師より判定およびフィードバックが届いています。</p>
        
        <div class="info-box">
            <strong>対象レクチャー:</strong> {{ $lectureTitle }}<br>
            <strong>判定結果:</strong> 
            @if($status === 'passed')
                <span class="status-badge status-passed">合格</span>
            @else
                <span class="status-badge status-revision">要修正</span>
            @endif
        </div>
        
        <p>詳細は以下のボタンよりマイページにてご確認ください。</p>
        
        <a href="{{ route('student.submissions.index') }}" class="button">判定結果を確認する</a>
        
        <div class="footer">
            <p>※ 本メールはシステムより自動送信されています。<br>
            ※ 通知設定はマイページの「設定」から変更いただけます。</p>
            <p>&copy; {{ date('Y') }} AI Craft Reskilling</p>
        </div>
    </div>
</body>
</html>
