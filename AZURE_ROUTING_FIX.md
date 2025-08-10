# Azure App Service Configuration for PHP Custom Routing (Nginx)

## The Problem

Your PHP application works locally because PHP's built-in development server automatically handles routing, but Azure App Service on Linux uses Nginx which requires different configuration than Apache's `.htaccess`.

## Solution for Nginx (Linux App Service)

Based on diagnostics showing `nginx/1.28.0`, we need Nginx-specific configuration.

## Files Created/Updated

1. `nginx.conf` - Nginx server configuration with URL rewriting
2. `startup.sh` - Custom startup script to apply Nginx config
3. `web.config` - Legacy IIS config (kept for compatibility)
4. `diagnostics.php` - Diagnostic tool
5. Azure App Service settings configured via Azure CLI

## Azure Configuration Applied

The following settings have been configured via Azure CLI:

```bash
# Enable custom startup script
az webapp config set \
  --name PHPDocSpark \
  --resource-group rg-controlorigins-docs \
  --startup-file "startup.sh"

# Enable app service storage for custom nginx config
az webapp config appsettings set \
  --name PHPDocSpark \
  --resource-group rg-controlorigins-docs \
  --settings WEBSITES_ENABLE_APP_SERVICE_STORAGE=true
```

## How It Works

1. **nginx.conf** - Contains URL rewriting rules for pretty URLs
2. **startup.sh** - Copies nginx.conf to the correct location and restarts Nginx
3. **Azure Config** - Points to startup.sh to run during app startup

## Testing the Fix

After deployment, the routing should work for URLs like:
- `https://phpdocspark.azurewebsites.net/doc/seo`
- `https://phpdocspark.azurewebsites.net/doc/chatgpt/sessions/create-php-joke-page`

### Step 3: Verify Configuration

1. Deploy your updated code with the new configuration files
2. Visit: `https://phpdocspark.azurewebsites.net/diagnostics.php`
3. Test the URL: `https://phpdocspark.azurewebsites.net/doc/seo`

## Alternative Solutions

### Option 1: Manual Azure Portal Configuration

If you prefer using the Azure Portal:

1. Go to App Service → Configuration → General Settings
2. Set Startup Command to: `startup.sh`
3. Save and restart the app

### Option 2: Check Deployment Logs

If the fix doesn't work immediately:

```bash
az webapp log tail --name PHPDocSpark --resource-group rg-controlorigins-docs
```

## Troubleshooting

1. **404 errors persist**: Check if startup.sh is executing by viewing logs
2. **Server errors**: Check Application Logs in Azure Portal
3. **Nginx config not applied**: Verify startup command is set to "startup.sh"
4. **Still not working**: Add debug logging to startup.sh:

```bash
#!/bin/bash
echo "Starting custom startup script..." >> /home/site/startup.log
if [ -f /home/site/wwwroot/nginx.conf ]; then
    cp /home/site/wwwroot/nginx.conf /etc/nginx/sites-available/default
    echo "Custom nginx configuration applied" >> /home/site/startup.log
else
    echo "ERROR: nginx.conf not found" >> /home/site/startup.log
fi
service nginx restart >> /home/site/startup.log 2>&1
```

## Next Steps

1. **Commit and push** the new `nginx.conf` and updated `startup.sh`
2. **Azure will automatically deploy** via GitHub Actions
3. **Test the URL**: <https://phpdocspark.azurewebsites.net/doc/seo>
4. **Check logs if needed**: `az webapp log tail --name PHPDocSpark --resource-group rg-controlorigins-docs`

The routing should now work correctly with the Nginx configuration!
