# Werkzoeken assessment

## Database installeren

### Vereisten
- MySQL 8.0
- Een bestaande, lege database met een gebruiker die er tabellen in mag aanmaken
- Een ingevulde `.env` in de projectroot (kopieer `.env.example` en vul de `DB_*`-waarden in)

### SQL-bestanden
Het schema is per story opgebouwd. De bestanden staan in `database/` en moeten in deze volgorde worden uitgevoerd:

| Volgorde | Bestand       | Inhoud                                                             |
|----------|---------------|--------------------------------------------------------------------|
| 1        | `story1.sql`  | Tabellen `companies` en `vacancies`                                |
| 2        | `seed.sql`    | Testdata: 25 bedrijven en 100 vacatures                            |
| 3        | `story4.sql`  | Tabel `applications` (sollicitaties)                               |
| 4        | `story4b.sql` | Tabellen `admins` en `login_attempts`, plus het beheeraccount `guest` |

`story4.sql` verwijst via een foreign key naar `vacancies` en moet daarom na `story1.sql` worden uitgevoerd. `story4b.sql` heeft geen foreign keys naar de andere tabellen.

### Schone installatie
Voer vanuit de projectroot uit:

```bash
set -a; . ./.env; set +a
export MYSQL_PWD="$DB_PASS"

for file in story1 seed story4 story4b; do
    mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" "$DB_NAME" < "database/$file.sql"
done
```

`MYSQL_PWD` zorgt ervoor dat het wachtwoord niet in de proceslijst of shellhistorie terechtkomt.

### Bestaande installatie bijwerken
Voer alleen de bestanden uit die nog niet zijn uitgevoerd, in de volgorde uit de tabel hierboven. Bijvoorbeeld als alleen het beheer nog ontbreekt:

```bash
set -a; . ./.env; set +a
MYSQL_PWD="$DB_PASS" mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" "$DB_NAME" < database/story4b.sql
```

Let op: elk bestand begint met `DROP TABLE IF EXISTS` voor zijn eigen tabellen. Opnieuw uitvoeren van `story4.sql` verwijdert alle opgeslagen sollicitaties; opnieuw uitvoeren van `story4b.sql` zet de beheeraccounts terug naar alleen `guest` en wist alle geregistreerde inlogpogingen.

### Opnieuw installeren
`story1.sql` verwijdert `vacancies` en `companies`, maar dat lukt niet zolang `applications` er via de foreign key naar verwijst. Verwijder daarom eerst de tabel `applications` en voer daarna de schone installatie uit:

```bash
set -a; . ./.env; set +a
MYSQL_PWD="$DB_PASS" mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" "$DB_NAME" -e "DROP TABLE IF EXISTS applications"
```

Hierbij gaan alle vacatures en sollicitaties verloren. De tabellen uit `story4b.sql` hebben geen foreign keys en hoeven niet eerst verwijderd te worden; de schone installatie maakt ze opnieuw aan. Geüploade CV's in `storage/uploads/` worden niet automatisch verwijderd.

## Opslagmap voor CV's

Geüploade CV's worden opgeslagen in `storage/uploads/`, buiten de webroot `public/`. Ze zijn dus niet via een URL op te vragen. De inhoud van deze map staat niet in git; alleen `.gitkeep` houdt de map aanwezig.

De webserver moet in deze map kunnen schrijven. Draait Apache als `www-data`, voer dan vanuit de projectroot uit:

```bash
sudo chgrp www-data storage/uploads
sudo chmod 2770 storage/uploads
```

- `2770`: eigenaar en groep `www-data` mogen lezen, schrijven en de map openen; anderen hebben geen toegang.
- De `2` (setgid) zorgt dat nieuwe bestanden ook de groep `www-data` krijgen.

Draait PHP bij de hostingpartij onder dezelfde gebruiker als de eigenaar van de bestanden, dan is `chmod 700 storage/uploads` voldoende.

## Beheer

Ingestuurde sollicitaties zijn te bekijken in een afgeschermd beheergedeelte op `/beheer/` (lokaal: `http://werkzoeken.local/beheer/`). Er staat bewust geen link naar deze pagina op de publieke site.

### Inloggen
`story4b.sql` maakt één beheeraccount aan:

| Gebruikersnaam | Wachtwoord         |
|----------------|--------------------|
| `guest`        | `@$YxoAeDN6SKesG#` |

Het wachtwoord staat in de database alleen als bcrypt-hash (`password_hash`). De gebruikersnaam is niet hoofdlettergevoelig, het wachtwoord wel.

### Wat je ziet
- Alle sollicitaties, nieuwste eerst: datum, vacature en bedrijf, naam, e-mailadres, de eerste 100 tekens van de motivatie en een knop om het CV te downloaden.
- CV's worden alleen aan ingelogde beheerders uitgeleverd, via `/beheer/cv.php`. De bestanden zelf staan buiten de webroot.

### Beveiliging
- **Inlogbeperking:** na 5 mislukte inlogpogingen binnen 15 minuten wordt het IP-adres 15 minuten geblokkeerd, ook voor het juiste wachtwoord. Een geslaagde inlog wist de mislukte pogingen van dat IP-adres.
- **Automatisch uitloggen:** na 30 minuten zonder activiteit moet je opnieuw inloggen.
- **Sessie:** bij inloggen en uitloggen krijgt de sessie een nieuw ID. Uitloggen kan alleen via de uitlogknop (POST met CSRF-token).

### IP-adres vrijgeven
Is een IP-adres geblokkeerd, dan kan het direct worden vrijgegeven door de mislukte pogingen te verwijderen:

```bash
set -a; . ./.env; set +a
MYSQL_PWD="$DB_PASS" mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" "$DB_NAME" -e "DELETE FROM login_attempts WHERE ip_address = '127.0.0.1'"
```

Vervang `127.0.0.1` door het geblokkeerde IP-adres, of laat de `WHERE` weg om alle blokkades op te heffen.
