<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { border-bottom: 2px solid #4f46e5; padding-bottom: 10px; margin-bottom: 20px; }
        .footer { font-size: 0.8em; color: #777; margin-top: 30px; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{ config('app.name') }} - Export RGPD</h2>
        </div>
        <p>Bonjour {{ $user->name }},</p>
        <p>Conformément à votre demande, vous trouverez ci-joint l'ensemble des données personnelles vous concernant présentes sur la plateforme <strong>{{ config('app.name') }}</strong>.</p>
        <p>Ce fichier au format JSON contient :</p>
        <ul>
            <li>Vos informations de profil</li>
            <li>Vos adhésions aux cercles</li>
            <li>Vos propositions de décisions et participations</li>
            <li>Vos retours publics et échanges</li>
        </ul>
        <p>Si vous n'êtes pas à l'origine de cette demande, veuillez contacter un administrateur immédiatement.</p>
        <div class="footer">
            <p>L'équipe {{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>
