# Control Origins Documentation & Demo Application

An educational, full-featured PHP 8 demo website showcasing: markdown documentation management, data exploration & visualization, CRUD with SQLite, API integrations, project portfolio rendering, full‑text markdown search, responsive UI (Bootstrap 5 + DataTables), and CI/CD deployment to Azure Web Apps.

**Quick Links:**

- [Published Site](https://controlorigins-docs.azurewebsites.net/)
- [Build Article](https://markhazleton.com/creating-a-php-website-with-chat-gpt.html)
- [Story Origin](https://storybird.ai/library/the-code-conjurer)

## Table of Contents

- [Control Origins Documentation \& Demo Application](#control-origins-documentation--demo-application)
  - [Table of Contents](#table-of-contents)
  - [Purpose \& Audience](#purpose--audience)
  - [Key Features](#key-features)
  - [Architecture Overview](#architecture-overview)
    - [Sequence (Example: Viewing Markdown)](#sequence-example-viewing-markdown)
    - [Sequence (Example: GitHub Commits)](#sequence-example-github-commits)
  - [Technology Stack](#technology-stack)
  - [Directory Structure](#directory-structure)
  - [Data \& Content Model](#data--content-model)
  - [Local Development](#local-development)
    - [Composer (Optional)](#composer-optional)
    - [File Permissions (Linux/macOS)](#file-permissions-linuxmacos)
  - [Running Without a Web Server](#running-without-a-web-server)
  - [Deployment (Azure Pipeline)](#deployment-azure-pipeline)
  - [Feature Walkthrough](#feature-walkthrough)
    - [Documentation Viewer (`document_view.php`)](#documentation-viewer-document_viewphp)
    - [Search (`search.php`)](#search-searchphp)
    - [CSV Analysis (`data-analysis.php`)](#csv-analysis-data-analysisphp)
    - [CRUD / Contacts (`crud.php` + `chart.php` advanced)](#crud--contacts-crudphp--chartphp-advanced)
    - [Project Portfolio (`project_list.php`)](#project-portfolio-project_listphp)
    - [GitHub Integration (`github.php`)](#github-integration-githubphp)
    - [Jokes API Demo (`joke.php` / `fetch_joke.php`)](#jokes-api-demo-jokephp--fetch_jokephp)
  - [Extensibility \& Customization](#extensibility--customization)
  - [Quality \& Security Notes](#quality--security-notes)
  - [Troubleshooting](#troubleshooting)
  - [Contributing Workflow](#contributing-workflow)
  - [Roadmap Ideas](#roadmap-ideas)
  - [License \& Ownership](#license--ownership)

---

## Purpose & Audience

This repository serves dual roles:

1. Internal / organizational documentation hub (Markdown-first).
2. A didactic PHP 8 reference implementation illustrating modern, minimal, framework‑light patterns for: routing, content parsing, UI composition, CSV analytics, SQLite CRUD, third‑party API access, caching, and structured data visualization.

It targets developers, technical writers, and stakeholders seeking a compact, readable codebase for experimentation and incremental enhancement.

## Key Features

| Category | Capability | File(s) / Notes |
| -------- | ---------- | --------------- |
| Routing/Layout | Simple front controller & layout composition | `website/index.php`, `website/layout.php` |
| Documentation Viewer | Recursive markdown discovery + Parsedown rendering | `pages/document_view.php`, `assets/markdown/**` |
| Markdown Search | Full tree traversal, excerpt & highlight, relevance sorting | `pages/search.php` |
| CSV Data Analysis | Automatic field stats (min/max/avg, frequency) + tabular preview & export hooks | `pages/data-analysis.php`, `data/*.csv` |
| Charts / CRUD | SQLite-backed contacts (Star Trek seed) CRUD with DataTables UI | `pages/crud.php`, `pages/chart.php`, `data/database.db` |
| Project Portfolio | JSON-driven card grid with filters + pagination | `pages/project_list.php`, `data/projects.json` |
| GitHub Integration | Live repo info, commit details (with file deltas), contributors + file count caching | `pages/github.php`, `data/commits_cache.json` |
| External API Demo | Random Joke (JokeAPI) via AJAX + PHP cURL mediator | `pages/joke.php`, `pages/fetch_joke.php` |
| Caching | JSON commit cache w/ TTL to reduce API calls | `pages/github.php` |
| Styling | Responsive UI: Bootstrap 5, Icons, DataTables integration | `website/layout.php` |
| CI/CD | Azure Pipelines build → artifact zip → Web App deploy | `azure-pipelines.yml` |

## Architecture Overview

Lightweight, intentionally minimal:

1. Input Router: `index.php` inspects `?page=` (default `document_view`) → validates against allow‑list → includes page script.
2. Page Scripts: Each `pages/*.php` file is self-contained (view + logic) for clarity.
3. Layout Composition: Page output captured via output buffering and injected into shared layout (`layout.php`).
4. Content Sources:
   - Markdown tree: `assets/markdown` (recursive scan + optgroup grouping).
   - CSV datasets: `data/*.csv` for analytical summarization.
   - SQLite store: `data/database.db` created on demand (contacts table).
   - JSON: `data/projects.json` drives portfolio cards.
5. External Integrations: GitHub REST API (unauth or token) + JokeAPI.
6. Caching Layer: Simple JSON file w/ timestamp for commit responses (reducing rate limit pressure).
7. Presentation Enhancements: DataTables for interactive tables; modals, forms, and cards for UX clarity.

### Sequence (Example: Viewing Markdown)

User selects document → GET `/?file=path.md` → `document_view.php` loads + parses via Parsedown → rendered HTML embedded in layout.

### Sequence (Example: GitHub Commits)

Request hits `github.php` → cache validated → (fetch if stale) → commit details enriched (individual commit endpoints) → structured UI (repo stats + recent commits + contributor cards).

## Technology Stack

| Layer | Technology | Notes |
| ----- | ---------- | ----- |
| Language | PHP 8.x | Strict error reporting enabled in `index.php` (development ready). |
| Web UI | Bootstrap 5, Bootstrap Icons, jQuery, DataTables | CDN delivered. |
| Data | SQLite (CRUD), CSV files, JSON metadata, Markdown content | No external DB dependency. |
| Parsing | Parsedown (bundled single file) | Converts Markdown → HTML. |
| DevOps | Azure Pipelines (YAML) | Automated build & deploy to Azure Web App (Linux). |
| Caching | File-based JSON | TTL = 1 hour for GitHub API responses. |

## Directory Structure

```text
website/
  index.php          # Router / front controller
  layout.php         # Common HTML layout & nav + assets
  pages/             # Feature-specific scripts
    document_view.php
    search.php
    data-analysis.php
    crud.php
    chart.php        # Enhanced CRUD + modal editing
    project_list.php
    github.php
    joke.php / fetch_joke.php
    Parsedown.php    # Markdown parser library
  assets/markdown/   # Documentation content (nested)
data/
  database.db        # SQLite (auto-created)
  projects.json      # Portfolio data
  commits_cache.json # GitHub API cache
  *.csv              # Sample analytical datasets
azure-pipelines.yml  # CI/CD pipeline definition
composer.json        # Minimal composer metadata (autoload reserved)
```

## Data & Content Model

| File | Purpose | Shape |
| ---- | ------- | ----- |
| `projects.json` | Project cards | Array of objs: `{ p: name, d: description, h: href, image: path }` |
| `commits_cache.json` | Cached GitHub API payload | `{ repoInfo, commits[], contributors[], timestamp }` |
| `*.csv` | Tabular dataset | Header row + homogeneous columns. |
| SQLite `contacts` | CRUD example table | `id INTEGER PK`, `name TEXT`, `email TEXT` (duplicate prevention logic). |

## Local Development

Prereqs: PHP 8+, (optional) Composer, Git.

Clone and serve:

```bash
git clone https://github.com/controlorigins/documents.git
cd documents/website
php -S localhost:8080
```

Visit <http://localhost:8080/> in your browser.

### Composer (Optional)

Currently only declares PHP constraint + PSR-4 namespace; no vendor deps required for runtime pages. Run `composer install` to satisfy pipeline expectations (creates vendor autoload scaffolding).

### File Permissions (Linux/macOS)

Ensure `data/` is writable if seeding or modifying SQLite / cache:

```bash
chmod 775 data
```

## Running Without a Web Server

For quick parsing tests of Markdown you can run Parsedown directly:

```bash
php -r "require 'website/pages/Parsedown.php'; echo (new Parsedown())->text('# Test');"
```

## Deployment (Azure Pipeline)

Pipeline: `azure-pipelines.yml`

1. Trigger: `main` branch commits.
2. Agent: Ubuntu latest.
3. Steps:
   - Switch PHP version (8.1.x).
   - `composer install` (even if minimal) to satisfy build integrity.
   - Zip `website/` folder only.
   - Publish artifact.
4. Deploy Stage:
   - Azure Web App deploy (`AzureWebApp@1`).
   - App Name: `controlorigins-docs` (Linux).

Environment Config Tips:

| Aspect | Recommendation |
| ------ | -------------- |
| PHP Errors | Disable `display_errors` in production (edit `index.php`). |
| Caching | Adjust TTL in `github.php` for commit cache if higher freshness needed. |
| GitHub Rate Limit | Add a token in `github.php` `$token` variable for higher API quota. |

## Feature Walkthrough

### Documentation Viewer (`document_view.php`)

Recursive scan builds `select` element with nested optgroups. Default selection = first discovered file. Uses Parsedown for safe transformation (HTML injection risk minimal—still sanitize if user-supplied content is later added).

### Search (`search.php`)

Case-insensitive substring search across all markdown. Returns excerpt with context (±50 chars) + highlighted matches, frequency counts, size & modified timestamp; sorted by occurrence frequency.

### CSV Analysis (`data-analysis.php`)

Computes per-field min / max / average (when numeric), distinct counts, most & least common values. Presents summary + interactive DataTable and export collection buttons (CSV, Excel, PDF) if buttons extension scripts added (structure in place).

### CRUD / Contacts (`crud.php` + `chart.php` advanced)

SQLite table auto-created. Prevents duplicate name/email pairs. Supports seed population, inline edit forms (basic CRUD) and enhanced modal editing in `chart.php` version with DataTables paging.

### Project Portfolio (`project_list.php`)

Filter by initial letter, live search overlay, pagination (client-side) and card-based responsive layout.

### GitHub Integration (`github.php`)

Aggregates: repo stats, recent commits (with file change diff counts), contributor leaderboard. Employs 1h JSON cache to limit repeated API calls; displays cache age & next refresh countdown.

### Jokes API Demo (`joke.php` / `fetch_joke.php`)

AJAX -> PHP cURL -> JokeAPI -> dynamic replacement with fade transitions; tracks count of jokes fetched in session.

## Extensibility & Customization

| Area | Extension Idea |
| ---- | -------------- |
| Routing | Introduce simple controller class + autoload for cleaner separation. |
| Security | Add CSRF tokens to POST forms (CRUD, seed, search). |
| Markdown | Add front-matter parsing for metadata (title, tags, updated). |
| Search | Build an inverted index for faster scaling, add fuzzy matching (Levenshtein). |
| Auth | Introduce login for protected docs; role-based visibility flags. |
| API | JSON endpoints for documents, projects, contacts. |
| Testing | PHPUnit tests for utility functions (duplicate detection, CSV parsing). |
| Observability | Add basic request logging + performance timings. |
| Accessibility | Audit ARIA labels; improve contrast and keyboard focus states. |

## Quality & Security Notes

Current Safeguards:
Current Safeguards:

- Allow-list for `?page=` prevents arbitrary file inclusion.
- Duplicate contact prevention reduces accidental duplication.
- Minimal external dependencies lowers supply-chain surface.

Recommended Hardening:

- Sanitize rendered Markdown (if user-editable in future) via HTML purifier.
- Replace inline SQL fragments with prepared statements (already used for CRUD) & add constraints.
- Add rate limiting to API endpoints (fetch_joke, GitHub) if exposed publicly.
- Store secrets (GitHub token) via environment variables instead of inline variable.
- Disable PHP error display in production; log to file/telemetry instead.

Performance Considerations:

- For large markdown sets, introduce cached directory index.
- Paginate large CSV tables server-side if they grow beyond memory comfort.

## Troubleshooting

| Symptom | Possible Cause | Resolution |
| ------- | -------------- | ---------- |
| Blank page | PHP fatal error hidden | Enable `display_errors` (dev) or inspect server logs. |
| GitHub data stale | Cache TTL active | Delete `data/commits_cache.json` or lower TTL. |
| SQLite permission error | Non-writable `data/` | Adjust directory permissions. |
| Joke fetch fails | Network / API outage | Retry; inspect browser console & server cURL error. |

## Contributing Workflow

```mermaid
graph TD;
  A[Fork / Branch] --> B[Implement / Update Docs];
  B --> C[Commit (conventional message)];
  C --> D[Push Branch];
  D --> E[Open PR];
  E --> F{Review?};
  F -- Revisions Needed --> G[Update Branch];
  G --> C;
  F -- Approved --> H[Merge to Main];
  H --> I[Pipeline Deploy];
  I --> J[Verification];
```

Guidelines:

- Prefer small, focused PRs (one feature or doc set).
- Use descriptive commit messages.
- Include before/after screenshots for UI changes when practical.
- Add sample data (sanitized) if introducing new data-driven features.

## Roadmap Ideas

- Tagging & categorization for documents.
- Dark mode toggle & persistent theme.
- Real-time search (client-side index / web worker).
- Markdown editing in-browser with live preview.
- REST/GraphQL layer for headless consumption.
- User authentication + role-based access.
- Unit & integration test suite (CI quality gates).
- Containerization (Dockerfile + GitHub Actions alternative pipeline).

## License & Ownership

All source code, documentation, and content are proprietary and confidential to Control Origins. Redistribution without explicit written authorization is prohibited. By contributing, you agree contributions are assigned to Control Origins.

---

For questions or improvement proposals, open an issue or submit a pull request. Enjoy exploring and extending the platform.
