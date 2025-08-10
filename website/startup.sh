#!/bin/bash

# Azure App Service startup script for PHP applications with custom routing
#!/bin/bash

# Copy custom nginx configuration
if [ -f /home/site/wwwroot/nginx.conf ]; then
    cp /home/site/wwwroot/nginx.conf /etc/nginx/sites-available/default
    echo "Custom nginx configuration applied"
else
    echo "No custom nginx.conf found, using default"
fi

# Restart nginx to apply changes
service nginx restart

# Start PHP-FPM (if not already started)
service php8.4-fpm start

echo "Startup script completed"

# Set working directory
cd /home/site/wwwroot

# Create Nginx configuration for URL rewriting if we can access it
if [ -w /etc/nginx/sites-available/default ]; then
    echo "Configuring Nginx for URL rewriting..."
    cat > /etc/nginx/sites-available/default << 'EOF'
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    
    root /home/site/wwwroot;
    index index.php index.html index.htm;
    server_name _;
    
    # Handle static files
    location ~* \.(css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        try_files $uri =404;
    }
    
    # Handle assets directory
    location ^~ /assets/ {
        try_files $uri =404;
    }
    
    # Handle data directory (block direct access for security)
    location ^~ /data/ {
        deny all;
    }
    
    # Handle PHP files directly
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    # Handle pretty URLs - send everything else to index.php
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
}
EOF
    
    # Test nginx configuration
    nginx -t && nginx -s reload
    echo "Nginx configuration updated and reloaded"
else
    echo "Cannot write to Nginx config, using alternative approach..."
fi

# Ensure .htaccess exists for Apache (if used)
if [ ! -f /home/site/wwwroot/.htaccess ]; then
    echo "Creating .htaccess for Apache compatibility..."
    cat > /home/site/wwwroot/.htaccess << 'EOF'
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
EOF
fi

# Set proper permissions
chmod 644 /home/site/wwwroot/.htaccess 2>/dev/null || echo "Could not set .htaccess permissions"
chmod +x /home/site/wwwroot/*.php 2>/dev/null || echo "Could not set PHP permissions"

# Log some diagnostics
echo "=== Startup Diagnostics ==="
echo "Current directory: $(pwd)"
echo "PHP version: $(php --version | head -n1)"
echo "Web server: $(ps aux | grep -E 'nginx|apache' | grep -v grep || echo 'Not detected')"
echo "Files in root: $(ls -la /home/site/wwwroot/ | head -10)"
echo "=== End Diagnostics ==="

echo "Startup script completed successfully."
