# Contributing to Translatable Migration Builder

First off, thank you for considering contributing to the Translatable Migration Builder! It's people like you that make this package such a great tool.

## Code of Conduct

This project and everyone participating in it is governed by our code of conduct. By participating, you are expected to uphold this code. Please report unacceptable behavior to the project maintainers.

## What's the difference between a question and a bug report?

A question is when you use the package and something doesn't work as expected, or you're not sure how to do something. A bug report is when the package isn't working as documented or expected.

## Ground Rules

- Create issues for any major changes and enhancements that you wish to make. Discuss things transparently and get community feedback.
- Do not build in a way that's closed-source. Work in the open.
- Keep pull requests focused on a single topic or feature.
- Be welcoming to newcomers and encourage diverse new contributors from all backgrounds.

## Your First Contribution

Unsure where to begin contributing? You can start by looking through these beginner and help-wanted issues:

- Beginner issues - issues which should only require a few lines of code, and a test or two.
- Help wanted issues - issues which should be a bit more involved than beginner issues.

## Getting started

### Setting up your development environment

1. Fork the repository on GitHub

2. Clone your fork locally:
```bash
git clone https://github.com/your-username/translatable-migration-builder.git
cd translatable-migration-builder
```

3. Create a development branch:
```bash
git checkout -b feature/your-feature-name
```

4. Install dependencies:
```bash
composer install
npm install
```

5. Create a test Laravel application (if needed for testing):
```bash
composer create-project laravel/laravel test-app
cd test-app
composer require livewire/livewire astrotomic/laravel-translatable
```

### Running Tests

```bash
# Run PHP tests
./vendor/bin/phpunit

# Run PHP linter
./vendor/bin/pint

# Check code quality
./vendor/bin/phpstan analyse src/
```

### Making Changes

1. Make your changes in your development branch
2. Follow the coding standards (PSR-12)
3. Add tests if you're adding functionality
4. Update documentation if needed
5. Ensure all tests pass

### Coding Standards

This project follows PSR-12 coding standards.

Format your code:
```bash
./vendor/bin/pint
```

### Commit Messages

- Use the present tense ("Add feature" not "Added feature")
- Use the imperative mood ("Move cursor to..." not "Moves cursor to...")
- Limit the first line to 72 characters or less
- Reference issues and pull requests liberally after the first line
- Consider starting the commit message with an applicable emoji:
  - 🎨 `:art:` when improving the format/structure of the code
  - 🚀 `:rocket:` when improving performance
  - 📚 `:books:` when writing docs
  - 🐛 `:bug:` when fixing a bug
  - ✨ `:sparkles:` when introducing a new feature
  - ⚡ `:zap:` when improving something
  - 🔒 `:lock:` when dealing with security
  - ⬆️ `:arrow_up:` when upgrading dependencies
  - ⬇️ `:arrow_down:` when downgrading dependencies
  - 🗑️ `:wastebasket:` when removing code or files

Example:
```
✨ Add column reordering feature

- Implement drag-and-drop UI for column reordering
- Add reorderColumns method to Table builder
- Update Livewire component to handle reordering
- Add tests for column reordering functionality

Closes #123
```

### Pull Request Process

1. Update the CHANGELOG.md with details of your changes
2. Update the README.md or INSTALLATION.md with any new documentation
3. Increase version numbers in any example files and the CHANGELOG.md
4. Push to your fork
5. Open a Pull Request with a clear description of the changes

#### PR Title Format

- `[Feature] Add <feature name>`
- `[Fix] Fix <bug description>`
- `[Docs] Update <documentation>`
- `[Refactor] Refactor <component name>`
- `[Tests] Add tests for <feature>`

#### PR Description Template

```markdown
## Description
Brief description of what this PR does.

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing
Describe how you tested this change.

## Screenshots (if applicable)
Add screenshots of the UI changes.

## Checklist
- [ ] Code follows the style guidelines of this project
- [ ] I have performed a self-review of my own code
- [ ] Tests added/updated and passing
- [ ] Documentation updated
- [ ] No new warnings generated

## Related Issues
Closes #(issue number)
```

## Style Guide

### PHP Code Style

Follow PSR-12:

```php
<?php

namespace Astrotomic\TranslatableMigrationBuilder\Builders;

use Illuminate\Support\Collection;

class MyClass
{
    /**
     * Public method with documentation
     */
    public function methodName($parameter): string
    {
        return sprintf('Result: %s', $parameter);
    }

    /**
     * Protected method
     */
    protected function protectedMethod(): void
    {
        // Implementation
    }
}
```

### Naming Conventions

- **Classes**: `PascalCase` - `MigrationGenerator`
- **Methods**: `camelCase` - `generateMigration()`
- **Properties**: `camelCase` - `$tableName`
- **Constants**: `UPPER_SNAKE_CASE` - `DEFAULT_TABLE_NAME`
- **Files**: Match class name - `MigrationGenerator.php`

### Documentation

- Use PHPDoc for all public methods
- Document parameters and return types
- Add examples in comments for complex logic

```php
/**
 * Generate migration code
 *
 * @param Table $table The table to generate migration for
 * @return string The generated migration PHP code
 * 
 * @example
 * $generator = new MigrationGenerator();
 * $code = $generator->generate($table);
 */
public function generate(Table $table): string
{
    // Implementation
}
```

## Documentation

- Write clear, accessible documentation
- Provide examples for new features
- Update READMEs and guides
- Consider adding your feature to the feature list

## Reporting Bugs

### Before Submitting A Bug Report

- Check the README and INSTALLATION guides
- Check if someone already reported it
- Try to reproduce the issue

### How Do I Submit A (Good) Bug Report?

Bugs are tracked as GitHub issues. Create an issue and provide the following information by filling out the bug report template:

- **Use a clear, descriptive title**
- **Describe the exact steps which reproduce the problem**
- **Provide specific examples to demonstrate the steps**
- **Describe the behavior you observed after following the steps**
- **Explain what you expected to see instead and why**
- **Include Laravel version, PHP version, and package versions**
- **Include screenshots if applicable**

## Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. Create an issue and provide:

- **Use a clear, descriptive title**
- **Provide a step-by-step description of the suggested enhancement**
- **Provide specific examples to demonstrate the steps**
- **Describe the current behavior and what you expected instead**
- **Explain why this enhancement would be useful**

## Review Process

- At least one maintainer will review your pull request
- Changes may be requested before merge
- Maintain consistency with the existing codebase
- Help ensure the quality of the package

## Community

- Join discussions on GitHub
- Help other users with issues
- Share your use cases and improvements

## Questions?

Feel free to ask questions in GitHub issues or discussions.

---

Thank you for contributing! 🎉
