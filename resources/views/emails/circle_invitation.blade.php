<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1e40af; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; border-radius: 0 0 8px 8px; }
        .button { display: inline-block; padding: 12px 24px; background: #2563eb; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; margin-top: 20px; }
        .footer { margin-top: 30px; font-size: 12px; color: #6b7280; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>DAZO</h1>
    </div>
    <div class="content">
        <h2>Bonjour,</h2>
        <p>Vous avez été invité à rejoindre le cercle <strong>{{ $circle->name }}</strong> sur la plateforme de décision DAZO.</p>
        <p>Ce cercle vous permettra de participer aux prises de décisions et de suivre les actualités de votre organisation.</p>
        <p>Pour rejoindre le cercle et créer votre compte, cliquez sur le bouton ci-dessous :</p>
        <div style="text-align: center;">
            <a href="{{ $acceptUrl }}" class="button">Rejoindre le cercle</a>
        </div>
        <p>Si le bouton ne fonctionne pas, vous pouvez copier ce lien :<br>
        <span style="font-size: 13px; color: #2563eb;">{{ $acceptUrl }}</span></p>
    </div>
    <div class="footer">
        Ceci est un message automatique envoyé par la plateforme DAZO.
    </div>
</body>
</html>
