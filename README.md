# wbr-pcon-section-builder

Generator tool for two WBR CMS sections used across ProcureCon event sites:

- **Coming Soon** — a teaser block for an upcoming event (logo, dates, venue, CTA button)
- **Check Out More Events** — a multi-event grid linking to related events

Live app: https://pconrebrand.wbresearch.com/app-coming-soon-builder

---

## Files

| File | Purpose |
|---|---|
| `events-section-generator.html` | v1 — static presets, no live fetch |
| `events-section-generator-v2.html` | v2 — live fetch via PHP proxy |
| `events-proxy.php` | Server-side proxy for scraping event dates/venues/colors |

---

## How to use

### 1. Open the generator

Open either HTML file in a browser locally, or paste it into a CMS page for hosted access.

Both files are **body-content only** — no `<!DOCTYPE>`, `<html>`, `<head>`, or `<body>` tags. Bootstrap v4.6.2 and jQuery are already loaded by the WBR CMS, so no CDN links are needed in production.

### 2. Pick a preset

Select an event from the dropdown. Presets are grouped by region (EU / UK and USA) and cover all active ProcureCon events. Selecting a preset fills in the brand colors, logo, date, venue, and event URL automatically.

### 3. Choose a section type

Use the **Coming Soon** or **Check Out More Events** tab to switch between section types.

- **Coming Soon** has two layout variants (CS-1: full-width hero, CS-2: side-by-side with image)
- **Check Out More Events** lets you add multiple events as cards

### 4. Generate and copy

Click **Generate HTML**, review the output, then click **Copy** to copy it to the clipboard. Paste directly into the WBR CMS page editor.

---

## v2 — Live fetch (PHP proxy)

v2 adds a **↻ Live** button next to each event that fetches the current date, venue, and brand colors directly from the live event site, so you don't need to update presets manually.

### Setting up the proxy

1. Upload `events-proxy.php` to your FTP server. The hosted path for the WBR instance is:
   ```
   /UploadedFiles/EventPage/brands/procurecon/events-proxy/events-proxy.php
   ```
2. In v2, the **Proxy URL** field defaults to the hosted path above. Change it if you uploaded it elsewhere.
3. Click **Test Connection** to confirm the proxy is reachable. A green ✓ means it's live.
4. Click **↻ Live** on any event card to fetch fresh data, or **↻ Refresh All** to update every event at once.

### What the proxy does

The proxy accepts a `?url=` query param, fetches the target event page server-side (avoiding CORS), and returns a JSON object with:

```json
{
  "status": "ok",
  "date": "...",
  "venue": "...",
  "colors": {
    "--event-header-color": "...",
    "--event-color-primary": "...",
    "--event-color-secondary": "...",
    "--event-color-tertiary": "..."
  }
}
```

When called with no `?url=`, it returns `{"status":"ok"}` — used by the Test Connection health check.

---

## Adding a new event preset

In the PRESETS array (near the top of the JS in either file), add an entry:

```javascript
{
  label: 'Event Name (Color)',
  region: 'USA',          // or 'EU'
  bg: '#000000',          // hero/card background color
  c1: '#000000',          // --event-color-primary
  c2: '#000000',          // --event-color-secondary
  c3: '#000000',          // --event-color-tertiary
  logo: 'pcon-name-knockout.svg',   // filename under /UploadedFiles/EventPage/brands/procurecon/reff/logos/knockout/
  logoPath: '/UploadedFiles/...',   // use logoPath instead of logo for custom paths
  url: 'https://eventsite.wbresearch.com/',
  date: 'Month DD — DD, YYYY',
  venue: 'Venue Name, City, State'
}
```

Use `logo` for events that share the standard knockout logo folder. Use `logoPath` for events with logos in their own event folder (e.g. West, Travel, Contingent Staffing).

---

## Active presets

### EU / UK
- Indirect (Teal)
- Direct (Navy/Teal)
- Indirect UK (Navy/Yellow)

### USA
- CS Connect (Rust)
- Connect USA (Indigo)
- Marketing USA (Purple)
- IT Sourcing (Green)
- Contingent Staffing (Red)
- East (Navy/Blue)
- West (Navy/Coral)
- MRO (Teal)
- Travel (Red)
