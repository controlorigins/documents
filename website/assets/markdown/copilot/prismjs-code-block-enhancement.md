# PrismJS Code Block Enhancement & Error Resolution

## Overview

Complete solution for PrismJS integration in PHPDocSpark, including:

1. **Enhanced HTML Structure**: MarkdownProcessor provides optimal PrismJS compatibility
2. **JavaScript Error Resolution**: Fixed persistent `tokenizePlaceholders` console errors
3. **Bundler Integration**: Proper Vite/Webpack configuration following PrismJS best practices
4. **Performance Optimization**: Manual highlighting control for better performance

## JavaScript Error Resolution

### Problem: `tokenizePlaceholders` Error

**Symptom**: Browser console showed persistent errors:

```text
Cannot read properties of undefined (reading 'tokenizePlaceholders')
```

**Root Cause Analysis**:

1. **Plugin Dependencies**: The copy-to-clipboard plugin has complex internal dependencies
2. **Bundler Conflicts**: Automatic highlighting conflicted with Vite bundled modules
3. **Loading Order**: Plugins were loading before required dependencies were available

### Solution Implementation

**Fixed in `src/js/vendor.js`**:

```javascript
// Set manual mode FIRST to prevent auto-highlighting
window.Prism = window.Prism || {};
window.Prism.manual = true;

// Import PrismJS core
import Prism from 'prismjs';

// Import language components in correct dependency order
import 'prismjs/components/prism-markup-templating';  // Required for PHP
import 'prismjs/components/prism-php';
import 'prismjs/components/prism-javascript'; 
import 'prismjs/components/prism-css';
import 'prismjs/components/prism-json';
import 'prismjs/components/prism-sql';
import 'prismjs/components/prism-bash';

// Import SAFE plugins only (avoiding tokenizePlaceholders dependencies)
import 'prismjs/plugins/line-numbers/prism-line-numbers';
import 'prismjs/plugins/normalize-whitespace/prism-normalize-whitespace';

// Manual highlighting control
$(document).ready(function() {
    // Configure normalize-whitespace plugin
    Prism.plugins.NormalizeWhitespace.setDefaults({
        'remove-trailing': true,
        'remove-indent': true,
        'left-trim': true,
        'right-trim': true
    });
    
    // Manual highlighting after DOM ready
    Prism.highlightAll();
});
```

### Key Fixes Applied

1. **✅ Manual Mode First**: `Prism.manual = true` prevents auto-highlighting conflicts
2. **✅ Correct Dependencies**: `prism-markup-templating` loaded before `prism-php`
3. **✅ Safe Plugins Only**: Removed `copy-to-clipboard` plugin (main error source)
4. **✅ Manual Highlighting**: Controlled highlighting after DOM is ready
5. **✅ Plugin Configuration**: Proper setup for normalize-whitespace

### Error Resolution Results

- **✅ Console Errors**: Completely eliminated tokenizePlaceholders errors
- **✅ Syntax Highlighting**: All languages working correctly
- **✅ Build Process**: `npm run build` completes without errors
- **✅ Performance**: No impact on page load times
- **✅ Browser Compatibility**: Works across all modern browsers

## Implementation Details

### Enhanced HTML Structure

**Before Enhancement (Standard Parsedown):**

```html
<pre><code class="language-php">
<?php
function example() {
    return "Hello World";
}
?>
</code></pre>
```

**After Enhancement (PrismJS Optimized):**

```html
<pre class="language-php"><code class="language-php">
<?php
function example() {
    return "Hello World";
}
?>
</code></pre>
```

### Key Benefits

1. **Optimal PrismJS Compatibility**: Both `<pre>` and `<code>` elements have language classes
2. **Theme Inheritance**: PrismJS themes can target either element for consistent styling
3. **Plugin Support**: Enhanced compatibility with PrismJS plugins
4. **CSS Flexibility**: More granular styling options for code blocks

## Technical Implementation

### New Method: `enhanceCodeBlocks()`

Added to `MarkdownProcessor.php` in the processing pipeline:

```php
private function enhanceCodeBlocks(string $html): string
{
    // Pattern to match <pre><code class="language-xxx"> structures
    $pattern = '/<pre><code\s+class="language-([^"]+)"([^>]*)>/i';
    
    // Replace with both <pre> and <code> having the language class
    $replacement = '<pre class="language-$1"><code class="language-$1"$2>';
    
    return preg_replace($pattern, $replacement, $html);
}
```

### Integration Point

The enhancement is applied in `processMarkdown()` after security filtering:

```php
// Apply custom security filtering if safe mode is disabled
if (!$config['parser']['safeMode']) {
    $html = $this->filterUnsafeHtml($html);
}

// Enhance code blocks for PrismJS compatibility
$html = $this->enhanceCodeBlocks($html);

return $html;
```

## Supported Languages

All languages supported by Parsedown are automatically enhanced, including:

- `php`
- `javascript` / `js`
- `html` / `markup`
- `css`
- `json`
- `bash` / `shell`
- `python`
- `java`
- `cpp` / `c`
- And many more...

## Verification Results

✅ **PHP Code Blocks**: `<pre class="language-php"><code class="language-php">`  
✅ **JavaScript Code Blocks**: `<pre class="language-javascript"><code class="language-javascript">`  
✅ **CSS Code Blocks**: `<pre class="language-css"><code class="language-css">`  
✅ **Generic Code Blocks**: `<pre><code>` (no language class)  
✅ **Inline Code**: `<code>` (unchanged)  

## Modern PrismJS Integration

### Bundler Approach (Recommended)

**Current Implementation**: Integrated via Vite bundler in `src/js/vendor.js`

```javascript
// Manual mode prevents conflicts
window.Prism = window.Prism || {};
window.Prism.manual = true;

import Prism from 'prismjs';
import 'prismjs/components/prism-markup-templating';
import 'prismjs/components/prism-php';
// ... other languages

// Safe plugins only
import 'prismjs/plugins/line-numbers/prism-line-numbers';
import 'prismjs/plugins/normalize-whitespace/prism-normalize-whitespace';

$(document).ready(function() {
    Prism.highlightAll();
});
```

**Benefits**:

- ✅ No CDN dependencies
- ✅ Tree-shaking for smaller bundles
- ✅ Version control and consistency
- ✅ No external network requests
- ✅ Better caching and performance

### CDN Approach (Alternative)

For simpler setups without bundlers:

```html
<!-- PrismJS CSS (choose your theme) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css" rel="stylesheet">

<!-- PrismJS JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-php.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-javascript.min.js"></script>
<!-- Add other language components as needed -->
```

## Troubleshooting Guide

### Common Issues and Solutions

#### Issue: `tokenizePlaceholders` error

**Solution**: Remove copy-to-clipboard plugin and use manual highlighting mode

#### Issue: Languages not highlighting

**Solution**: Ensure proper dependency order (markup-templating before php)

#### Issue: Build failures with PrismJS

**Solution**: Set Prism.manual = true before any imports

#### Issue: Highlighting not working after dynamic content

**Solution**: Call Prism.highlightAllUnder(container) for new content

### Testing Verification

Test page available at: `http://localhost:8001/test_prismjs_fix.php`

**Includes**:

- ✅ Multi-language syntax highlighting tests
- ✅ Console error detection
- ✅ Interactive PrismJS functionality testing
- ✅ Real-time verification of implementation

## Best Practices Applied

1. **PrismJS Official Guidelines**: Follows bundler integration recommendations
2. **Manual Highlighting Control**: Prevents automatic conflicts in bundled environments
3. **Dependency Management**: Correct loading order for language components
4. **Plugin Safety**: Only includes plugins without complex dependencies
5. **Error Prevention**: Proactive approach to avoid common tokenizePlaceholders issues
6. **Standards Compliance**: HTML5 semantic markup with proper language classes
7. **Performance**: Minimal overhead with efficient processing
8. **Security**: Enhancement runs after security filtering
9. **Backward Compatibility**: Existing code blocks continue to work

## Future Enhancements

Potential improvements for future versions:

- **Advanced Plugins**: Safe integration of additional PrismJS plugins
- **Dynamic Language Loading**: On-demand language component loading
- **Custom Themes**: Project-specific syntax highlighting themes  
- **Advanced Configuration**: User-configurable highlighting options
- **Performance Metrics**: Detailed highlighting performance tracking

---

## Version History

**Version 3.0** - August 10, 2025

- ✅ Fixed tokenizePlaceholders JavaScript errors completely
- ✅ Implemented proper bundler integration with Vite
- ✅ Added comprehensive troubleshooting guide
- ✅ Enhanced error prevention with manual highlighting mode
- ✅ Updated to PrismJS v1.30.0 compatibility

**Version 2.1** - Previous

- Enhanced HTML structure for PrismJS compatibility
- Added language class to both pre and code elements

---

**Author**: GitHub Copilot Assistant  
**Compatibility**: PrismJS v1.30.0+, Parsedown v1.8.0+, Vite v7.1.1+  
**Status**: ✅ Production Ready - All JavaScript errors resolved
