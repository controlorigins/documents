# Cloudflare Redirect Troubleshooting Guide

## Problem Description

### Issue Overview

When setting up a Cloudflare redirect rule to forward traffic from a custom domain to an Azure Web App, the wildcard pattern was incorrectly capturing parts of the domain name instead of just the URL path.

### Specific Problem

- **Source Domain**: `https://phpdocspark.markhazleton.com/`
- **Target Domain**: `https://phpdocspark.azurewebsites.net/`
- **Expected Behavior**: Root domain should redirect to root domain
- **Actual Behavior**: `https://phpdocspark.markhazleton.com/` redirected to `https://phpdocspark.azurewebsites.net/markhazleton.com/`

### Original Configuration (Problematic)

- **Rule Type**: Single Redirect
- **Pattern**: `https://phpdocspark.markhazleton.com/*` (wildcard pattern)
- **Target URL**: `https://phpdocspark.azurewebsites.net/${1}`
- **Status Code**: 301
- **Preserve Query String**: Yes

### Root Cause Analysis

The wildcard `*` in the pattern was incorrectly capturing `markhazleton.com/` when visiting the root domain (`/`), instead of capturing just the empty path. This suggests that Cloudflare's wildcard matching was interpreting the pattern boundaries incorrectly for edge cases like the root domain.

## Solutions

### Solution 1: Two Separate Rules (Recommended)

This is the **officially recommended approach** from Cloudflare documentation for handling root domain and path redirects separately.

#### Rule 1: Root Domain Handler (Higher Priority)

```text
Type: Single Redirect
When incoming requests match: Wildcard pattern
Request URL: https://phpdocspark.markhazleton.com/
Target URL: https://phpdocspark.azurewebsites.net/
Status Code: 301
Preserve Query String: Yes
Priority: 1 (Higher)
```

#### Rule 2: Path Handler (Lower Priority)

```text
Type: Single Redirect
When incoming requests match: Wildcard pattern
Request URL: https://phpdocspark.markhazleton.com/*
Target URL: https://phpdocspark.azurewebsites.net/${1}
Status Code: 301
Preserve Query String: Yes
Priority: 2 (Lower)
```

#### Implementation Steps

1. Delete the existing problematic rule
2. Create Rule 1 for exact root domain matching
3. Create Rule 2 for wildcard path matching
4. Ensure Rule 1 has higher priority than Rule 2
5. Test both root domain and paths with subdirectories

### Solution 2: Custom Filter Expression (Advanced)

For users comfortable with Cloudflare's expression language, this provides more precise control.

#### Configuration

```text
Type: Single Redirect
When incoming requests match: Custom filter expression
Expression: (http.host eq "phpdocspark.markhazleton.com")
Target URL: concat("https://phpdocspark.azurewebsites.net", http.request.uri.path)
Status Code: 301
Preserve Query String: Yes
```

#### Advantages

- Single rule handles all cases
- More predictable behavior
- Uses Cloudflare's built-in functions
- Better control over URL construction

### Solution 3: Pattern Syntax Correction

If preferring to keep a single wildcard rule, ensure proper pattern syntax:

#### Corrected Configuration

```text
Type: Single Redirect
When incoming requests match: Wildcard pattern
Request URL: phpdocspark.markhazleton.com/*
Target URL: https://phpdocspark.azurewebsites.net/$1
Status Code: 301
Preserve Query String: Yes
```

#### Key Changes

- Remove `https://` from the pattern (domain matching only)
- Use `$1` instead of `${1}` (though both should work)

## Technical Documentation Reference

### Cloudflare Official Documentation

- **Main Guide**: [URL Forwarding - Single Redirects](https://developers.cloudflare.com/rules/url-forwarding/single-redirects/)
- **Wildcard Patterns**: [Wildcard Matching](https://developers.cloudflare.com/ruleset-engine/rules-language/operators/#wildcard-matching)
- **Functions Reference**: [Wildcard Replace Function](https://developers.cloudflare.com/ruleset-engine/rules-language/functions/#wildcard_replace)

### Key Technical Concepts

#### Wildcard Matching Behavior

- Cloudflare uses **lazy matching** - tries to match each `*` with the shortest possible string
- The entire source URL must match the wildcard pattern (not just part of it)
- Two consecutive `*` characters (`**`) are invalid and cannot be used

#### Execution Order

1. **Single Redirects** (higher priority)
2. URL Rewrite Rules
3. Configuration Rules
4. Origin Rules
5. **Bulk Redirects** (lower priority)

#### Rule Priority

- For terminating actions (like redirects), the **first matching rule wins**
- Rules are processed in the order they appear in the dashboard
- Higher priority rules should be listed first

## Testing and Validation

### Test Cases to Verify

1. **Root Domain**: `https://phpdocspark.markhazleton.com/` → `https://phpdocspark.azurewebsites.net/`
2. **Single Path**: `https://phpdocspark.markhazleton.com/about` → `https://phpdocspark.azurewebsites.net/about`
3. **Nested Path**: `https://phpdocspark.markhazleton.com/docs/guide` → `https://phpdocspark.azurewebsites.net/docs/guide`
4. **Query Strings**: `https://phpdocspark.markhazleton.com/search?q=test` → `https://phpdocspark.azurewebsites.net/search?q=test`
5. **Fragment URLs**: `https://phpdocspark.markhazleton.com/page#section` → `https://phpdocspark.azurewebsites.net/page#section`

### Testing Tools

- **Cloudflare Trace**: Use [Cloudflare Trace](https://developers.cloudflare.com/rules/trace-request/) to debug rule matching
- **Browser Testing**: Use incognito/private browsing to avoid cached redirects
- **cURL Testing**: Command-line testing for precise HTTP response analysis

```bash
# Test redirect without following
curl -I "https://phpdocspark.markhazleton.com/"

# Expected response
HTTP/2 301
location: https://phpdocspark.azurewebsites.net/
```

## Best Practices

### Configuration Management

1. **Document all rules** with clear descriptions
2. **Use descriptive rule names** that explain their purpose
3. **Test in staging** before applying to production domains
4. **Monitor redirect chains** to avoid loops or excessive redirects

### Performance Considerations

- **Minimize redirect chains** - direct redirects are faster
- **Use 301 (Permanent)** for SEO benefits on permanent moves
- **Use 302 (Temporary)** only for temporary redirections
- **Enable query string preservation** when original parameters matter

### Security Considerations

- **Validate target domains** to prevent open redirect vulnerabilities
- **Use HTTPS** for both source and target URLs
- **Consider redirect loops** - ensure target doesn't redirect back to source
- **Monitor for redirect abuse** in access logs

## Troubleshooting Checklist

### When Redirects Don't Work

- [ ] DNS records are proxied through Cloudflare (orange cloud)
- [ ] Rule syntax is correct and valid
- [ ] Rule priority/order is properly configured
- [ ] No conflicting rules exist
- [ ] Target domain is accessible and correct

### When Redirects Behave Unexpectedly

- [ ] Clear browser cache or test in incognito mode
- [ ] Check for cached DNS responses
- [ ] Verify wildcard pattern matching with Cloudflare Trace
- [ ] Review rule execution order and priorities
- [ ] Test with different URL patterns and edge cases

### Common Pitfalls

1. **Incorrect wildcard syntax** - ensure proper escaping and pattern structure
2. **Missing HTTPS in target URLs** - always specify the full target URL
3. **Wrong rule priority** - more specific rules should have higher priority
4. **Cached redirects** - browser and CDN caching can mask changes
5. **DNS propagation delays** - allow time for DNS changes to propagate

## Monitoring and Maintenance

### Ongoing Monitoring

- **Regular testing** of critical redirect paths
- **Analytics review** to ensure traffic is flowing correctly
- **Error rate monitoring** for 4xx/5xx responses
- **Performance impact assessment** of redirect rules

### Documentation Updates

- **Keep rule documentation current** when making changes
- **Document any exceptions** or special cases
- **Maintain test case coverage** for all redirect scenarios
- **Update stakeholders** when redirect behavior changes

---

## Conclusion

The Cloudflare redirect issue was resolved by implementing **two separate redirect rules** - one for the root domain exact match and another for wildcard path matching. This approach follows Cloudflare's official best practices and provides more reliable and predictable redirect behavior.

The key lesson learned is that wildcard patterns can behave unexpectedly at domain boundaries, especially for root domain redirects. Using explicit rules for different URL patterns provides better control and easier debugging.

**Date Created**: August 10, 2025  
**Last Updated**: August 10, 2025  
**Status**: Resolved  
**Solution Implemented**: Two Separate Rules (Solution 1)
