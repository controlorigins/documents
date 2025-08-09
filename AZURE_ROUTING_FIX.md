# Azure App Service Configuration for PHP Custom Routing

## The Problem

Your PHP application works locally because PHP's built-in development server automatically handles routing, but Azure App Service requires explicit URL rewriting configuration.

## Files Created

1. `.htaccess` - Apache URL rewriting rules
2. `web.config` - IIS URL rewriting rules (backup)
3. `startup.sh` - Linux startup script
4. `diagnostics.php` - Diagnostic tool
5. `appsettings.json` - App Service configuration

## Manual Azure App Service Configuration

### Step 1: Configure App Service Settings

In the Azure Portal, go to your App Service → Configuration → Application Settings and add:

```
WEBSITE_DYNAMIC_CACHE = 0
WEBSITE_LOCAL_CACHE_OPTION = Never
PHP_INI_SCAN_DIR = /usr/local/etc/php/conf.d:/home/site
```

### Step 2: Enable URL Rewriting (if using Apache)

In Configuration → General Settings:

- Ensure "Always On" is enabled
- Set PHP version to 8.1 or higher

### Step 3: Test Deployment

1. Deploy your updated code with the new configuration files
2. Visit: `https://controlorigins-docs.azurewebsites.net/diagnostics.php`
3. Test the URL: `https://controlorigins-docs.azurewebsites.net/doc/chatgpt/sessions/create-php-joke-page`

## Alternative Solutions

### Option 1: Use Azure CLI to configure

```bash
az webapp config appsettings set \
  --name controlorigins-docs \
  --resource-group <your-resource-group> \
  --settings \
    WEBSITE_DYNAMIC_CACHE=0 \
    WEBSITE_LOCAL_CACHE_OPTION=Never

az webapp restart --name controlorigins-docs --resource-group <your-resource-group>
```

### Option 2: Check if using Nginx

If Azure App Service is using Nginx instead of Apache, you may need to configure it differently. Check the server software in diagnostics.php.

## Troubleshooting

1. **404 errors persist**: Check diagnostics.php to see if .htaccess is loaded
2. **Server errors**: Check Application Logs in Azure Portal
3. **Still not working**: Try adding this to your index.php (temporary debug):

```php
// Add at the top of index.php for debugging
error_log("REQUEST_URI: " . $_SERVER['REQUEST_URI']);
error_log("SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME']);
```

## Next Steps

1. Commit and push these changes
2. Let the Azure Pipeline deploy
3. Test the URL: <https://controlorigins-docs.azurewebsites.net/doc/chatgpt/sessions/create-php-joke-page>
4. If still not working, run diagnostics and check the logs

The issue should be resolved after deployment with these configuration files.
