# PrismJS Code Block Enhancement

## Overview

Updated the MarkdownProcessor to provide optimal compatibility with PrismJS syntax highlighting by ensuring both `<pre>` and `<code>` elements receive the appropriate `language-xxx` classes.

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

## PrismJS Integration

To use PrismJS with the enhanced code blocks, simply include PrismJS CSS and JavaScript:

```html
<!-- PrismJS CSS (choose your theme) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css" rel="stylesheet">

<!-- PrismJS JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-php.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-javascript.min.js"></script>
<!-- Add other language components as needed -->
```

## Best Practices Applied

1. **Standards Compliance**: Follows HTML5 semantic markup recommendations
2. **PrismJS Documentation**: Implements official PrismJS best practices
3. **Performance**: Minimal overhead with efficient regex patterns
4. **Backward Compatibility**: Existing code blocks continue to work
5. **Security**: Enhancement runs after security filtering

## Future Enhancements

Potential future improvements:

- Automatic language detection
- Custom CSS class injection
- Line numbering support
- Highlight line ranges
- Copy-to-clipboard functionality

---

**Version**: 2.1  
**Date**: August 10, 2025  
**Author**: GitHub Copilot Assistant  
**Compatibility**: PrismJS v1.29.0+, Parsedown v1.8.0+
