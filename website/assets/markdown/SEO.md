# SEO & Metadata Strategy for PHPDocSpark

PHPDocSpark by Mark Hazleton implements a lightweight but comprehensive technical SEO baseline suitable for an open-source documentation & data exploration platform.

## Objectives

- Clear brand identity tied to the WebSpark suite
- Full indexability of documentation (parameterized markdown file views)
- Rich search appearance (structured data, Open Graph, Twitter Cards)
- Performance-conscious metadata without heavy frameworks

## Implemented Elements

| Category | Implementation |
|----------|----------------|
| Canonical URLs | Computed from configured canonical base + current request path/query |
| Meta Description | Page-specific or fallback to global description constant |
| Open Graph & Twitter | Dynamic tags for title, description, image |
| Structured Data | Person (author), Website, BreadcrumbList (context aware) |
| Sitemap | Dynamic `sitemap.php` enumerates static pages + each markdown document (?file=) |
| Robots | `robots.txt` allows indexing, references sitemap |
| Accessibility | Skip link, semantic landmarks, alt attributes maintained |
| Performance | Minimal inline logic; no blocking external SEO scripts |

## Extending

1. Add per-page `$metaDescription` before including `layout.php`.
2. Provide a social image at `website/assets/images/fallback-social.png` for richer sharing.
3. Update `includes/config.php` if canonical domain changes.
4. Consider adding Article schema for long-form markdown docs (map first heading → headline).

## Not Included (Yet)

- Analytics/Tracking (deferred per requirements)
- Localization metadata (`hreflang`)
- Automatic image dimension injection (could be added via a build step)

## Future Ideas

- Generate static HTML snapshots for key docs for faster first paint
- Add search indexing JSON export to feed external search services
- Structured data for datasets (CSV pages) using `Dataset` schema

---
Maintained by Mark Hazleton. Part of the WebSpark suite.
