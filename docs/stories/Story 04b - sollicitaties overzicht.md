## Sollicitaties overzicht (beheer)
Assessors moeten de ingestuurde sollicitaties kunnen bekijken. Deze pagina is alleen toegankelijk na inloggen.

### Inloggen
* Maak een inlogpagina met een gebruikersnaam en wachtwoord.
* Sla beheerders op in MySQL; wachtwoorden worden alleen gehasht opgeslagen (`password_hash` / `password_verify`).
* Beheerders worden aangemaakt via een script of SQL, er is geen registratiepagina.
* Toon bij een mislukte inlog één algemene foutmelding, zonder te verraden of de gebruikersnaam bestaat.
* Beperk het aantal mislukte inlogpogingen om brute-force te voorkomen.
* Vernieuw het sessie-ID na een geslaagde inlog en bescherm het inlogformulier met een CSRF-token.
* Voeg een uitlogknop toe die de sessie beëindigt.

### Overzicht
* Toon een lijst van alle sollicitaties, nieuwste eerst.
* Toon per sollicitatie: datum, vacature (titel en bedrijf), naam, e-mail, motivatie en een link naar het CV.
* Niet-ingelogde bezoekers worden doorgestuurd naar de inlogpagina.

### CV downloaden
* Een CV is alleen te downloaden door een ingelogde beheerder.
* De CV's blijven buiten de webroot staan en worden via PHP uitgeleverd, met de originele bestandsnaam.
