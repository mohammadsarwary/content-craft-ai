# Contributing to ContentCraft AI

Thank you for your interest in contributing! This document provides guidelines for contributing to the project.

---

## 🤝 How to Contribute

### Reporting Bugs

If you find a bug, please open an issue with:
- Clear description of the problem
- Steps to reproduce
- Expected vs. actual behavior
- WordPress version, PHP version, plugin version
- Error messages or logs (if applicable)

### Suggesting Features

Feature requests are welcome! Please open an issue with:
- Clear description of the feature
- Use cases and benefits
- Mockups or examples (if applicable)

### Pull Requests

1. **Fork the repository**
2. **Create a feature branch**: `git checkout -b feature/your-feature-name`
3. **Make your changes** (see coding standards below)
4. **Write tests** for new functionality
5. **Ensure all tests pass**
6. **Commit with clear messages**: `git commit -m "Add: feature description"`
7. **Push to your fork**: `git push origin feature/your-feature-name`
8. **Open a Pull Request** with:
   - Description of changes
   - Related issue number (if applicable)
   - Screenshots (for UI changes)

---

## 🛠️ Development Setup

### Prerequisites

- PHP 7.4+ (8.0+ recommended)
- WordPress 5.8+
- Python 3.9+
- Node.js (for JS linting)
- Docker (optional, for backend)
- Composer
- Poetry (for Python dependencies)

### WordPress Plugin Setup

```bash
# Clone repository
git clone https://github.com/yourusername/contentcraft-ai.git
cd contentcraft-ai

# Install PHP dependencies
cd plugin
composer install

# Symlink plugin to WordPress installation
ln -s $(pwd)/plugin /path/to/wordpress/wp-content/plugins/contentcraft-ai

# Activate plugin in WordPress admin
```

### FastAPI Backend Setup

```bash
# Navigate to backend
cd backend

# Copy environment file
cp .env.example .env

# Edit .env with your API keys

# Install dependencies
poetry install

# Run server
poetry run uvicorn app.main:app --reload --port 8000

# OR use Docker
cd ../infra
docker-compose up -d
```

---

## 📝 Coding Standards

### PHP (WordPress)

- Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/)
- Use tabs for indentation
- Use Yoda conditions where appropriate
- All functions prefixed with `contentcraft_`
- All classes in `ContentCraft\` namespace
- Text domain: `contentcraft-ai`

**Check standards**:
```bash
composer run phpcs
```

**Auto-fix**:
```bash
composer run phpcbf
```

### Python (FastAPI)

- Follow [PEP 8](https://pep8.org/)
- Use 4 spaces for indentation
- Type hints for all function parameters and returns
- Docstrings for all public functions/classes
- Max line length: 100 characters

**Check standards**:
```bash
poetry run black --check .
poetry run flake8
```

**Auto-fix**:
```bash
poetry run black .
```

### JavaScript

- Use ES6+ syntax
- 2 spaces for indentation
- Semicolons required
- Use `const` and `let`, avoid `var`

**Check standards**:
```bash
npm run lint
```

---

## 🧪 Testing

### PHP Tests (PHPUnit)

```bash
cd plugin
composer test

# With coverage
composer test:coverage
```

### Python Tests (pytest)

```bash
cd backend
poetry run pytest

# With coverage
poetry run pytest --cov=app --cov-report=html
```

### End-to-End Tests

Follow scenarios in `/docs/demo-scripts.md`

---

## 🎨 UI/UX Guidelines

- Use WordPress admin color scheme
- Follow WordPress admin UI patterns
- Ensure accessibility (WCAG 2.1 AA)
- Mobile-responsive design
- Use WordPress admin components (no custom frameworks)
- Add loading states for async operations
- Clear error messages

---

## 🔒 Security Guidelines

- **Never** expose API keys to frontend
- **Always** sanitize user inputs
- **Always** escape outputs
- **Always** verify nonces on AJAX requests
- **Always** check user capabilities
- Use `wp_remote_post()` instead of `curl`
- Validate and sanitize all REST API inputs
- Use prepared statements for custom database queries

---

## 📚 Documentation

When adding features:
- Update relevant docs in `/docs`
- Add JSDoc comments for JavaScript
- Add docblocks for PHP functions
- Add docstrings for Python functions
- Update API contracts if changing endpoints
- Add demo scripts if user-facing feature

---

## 🌍 Internationalization (i18n)

- Wrap all user-facing strings: `__('Text', 'contentcraft-ai')`
- Use `_e()` for direct echo
- Use `_n()` for plurals
- Use `esc_html__()` / `esc_attr__()` for escaped output
- Update `.pot` file after adding strings:

```bash
wp i18n make-pot plugin/ plugin/languages/contentcraft-ai.pot
```

---

## 🚀 Release Process

1. Update version in `plugin/contentcraft-ai.php`
2. Update version in `backend/app/main.py`
3. Update `CHANGELOG.md` with release notes
4. Create Git tag: `git tag -a v1.0.0 -m "Release 1.0.0"`
5. Push tag: `git push origin v1.0.0`
6. GitHub Actions will build and create release

---

## 📋 Commit Message Guidelines

Use conventional commit format:

- `feat:` New feature
- `fix:` Bug fix
- `docs:` Documentation changes
- `style:` Code style changes (formatting)
- `refactor:` Code refactoring
- `test:` Adding or updating tests
- `chore:` Maintenance tasks

**Examples**:
```
feat: Add bulk generation progress tracking
fix: Resolve WooCommerce meta box not appearing
docs: Update API contracts for new endpoint
test: Add tests for brand voice training
```

---

## 🐛 Debugging

### Enable WordPress Debug Mode

```php
// wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### FastAPI Debug Mode

```bash
# backend/.env
DEBUG=true

# Run with auto-reload
uvicorn app.main:app --reload --log-level debug
```

### View Logs

**WordPress**:
```bash
tail -f wp-content/debug.log
```

**FastAPI**:
```bash
docker-compose logs -f fastapi
```

---

## 🤔 Questions?

- Open an issue for technical questions
- Check existing documentation in `/docs`
- Review `AI_AGENT_INSTRUCTIONS.md` for architecture details

---

## 📜 License

By contributing, you agree that your contributions will be licensed under the MIT License.

---

**Thank you for contributing to ContentCraft AI!** 🚀


