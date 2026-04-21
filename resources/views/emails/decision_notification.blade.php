<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #334155; line-height: 1.6; margin: 0; padding: 0; background-color: #f8fafc; }
        .container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #1e3a8a, #3b82f6); padding: 32px 24px; text-align: center; color: white; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.025em; }
        .content { padding: 32px 24px; }
        .intro { font-size: 16px; margin-bottom: 24px; color: #475569; }
        .decision-box { background: #f1f5f9; border-radius: 8px; padding: 24px; border-left: 4px solid #3b82f6; margin-bottom: 24px; }
        .decision-title { font-size: 18px; font-weight: 700; margin-bottom: 12px; color: #1e293b; }
        .decision-text { font-size: 14px; color: #334155; }
        .attachments { margin-bottom: 24px; padding: 0; list-style: none; }
        .attachments li { margin-bottom: 8px; display: flex; align-items: center; font-size: 13px; }
        .attachments a { color: #3b82f6; text-decoration: none; font-weight: 500; }
        .attachments a:hover { text-decoration: underline; }
        .footer { padding: 24px; text-align: center; background: #f8fafc; border-top: 1px solid #e2e8f0; }
        .btn { display: inline-block; background-color: #3b82f6; color: white !important; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 15px; margin-top: 16px; }
        .text-muted { color: #64748b; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>DAZO</h1>
        </div>
        <div class="content">
            <p class="intro">Bonjour,</p>
            <p class="intro">Une nouvelle proposition nécessite votre attention dans le cercle <strong>{{ $decision->circle->name }}</strong>.</p>
            
            <div class="decision-box">
                <div class="decision-title">{{ $decision->title }}</div>
                <div class="decision-text">
                    {!! $version->content !!}
                </div>
            </div>

            @if($version->attachments->count() > 0)
            <div style="font-weight: 600; font-size: 14px; margin-bottom: 8px; color: #1e293b;">Pièces jointes :</div>
            <ul class="attachments">
                @foreach($version->attachments as $attachment)
                <li>
                    <span>📎</span>&nbsp;
                    <a href="{{ config('app.url') . Storage::url($attachment->file_path) }}" target="_blank">
                        {{ $attachment->filename }} ({{ round($attachment->file_size / 1024) }} KB)
                    </a>
                </li>
                @endforeach
            </ul>
            @endif

            <div style="text-align: center;">
                <a href="{{ config('app.url') . '/decisions/' . $decision->id }}" class="btn">Accéder à la décision</a>
            </div>
        </div>
        <div class="footer">
            <p style="margin-bottom: 8px; font-weight: 500;">DAZO — Plateforme de Gouvernance Partagée</p>
            <p class="text-muted">Cet email vous a été envoyé car vous êtes membre du cercle {{ $decision->circle->name }}.</p>
        </div>
    </div>
</body>
</html>
