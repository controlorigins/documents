# GitHub Copilot Instructions Best Practices for PHP Projects

## Overview

This document provides comprehensive best practices for creating and maintaining effective `.github/copilot-instructions.md` files specifically for PHP development projects. Based on Microsoft documentation and real-world implementation experience, these guidelines will help you maximize GitHub Copilot's effectiveness in your PHP development workflow.

## What are Copilot Instructions?

GitHub Copilot Instructions are custom configuration files that provide persistent context to GitHub Copilot, eliminating the need to repeatedly provide the same information in every chat prompt. When properly configured, these instructions enable Copilot to understand your project structure, coding standards, and development preferences automatically.

### Key Benefits

- **Consistent Context**: Automatically includes project-specific information in all Copilot interactions
- **Reduced Repetition**: Eliminates need to explain project structure and preferences repeatedly  
- **Team Alignment**: Ensures all team members receive consistent AI assistance
- **Improved Accuracy**: More relevant suggestions based on project-specific context

## File Location and Structure

### Required Location

```text
/.github/copilot-instructions.md
```

**Critical**: The file MUST be located at the repository root in the `.github` folder with the exact filename `copilot-instructions.md`.

### Basic Structure Template

```markdown
# GitHub Copilot Instructions

## Project Overview
[Brief description of project purpose and scope]

## Project Structure
[Key directories and their purposes]

## Development Environment
[PHP version, dependencies, tools]

## Coding Standards
[PHP best practices, naming conventions, security guidelines]

## Common Commands
[Frequently used development commands]

## Development Workflow
[Process guidelines and common tasks]
```

## Essential Sections for PHP Projects

### 1. Project Overview

Provide a clear, concise description of your PHP project:

```markdown
## Project Overview

This is a PHP-based [web application/API/CLI tool] that [primary purpose].
Key features include [list 3-5 main features].
Target audience: [developers/end-users/etc.]
```

### 2. Project Structure

Document your directory organization with explanations:

```markdown
## Project Structure

- **Root Directory**: `/path/to/project`
- **Web Directory**: `/public` or `/website` (document your web root)
- **Source Code**: `/src` or `/app` (main PHP files location)
- **Configuration**: `/config` (settings and environment files)
- **Dependencies**: `/vendor` (Composer packages)
- **Assets**: `/assets` (CSS, JS, images)
- **Database**: `/database` or `/data` (migrations, seeds, SQLite files)
- **Tests**: `/tests` (PHPUnit tests)
- **Documentation**: `/docs` (project documentation)
```

### 3. Development Environment

Specify your PHP environment details:

```markdown
## Development Environment

- **PHP Version**: 8.2+ (specify minimum version)
- **Web Server**: Apache/Nginx/PHP built-in server
- **Database**: MySQL/PostgreSQL/SQLite
- **Dependencies**: Composer-managed
- **Testing Framework**: PHPUnit
- **Code Quality**: PHP_CodeSniffer, PHPStan, Psalm
- **Frontend**: Bootstrap/Tailwind/Vue.js (if applicable)
```

### 4. Coding Standards

Define your PHP coding practices:

```markdown
## Coding Standards

### PHP Best Practices
- Follow PSR-12 coding standard
- Use strict types: `declare(strict_types=1);`
- Always sanitize output with `htmlspecialchars()`
- Use prepared statements for database queries
- Implement proper error handling and logging

### Security Guidelines
- Validate and sanitize all user input
- Use CSRF protection for forms
- Implement proper authentication and authorization
- Store sensitive data in environment variables
- Use HTTPS for production environments

### Database Operations
- Use PDO with prepared statements
- Implement proper transaction handling
- Include error handling for all database operations
- Use connection pooling when appropriate
```

### 5. File and Directory Rules

Establish clear conventions:

```markdown
## File Structure Rules

- **Web Root**: All publicly accessible files in `/public` or `/website`
- **PHP Classes**: PSR-4 autoloading structure in `/src`
- **Configuration**: Environment-specific configs in `/config`
- **Templates**: View files in `/templates` or `/views`
- **Static Assets**: CSS, JS, images in `/assets` with organized subdirectories
- **Database Migrations**: Versioned files in `/database/migrations`

### Naming Conventions
- **Classes**: PascalCase (e.g., `UserController`)
- **Methods**: camelCase (e.g., `getUserData()`)
- **Variables**: camelCase (e.g., `$userData`)
- **Constants**: UPPER_SNAKE_CASE (e.g., `MAX_UPLOAD_SIZE`)
- **Files**: snake_case for includes, PascalCase for classes
```

### 6. Common Commands

Document frequently used development commands:

```markdown
## Common Commands

### Composer Management

```bash
# Install dependencies
composer install

# Update dependencies  
composer update

# Add new package
composer require vendor/package
```

### Development Server

```bash
# Start PHP built-in server
php -S localhost:8000 -t public/

# Check PHP modules
php -m

# Run syntax check
php -l filename.php
```

### Testing

```bash
# Run PHPUnit tests
./vendor/bin/phpunit

# Run with coverage
./vendor/bin/phpunit --coverage-html coverage/
```

### 7. Framework-Specific Guidelines

If using a PHP framework, include specific instructions:

```markdown
## Framework Guidelines

### Laravel Projects
- Use Eloquent ORM for database operations
- Follow Laravel naming conventions
- Use Artisan commands for scaffolding
- Implement proper middleware for authentication

### Symfony Projects  
- Use Doctrine ORM for database operations
- Follow Symfony best practices for controllers
- Use dependency injection container
- Implement proper event listeners

### Custom Framework
- [Document your custom patterns and conventions]
- [Include common architectural decisions]
```

### 8. Development Workflow

Outline your development process:

```markdown
## Development Workflow

### Before Making Changes
1. Pull latest changes from main branch
2. Install/update dependencies with Composer
3. Run database migrations if needed
4. Verify development environment is running

### Code Quality Checks
- Run PHPStan: `./vendor/bin/phpstan analyse`
- Check code style: `./vendor/bin/phpcs`
- Fix code style: `./vendor/bin/phpcbf`
- Run tests: `./vendor/bin/phpunit`

### Common Issues and Solutions
1. **Composer Issues**: Clear cache with `composer clear-cache`
2. **Autoload Issues**: Run `composer dump-autoload`
3. **Permission Issues**: Check file permissions on cache/storage directories
```

## Advanced Configuration Patterns

### Context-Aware Instructions

Include conditional guidance based on file types:

```markdown
## File-Specific Guidelines

### When Working with Controllers
- Implement proper request validation
- Use dependency injection for services
- Return appropriate HTTP status codes
- Include proper error handling

### When Working with Models
- Define relationships using Eloquent/Doctrine
- Implement proper validation rules
- Use accessors and mutators when needed
- Include proper database constraints

### When Working with Views/Templates
- Escape all output to prevent XSS
- Use template inheritance when possible
- Keep logic minimal in templates
- Implement proper CSRF protection
```

### Integration Guidelines

For projects with external integrations:

```markdown
## External Integrations

### API Development
- Use proper HTTP status codes
- Implement rate limiting
- Include comprehensive error responses
- Document endpoints with OpenAPI/Swagger

### Database Integration
- Use connection pooling for high-traffic applications
- Implement proper indexing strategies
- Use transactions for data consistency
- Include backup and recovery procedures

### Third-Party Services
- Always include error handling for external API calls
- Set appropriate timeout values
- Use circuit breaker patterns for critical services
- Cache responses when appropriate
```

## Implementation Tips

### 1. Start Simple

Begin with basic project information and expand gradually:

- Project overview and structure
- Essential coding standards
- Common commands
- Add complexity as needed

### 2. Keep Instructions Current

- Review and update instructions regularly
- Remove outdated information
- Add new patterns as they emerge
- Sync with team coding standards

### 3. Use Clear, Actionable Language

- Write instructions as if for a new team member
- Use specific examples rather than general statements
- Include rationale for important decisions
- Provide alternatives when appropriate

### 4. Test with Real Scenarios

- Use instructions with actual Copilot interactions
- Verify suggestions align with documented standards
- Refine based on actual usage patterns
- Gather feedback from team members

## Validation and Testing

### Copilot Integration Check

1. Create or edit a PHP file
2. Ask Copilot to generate code following your standards
3. Verify suggestions match documented conventions
4. Check if project-specific context is applied

### Team Validation

- Share instructions with team members
- Gather feedback on clarity and completeness
- Test with various development scenarios
- Update based on real-world usage

## Common Pitfalls to Avoid

### 1. Over-specification

- Don't include every possible coding rule
- Focus on project-specific deviations from standards
- Avoid repeating well-known PHP best practices

### 2. Outdated Information

- Regular maintenance is essential
- Remove deprecated patterns and tools
- Update version requirements as needed

### 3. Inconsistent Guidelines

- Ensure instructions don't contradict each other
- Align with existing team documentation
- Maintain consistency across related projects

### 4. Lack of Context

- Explain why certain patterns are preferred
- Provide examples of good and bad implementations
- Include links to relevant documentation

## Maintenance Strategy

### Regular Review Schedule

- **Monthly**: Check for outdated tool versions
- **Quarterly**: Review coding standards alignment  
- **Per Release**: Update environment requirements
- **As Needed**: Add new patterns and conventions

### Version Control

- Treat instructions as code - review changes
- Use descriptive commit messages for updates
- Tag major instruction updates
- Maintain changelog for significant changes

## Example Implementation

For a complete example of these best practices in action, see the `.github/copilot-instructions.md` file in this repository, which demonstrates:

- Clear project structure documentation
- PHP-specific coding standards
- Development environment setup
- Common command reference
- Security and best practice guidelines
- Framework-specific patterns (where applicable)

## Conclusion

Effective GitHub Copilot Instructions are essential for maximizing AI assistance in PHP development. By following these best practices, you'll create instructions that provide consistent, relevant context to GitHub Copilot, improving code quality and development velocity.

Remember that copilot-instructions.md is a living document that should evolve with your project. Regular maintenance and team feedback are key to maintaining its effectiveness.

## Resources

- [Microsoft Documentation: Customize GitHub Copilot Chat](https://learn.microsoft.com/en-us/visualstudio/ide/copilot-chat-context)
- [GitHub Copilot Documentation](https://docs.github.com/en/copilot)
- [PSR Standards](https://www.php-fig.org/psr/)
- [PHP Best Practices](https://phptherightway.com/)

---

*This document represents best practices gathered from Microsoft documentation, real-world implementation, and PHP community standards. Adapt these guidelines to fit your specific project needs and team preferences.*
