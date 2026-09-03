<?php
// Copy this file to config.local.php and fill in values.
// config.local.php is gitignored — never commit it.

putenv('DB_SERVER=localhost');
putenv('DB_USERNAME=your_db_username');
putenv('DB_PASSWORD=your_db_password');
putenv('DB_NAME=emailpos');

// Salesforce Marketing Cloud installed package
putenv('SFMC_CLIENT_ID=');
putenv('SFMC_CLIENT_SECRET=');
putenv('SFMC_AUTH_BASE_URI=https://YOUR_SUBDOMAIN.auth.marketingcloudapis.com');
putenv('SFMC_REST_BASE_URI=https://YOUR_SUBDOMAIN.rest.marketingcloudapis.com');

// Azure Communication Services (test email)
putenv('AZURE_EMAIL_ENDPOINT=');
putenv('AZURE_EMAIL_ACCESS_KEY=');
putenv('AZURE_EMAIL_SENDER=');
