<x-mail::message>
# Bonjour {{ $user->name }},

Ceci est un rappel concernant la décision : **{{ $decision->title }}**.

La phase actuelle (**{{ $decision->status->value }}**) arrive bientôt à échéance. Votre participation est attendue afin de faire progresser le processus.

**Échéance :** {{ $deadline }}

<x-mail::button :url="$url">
Accéder à la décision
</x-mail::button>

Merci de votre contribution à la gouvernance de l'organisation.

Cordialement,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
