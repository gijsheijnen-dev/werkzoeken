# Werkzoeken assessment

## Database installeren

### Vereisten
- MySQL 8.0
- Een bestaande, lege database met een gebruiker die er tabellen in mag aanmaken
- Een ingevulde `.env` in de projectroot (kopieer `.env.example` en vul de `DB_*`-waarden in)

### SQL-bestanden
Het schema is per story opgebouwd. De bestanden staan in `database/` en moeten in deze volgorde worden uitgevoerd:

| Volgorde | Bestand     | Inhoud                                          |
|----------|-------------|-------------------------------------------------|
| 1        | `story1.sql` | Tabellen `companies` en `vacancies`             |
| 2        | `seed.sql`   | Testdata: 25 bedrijven en 100 vacatures         |
| 3        | `story4.sql` | Tabel `applications` (sollicitaties)            |

`story4.sql` verwijst via een foreign key naar `vacancies` en moet daarom na `story1.sql` worden uitgevoerd.

### Schone installatie
Voer vanuit de projectroot uit:

```bash
set -a; . ./.env; set +a
export MYSQL_PWD="$DB_PASS"

for file in story1 seed story4; do
    mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" "$DB_NAME" < "database/$file.sql"
done
```

`MYSQL_PWD` zorgt ervoor dat het wachtwoord niet in de proceslijst of shellhistorie terechtkomt.

### Bestaande installatie bijwerken
Staan `companies` en `vacancies` er al, voer dan alleen de nieuwe bestanden uit. Voor de sollicitaties is dat:

```bash
set -a; . ./.env; set +a
MYSQL_PWD="$DB_PASS" mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" "$DB_NAME" < database/story4.sql
```

Let op: `story4.sql` begint met `DROP TABLE IF EXISTS applications`. Opnieuw uitvoeren verwijdert alle opgeslagen sollicitaties.

### Opnieuw installeren
`story1.sql` verwijdert `vacancies` en `companies`, maar dat lukt niet zolang `applications` er via de foreign key naar verwijst. Verwijder daarom eerst de tabel `applications` en voer daarna de schone installatie uit:

```bash
set -a; . ./.env; set +a
MYSQL_PWD="$DB_PASS" mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" "$DB_NAME" -e "DROP TABLE IF EXISTS applications"
```

Hierbij gaan alle vacatures en sollicitaties verloren. Geüploade CV's in `storage/uploads/` worden niet automatisch verwijderd.

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
