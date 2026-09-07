# Security Policy

Do not report suspected vulnerabilities in public issues.

For a real deployment, use your organization's private security-reporting channel and rotate any credential that may have been exposed.

## Baseline expectations

- never commit `.env` or production credentials;
- use strong unique `APP_KEY` values per environment;
- serve production traffic over HTTPS;
- use a supported PHP/Laravel version;
- restrict access to audit logs and system settings;
- review role assignments regularly;
- expire/revoke API tokens that are no longer required;
- keep secrets out of audit metadata and application logs;
- define backup, retention and disaster-recovery policies before production use.

This repository is a starter architecture, not a substitute for an organization-specific security review.
