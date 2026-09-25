# CLAUDE.md

Deze repo is een **developer assessment voor werkzoeken.nl** (PHP/MySQL & HTML/CSS/JS). De originele opdracht staat in `docs/Developer Assessment.pdf`. Dat is de bron van waarheid. Twijfel je over scope, lees dan eerst die PDF.

## Opdracht (samengevat)

Een compact vacatureoverzicht en een vacaturepagina waarop je direct kunt solliciteren. In de map docs/stories vindt je individuele stories 
waarmee we dit assesment gaan implementeren;

## Harde eisen bij oplevering
- **Geen frameworks of externe libraries.** Dus geen Laravel/Symfony, geen Composer-packages (ook geen phpdotenv), geen jQuery/Bootstrap/Tailwind, geen CDN-scripts. Alleen plain PHP, SQL, HTML, CSS en vanilla JS. Composer staat wel op de machine, maar gebruik het niet voor dependencies.
- **Online hosten en toegang geven tot de scripts.**
- **Claude zal zelf geen comments toevoegen op de code. De developer zal zelf korte toelichtingen als comments in de code plaatsen.** Developer legt het *waarom* uit, vooral bij security-keuzes.

## Waarop beoordeeld wordt
1. **Structuur en leesbaarheid van de code**: duidelijke scheiding van verantwoordelijkheden, consistente naamgeving, kleine functies.
2. **Logische denkwijze en oplossingsgerichtheid.**
3. **Security**: expliciet een beoordelingspunt. Zie de checklist hieronder.
4. **Design telt niet mee.** Houd de CSS minimaal en functioneel en steek er geen tijd in.

## Security-checklist (niet overslaan)
- **SQL-injectie**: altijd **PDO met prepared statements** en gebonden parameters, nooit string-concatenatie in queries. Zet `PDO::ATTR_EMULATE_PREPARES => false` en `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION`, en gebruik `charset=utf8mb4`.
- **Zoeken met LIKE**: escape `%`, `_` en `\` in de gebruikersinput voordat je de wildcards toevoegt.
- **XSS**: alle output escapen met `htmlspecialchars($v, ENT_QUOTES, 'UTF-8')` via een kleine helper (bijv. `e()`). In JS `textContent` gebruiken in plaats van `innerHTML` met gebruikersdata.
- **CSRF**: token in de sessie en een hidden field in het sollicitatieformulier, gecontroleerd met `hash_equals()`.
- **Server-side validatie** is altijd verplicht. JS-validatie is alleen voor UX. Valideer naam (lengte), e-mail (`filter_var(..., FILTER_VALIDATE_EMAIL)`), motivatie (max. lengte) en een geldig, bestaand vacature-ID.
- **CV-upload**:
  - Controleer `$_FILES[...]['error']` en `is_uploaded_file()`.
  - Stel een maximale bestandsgrootte in (bijv. 2–5 MB) en controleer die zowel server-side als in JS.
  - Bepaal het MIME-type met `finfo` (niet `$_FILES['type']` of de extensie vertrouwen). Whitelist: PDF, en eventueel DOC/DOCX.
  - Sla het bestand op onder een **random naam** (`bin2hex(random_bytes(16))` + een veilige extensie), **buiten de webroot** of in een map waar uitvoeren/direct serveren geblokkeerd is.
  - Bewaar de originele bestandsnaam alleen als metadata in de database.
- **ID-parameters**: casten/valideren als positief integer (`filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)`). Bij een onbekend ID een nette 404.
- **Foutafhandeling**: in productie geen stacktraces of DB-fouten naar de gebruiker (`display_errors=0`, loggen). Toon een generieke foutmelding.
- **Credentials**: DB-gegevens in `.env` (staat in `.gitignore`), met een eigen minimale parser, zonder library. Commit een `.env.example`.
- **Na een POST**: Post/Redirect/Get, of een JSON-response bij een fetch-submit, om dubbele inzendingen te voorkomen.
- Security headers waar dat eenvoudig kan (`X-Content-Type-Options: nosniff`, `X-Frame-Options`/CSP-basis) en sessiecookies met `HttpOnly`, `SameSite=Lax` en `Secure` online.

## Omgeving
- Lokaal: PHP 8.3, MySQL 8.0, Ubuntu. Het project staat in `/var/www/html/werkzoeken-assessment` (Apache-webroot).
- IDE: PhpStorm (`.idea/` staat in `.gitignore`).
- Gebruik PHP 8-features waar dat de leesbaarheid helpt: `declare(strict_types=1);`, typed parameters en return types, `match`, named arguments.
- Tekst in de UI is **Nederlands**.

## Voorgestelde structuur (nog niet gebouwd, aanpasbaar)
```
public/                 # webroot: alleen dit is publiek bereikbaar
  index.php             # vacatureoverzicht + zoekformulier (wat/waar)
  vacature.php          # detailpagina (?id=)
  solliciteer.php       # POST-endpoint voor sollicitaties
  assets/css/style.css
  assets/js/apply.js    # toont formulier, client-side validatie
src/                    # PHP-logica, niet publiek
  config.php            # .env laden, instellingen
  Database.php          # PDO-verbinding
  VacancyRepository.php # queries voor vacatures/zoeken
  ApplicationRepository.php
  helpers.php           # e(), csrf-functies, redirect, etc.
templates/              # gedeelde header/footer/partials
database/
  schema.sql            # CREATE TABLE-statements
  seed.sql              # 25 bedrijven, 100 vacatures
storage/uploads/        # CV's, buiten de webroot, niet in git
docs/                   # opdracht-PDF
```
Als hosting geen aparte webroot toelaat: zet de niet-publieke mappen dicht met `.htaccess` (`Require all denied`).

## Voorgesteld datamodel
- `companies` (id, name, …)
- `vacancies` (id, company_id FK, title, description, location, contact_name, contact_email/phone, created_at), met indexen op `title`, en `location`, eventueel FULLTEXT.
- `applications` (id, vacancy_id FK, name, email, motivation, cv_path, cv_original_name, created_at)
- InnoDB, `utf8mb4_unicode_ci`, foreign keys.

## Werkafspraken voor Claude
- Houd het **compact**: de opdracht vraagt om een eenvoudige oplossing, geen over-engineering (geen eigen mini-framework of router tenzij het echt helpt).
- Code en identifiers in het Engels, UI-teksten in het Nederlands, comments in het Nederlands maar consistent.
- Maak gebruik van SOLID design principes;
- Voeg bij het opleveren een README toe met setup-instructies (schema + seed importeren, `.env` invullen) en de online URL.
- Stel per story een implementatieplan op in kleine logische delen. Begin nooit zelf aan een implementatie. Ik moet altijd toestemming geven om te starten met implementeren;