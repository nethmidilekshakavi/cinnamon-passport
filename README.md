# SMS-First Batch Quality Passport for Ceylon Cinnamon Smallholders

Final year project, Canterbury Christ Church University (Saegis Campus), 2026.
T.K.N.D. Kavindi (100197717). Supervisor: Ms. Ashani Gallage.

A prototype that lets a cinnamon grower record a harvest batch with a plain text message (no smartphone or data plan), checks it with simple quality rules, hands it over to a collector, and gives a buyer a public passport page with a QR code.

**All data is synthetic. SMS is simulated: there is no live SMS gateway.**

## Research questions

- **RQ1** Is SMS-first entry as reliable as an app form? Tested with a scripted scenario test (4 tasks x 16 typical mistakes x 2 channels = 116 runs). A usability study mode (timed tasks, SUS) is also built in.
- **RQ2** How precisely and completely can simple rules flag suspicious batches? Tested on synthetic batches with injected anomalies and noise.

## Stack

Laravel 13, Vue 3 + Inertia, Tailwind CSS, SQLite, Pest. Screens and SMS replies are in English, Sinhala and Tamil.

## Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite        # Windows PowerShell: New-Item database/database.sqlite
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

Open http://localhost:8000.

### Demo accounts (password: `password`)

| Role | Email | Phone |
| --- | --- | --- |
| Grower | grower@example.com | 0770000001 |
| Collector | collector@example.com | 0770000002 |
| Buyer | buyer@example.com | 0770000003 |

## SMS formats (use the SMS simulator at `/sms` when logged in as a grower or collector)

| Action | Who | Message |
| --- | --- | --- |
| Record a batch | Grower | `BATCH P1 40KG 12OCT DRY3 ALBA` |
| Request handover | Grower | `HAND CIN-0001 0770000002` (collector's phone) |
| Confirm receipt | Collector | `OK CIN-0001` |

A batch message is: command, plot, weight (`KG`), harvest date (`12OCT`), drying days (`DRY3`) and an optional grade (`ALBA`, `C5`, `C4`, `H1`). Order after the plot does not matter, and the year is not typed. Sinhala and Tamil commands work too (`තොගය`, `භාර`, `තහවුරු`, `தொகுதி`, `ஒப்படை`, `உறுதி`).

## Quality rules (six)

`WEIGHT_HIGH`, `DRYING_SHORT`, `DRYING_LONG`, `LATE_LOG`, `DUPLICATE`, `WEIGHT_JUMP`. A flag means "check this batch", not "reject it".

## Public passport

Every batch has a code such as `CIN-0001` and a public page at `/verify/CIN-0001`. It shows batch facts only, never the grower's name or notes.

## Experiments

```bash
# RQ2: precision and recall on 300 synthetic batches (add --save to store them in the database)
php artisan cinnamon:simulate --growers=30 --batches=10 --anomaly-rate=0.15 --noise-rate=0.05 --seed=42

# RQ1: scripted scenario test, writes storage/app/rq1/scenario-results.csv
php artisan cinnamon:scenario-test
```

Usability study mode (timed tasks, SUS, CSV export) is at `/study` for growers.

## Tests

```bash
php artisan test
```

## Limits

Synthetic data only, thresholds are assumed rather than learned from real growers, SMS is simulated, and recall in RQ2 is partly circular because anomalies are generated to cross the same thresholds the rules use.

Next steps: a live SMS gateway, thresholds from real data, and a pilot with growers.
