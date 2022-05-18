@component('mail::message')
<div style="width: 100%">
  <img src="{{ $logo }}" style="height: 75px; margin: auto;"/><br><br>
</div>
Beste bestuurslid<br><br>

Via de knop onderaan deze mail kan u uw wachtwoord voor onze nieuwe applicatie zelf instellen.
 1. Druk op de knop hieronder<br>
 2. Geef een wachtwoord naar keuze in<br>
 3. Bevestig het gekozen wachtwoord nog een keer<br>
 4. Druk op de knop wachtwoord instellen<br>
 5. U kan nu inloggen door uw mailadres en het gekozen wachtwoord in te geven.<br>

@component('mail::button', ['url' => env('VUE_APP_URL') . '/update-password?token='.$token])
Wachtwoord instellen
@endcomponent

Bedankt,<br>
{{ config('app.name') }}
@endcomponent