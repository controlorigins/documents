#!/bin/bash

# Azure App Service startup script for PHP applications
# This ensures the web server is configured properly for URL rewriting

echo "Starting PHP application with custom routing..."

# Copy .htaccess if it doesn't exist in the web root
if [ ! -f /home/site/wwwroot/.htaccess ]; then
    echo "No .htaccess found, creating one for URL rewriting..."
    cat > /home/site/wwwroot/.htaccess << 'EOF'
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
EOF
fi

# Set proper permissions
chmod 644 /home/site/wwwroot/.htaccess

echo "Startup script completed successfully."
