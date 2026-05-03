<x-mail::message>
{{ $body }}

<x-mail::button :url="$url">
Accéder à la décision
</x-mail::button>

Merci de votre contribution à la gouvernance de l'organisation.

Cordialement,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
