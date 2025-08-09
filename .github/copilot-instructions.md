# GitHub Copilot Instructions

## Project Overview

This is a PHP-based documentation and portfolio website that showcases various programming concepts, data analysis tools, and interactive features.

## Project Structure

- **Root Directory**: `c:\GitHub\ControlOrigins\documents`
- **Website Directory**: `c:\GitHub\ControlOrigins\documents\website` (All PHP files and web assets)
- **Entry Point**: `website/index.php`
- **Pages**: Located in `website/pages/` directory
- **Assets**: Located in `website/assets/` directory
- **Data**: Located in `website/data/` directory (SQLite database, JSON files, CSV files)

## Development Environment

- **PHP Version**: 8.2+ (configured at `C:\tools\php82\php.ini`)
- **Web Server**: PHP built-in development server
- **Database**: SQLite (PDO SQLite driver enabled)
- **Dependencies**: Managed via Composer
- **Frontend**: Bootstrap 5, jQuery, Chart.js

## Terminal Usage Guidelines

### **CRITICAL RULE: Minimize Terminal Creation**

- **Always use existing terminals** when possible
- **Only create new terminals** when explicitly requested by the user
- Check for running processes before starting new ones
- Use `get_terminal_output` to check existing terminal status

### PHP Development Server

- **Server must run from**: `c:\GitHub\ControlOrigins\documents\website` directory
- **Standard command**: `php -S localhost:8001`
- **Before starting server**: Check if already running with `Get-Process php`
- **Stop server**: Use `Stop-Process -Name php -Force` if needed

### Correct Server Startup Sequence

```powershell
# 1. Check if server is already running
Get-Process php -ErrorAction SilentlyContinue

# 2. Navigate to correct directory
cd C:\GitHub\ControlOrigins\documents\website

# 3. Start server (only if not running)
php -S localhost:8001
```

## File Structure Rules

- **Website root**: All web-accessible files must be in `/website/` directory
- **Markdown files**: ALL `.md` files (except root `README.md` which stays as GitHub repository standard) MUST be placed under `/website/assets/markdown/` folder tree
- **PHP includes**: Use relative paths from website directory
- **Database location**: `website/data/database.db`
- **Page routing**: Handled by `index.php` with `?page=` parameter

## Coding Standards

### PHP Best Practices

- Use error reporting for development: `error_reporting(E_ALL)`
- Always sanitize output with `htmlspecialchars()`
- Use PDO with prepared statements for database queries
- Include proper error handling for all external API calls
- Use absolute paths when referencing files outside website directory

### Database Operations

- **Database file**: `website/data/database.db` (SQLite)
- **Connection**: Use PDO with sqlite driver
- **Error handling**: Always check for PDO exceptions
- **Prepared statements**: Required for all user input

### External API Integration

- **cURL settings**: Include SSL verification settings for HTTPS
- **Error handling**: Check for cURL errors and null responses
- **User agent**: Set appropriate user agent for API requests
- **Timeouts**: Set reasonable timeout values (10 seconds)

### Frontend Integration

- **Bootstrap**: Version 5.x
- **jQuery**: For AJAX requests and DOM manipulation
- **Chart.js**: For data visualization
- **Icons**: Bootstrap Icons
- **Responsive**: Mobile-first design

## Common Commands

### Composer Management

```bash
# Install dependencies
composer install

# Update dependencies
composer update
```

### PHP Server Management

```powershell
# Check if server is running
Get-Process php -ErrorAction SilentlyContinue

# Start server (from website directory)
cd C:\GitHub\ControlOrigins\documents\website
php -S localhost:8001

# Stop server
Stop-Process -Name php -Force
```

### Testing Individual Scripts

```powershell
# Test PHP scripts directly
php -f "pages/fetch_joke.php"

# Check PHP modules
php -m

# Test database connection
php -r "try { new PDO('sqlite:data/database.db'); echo 'SQLite OK'; } catch(Exception $e) { echo $e->getMessage(); }"
```

## Pages and Features

### Available Pages

- `document_view` (default) - Markdown document viewer
- `data-analysis` - CSV file analysis tools
- `chart` - Interactive charting with Chart.js
- `project_list` - Portfolio projects display
- `github` - GitHub API integration
- `crud` - Database CRUD operations
- `joke` - External API integration (JokeAPI)
- `search` - Document search functionality

### Database Features

- SQLite database with contact management
- CRUD operations (Create, Read, Update, Delete)
- Data seeding with sample records
- Duplicate prevention logic

### API Integrations

- **JokeAPI**: `https://v2.jokeapi.dev/joke/Any?safe-mode`
- **GitHub API**: Repository information display
- Error handling and fallback messaging

## Development Workflow

### Before Making Changes

1. Ensure PHP extensions are enabled (PDO SQLite, cURL)
2. Check that server is running from correct directory
3. Verify database file exists and is writable
4. Test individual components before integration

### Common Issues and Solutions

1. **PDO SQLite Error**: Enable `extension=pdo_sqlite` in php.ini
2. **cURL Issues**: Add SSL verification settings and user agent
3. **File Not Found**: Ensure server runs from `/website` directory
4. **Database Errors**: Check file permissions on `website/data/`

## Debugging Guidelines

- Enable error reporting in development
- Use browser developer tools for JavaScript errors
- Check PHP server logs for backend issues
- Test API endpoints individually before integration
- Verify file paths and directory structure

## Security Considerations

- Always sanitize output with `htmlspecialchars()`
- Use prepared statements for database queries
- Validate file uploads and user input
- Set appropriate file permissions
- Use HTTPS for external API calls when possible

---

**Remember**: This project structure requires the web server to run from the `/website` directory to properly serve files and handle routing. Always check for existing terminals before creating new ones. NEVER start the web server in any other directory, it MUST be /website.
