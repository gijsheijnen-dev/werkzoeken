## Code correctie en nogmaals testen
In Story 03 ben ik erachter gekomen dat er overal loose comparison wordt gebruikt. Hierop heb ik destijds de CLAUDE.md
aangepast, maar dit moet nog overal worden doorgevoerd:

* Zorg ervoor dat er overal strict comparison plaatsvindt (bekend: `src/Http/ErrorHandler.php`, `!(error_reporting() & $severity)`). Dit geld voor zowel PHP als Javascript;
* Onderzoek waarom het JavaScript niet werkte door een Content Security Policy-melding. Als workaround staat er nu een
`<meta http-equiv="Content-Security-Policy" content="script-src 'self'">` in `templates/layout/header.php`. Zoek de oorzaak,
los die op en bepaal of de meta-tag daarna weg kan;
* Test de gehele applicatie opnieuw;