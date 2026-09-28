## Code correctie en nogmaals testen
In Story 03 ben ik erachter gekomen dat er overal loose comparison wordt gebruikt. Hierop heb ik destijds de CLAUDE.md
aangepast, maar dit moet nog overal worden doorgevoerd:

* Zorg ervoor dat er overal strict comparison plaatsvindt (bekend: `src/Http/ErrorHandler.php`, `!(error_reporting() & $severity)`). Dit geld voor zowel PHP als Javascript;
* Onderzoek waarom het JavaScript niet werkte door een Content Security Policy-melding. Als workaround staat er nu een
`<meta http-equiv="Content-Security-Policy" content="script-src 'self'">` in `templates/layout/header.php`. Zoek de oorzaak,
los die op en bepaal of de meta-tag daarna weg kan;
* Voeg de bijna gelijke CSS-klassen `.vacancy-list`/`.vacancy-card` en `.application-list`/`.application-card` samen tot
algemene klassen (bijv. `.card-list` en `.card`) en pas de templates daarop aan;
* Voeg de schema-bestanden in `database/` (`story1.sql`, `story4.sql` en `story4b.sql`) samen tot 1 migratie (`schema.sql`).
De testdata blijft apart in `seed.sql`. Werk de installatie-instructies in de README.md daarop bij;
* Test de gehele applicatie opnieuw;