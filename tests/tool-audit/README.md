# Tool audit tests (Phase 01 baseline)

Browser tests against the **dev site** only. Re-run in Phase 05 to produce the "after" inventory.

```bash
W=/tmp/tool-audit && python3 make-fixtures.py $W
node studio-test.js $W      # image studio: valid/invalid/large/batch/cancel/watermark/bg-remover
node calc-test.js $W        # profit, invoice (incl. print PDF), UTM, 100 MB batch limit
node responsive-test.js $W  # overflow/tap targets at 360/768/1024, keyboard order
```

Requires Playwright with Chromium, plus Python with Pillow and NumPy. Results and the Phase 01 baseline are in `docs/seller-toolkit/TOOL-AUDIT.md`.
