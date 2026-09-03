# EmailPOS (Email Marketing System)

PHP email builder for EPOS. Designed to run on Azure App Service against Azure Database for MySQL, with send support via Azure Communication Services and Salesforce Marketing Cloud.

## Setup

1. Copy `config.local.example.php` to `config.local.php` and add database and API credentials.  
   On Azure, set the same values as App Service application settings instead (`DB_SERVER_AZURE`, `DB_USERNAME_AZURE`, `DB_PASSWORD_AZURE`, `DB_NAME_AZURE`, `SFMC_*`, `AZURE_EMAIL_*`).
2. Copy `settings.example.json` to `settings.json` if you need local brand defaults.
3. Point the web server document root at this folder (PHP 8.1+ with mysqli and curl).
4. Create the first admin user in MySQL (do not put passwords in PHP files):

```sql
INSERT INTO users (username, name, email, password_hash, user_level, created_at)
VALUES (
  'your_admin',
  'Your Name',
  'you@example.com',
  '$2y$10$REPLACE_WITH_password_hash_OUTPUT',
  'admin',
  NOW()
);
```

Generate the hash with `php -r "echo password_hash('your-password', PASSWORD_DEFAULT), PHP_EOL;"`

## Layout

- `index.php` — login
- `emailpos.php` — editor
- `api/` — calendar, templates, SFMC send, user admin
- `includes/init.php` — session and auth bootstrap
- `emails/` — saved email JSON/HTML

## Security

Never commit `config.local.php`. Rotate any credentials that previously lived in source control (Azure MySQL, SFMC installed package, admin passwords).
