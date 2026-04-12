<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workshop Reminder</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f9fafb; margin: 0; padding: 0; }
        .wrapper { max-width: 560px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .header { background: linear-gradient(135deg, #4f46e5, #6d28d9); padding: 32px 40px; }
        .header h1 { margin: 0; font-size: 20px; font-weight: 700; color: #ffffff; }
        .header p  { margin: 4px 0 0; font-size: 13px; color: #c7d2fe; }
        .body { padding: 32px 40px; }
        .body p { margin: 0 0 16px; font-size: 15px; color: #374151; line-height: 1.6; }
        .card { background: #f0f4ff; border-left: 4px solid #4f46e5; border-radius: 8px; padding: 16px 20px; margin: 24px 0; }
        .card .title { font-size: 16px; font-weight: 700; color: #1e1b4b; margin: 0 0 8px; }
        .card .meta  { font-size: 13px; color: #4f46e5; margin: 0; }
        .footer { background: #f9fafb; border-top: 1px solid #e5e7eb; padding: 20px 40px; text-align: center; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>Internal Academy</h1>
            <p>Your workshop is tomorrow!</p>
        </div>

        <div class="body">
            <p>Hi {{ $user->name }},</p>
            <p>Just a friendly reminder that you are registered for a workshop happening <strong>tomorrow</strong>:</p>

            <div class="card">
                <p class="title">{{ $workshop->title }}</p>
                <p class="meta">
                    📅 {{ $workshop->start_time->format('l, d F Y') }}
                    &nbsp;·&nbsp;
                    🕐 {{ $workshop->start_time->format('H:i') }} – {{ $workshop->end_time->format('H:i') }}
                </p>
            </div>

            <p>We look forward to seeing you there. If you can no longer attend, please cancel your registration so your spot can go to someone on the waiting list.</p>
            <p>See you tomorrow!<br><strong>The Internal Academy Team</strong></p>
        </div>

        <div class="footer">
            You are receiving this email because you registered for a workshop on Internal Academy.
        </div>
    </div>
</body>
</html>
