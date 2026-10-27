# 5-minute demo script

## Before you start (do this once, 10 minutes early)

```bash
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

Open three browser windows (use a private window for two of them so sessions do not clash):
grower@example.com, collector@example.com, buyer@example.com. Password: `password`.
Check beforehand that the heavy-weight and short-drying messages below really raise a flag; adjust the numbers if the thresholds differ.

## Script

| Time | Do | Say |
| --- | --- | --- |
| 0:00 | Show the welcome page. Switch language to Sinhala, then back. | "A grower can use this on any phone, in Sinhala, Tamil or English." |
| 0:30 | Grower: open the SMS simulator. Send `BATCH P1 40KG 12OCT DRY3 ALBA`. | "One text message. The system replies with a batch code." |
| 1:00 | Send the Sinhala version: `තොගය P2 35KG 10OCT DRY4 C5`. Open My batches. | "Same server code for every language. The batch is listed." |
| 1:30 | Send `BATCH P1 450KG 12OCT DRY1`. Show the flags on My batches. | "Six simple rules flag odd values. A flag means check, not reject." |
| 2:00 | Send `BATCH P1 40KG`. Show the error reply. | "A bad message gets a clear reply saying what is missing." |
| 2:30 | Send `HAND CIN-0001 0770000002`. | "The grower hands the batch to a named collector." |
| 3:00 | Collector window: show the desk, send `OK CIN-0001` (or press confirm). | "The collector confirms receipt; the grower is notified." |
| 3:30 | Buyer window: open `/verify/CIN-0001` (scan the QR on the passport). | "The buyer sees batch facts only. No grower name." |
| 4:00 | Terminal: `php artisan cinnamon:scenario-test`. Show the table. | "RQ1: 116 scripted runs. The test found two silent SMS bugs; I fixed them and re-ran. Slips caught rose from 65.7% to 82.9%, level with the app form." |
| 4:30 | Show the RQ2 numbers (poster or `php artisan cinnamon:simulate --seed=42`). | "RQ2: recall 100%, but that is by design. Precision is 73.3% plus or minus 8.1 over 10 runs." |

## Likely questions

- **Why synthetic data?** No real grower data or ethics approval for human participants; the study is a prototype and its limits are stated.
- **Is the SMS real?** No. The simulator calls the same server code a gateway would. A live gateway is future work.
- **Is 100% recall impressive?** No. Anomalies are generated to cross the same thresholds the rules use, so it is expected. The useful finding is precision under noise.
- **Why only 16 mistakes?** They are common input slips chosen by the researcher; the percentages describe these cases, not real error rates.
- **Where is the human usability result?** Not collected. A study mode with SUS and timings is built for future work.
- **What would you do next?** Live gateway, thresholds from real batches, a small pilot with growers.
