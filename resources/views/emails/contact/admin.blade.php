@component('mail::message')
# Nouveau message de contact

Bonjour,

Vous avez reçu un nouveau message de la part de **{{ $userName }}** ({{ $userEmail }}) via le centre d'aide DAZO.

**Sujet :** {{ $subjectLine }}

**Message :**
@component('mail::panel')
{{ $messageBody }}
@endcomponent

Pour répondre à cet utilisateur, vous pouvez simplement répondre à cet email.

Merci,<br>
L'équipe {{ config('app.name') }}
@endcomponent
