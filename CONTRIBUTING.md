# Contributing

Use a focused branch and pull request. Explain the enterprise concern being solved, not only the code change.

Before opening a PR:

```bash
php artisan test
vendor/bin/pint --test
```

Changes involving authentication, authorization, audit logging or public API data should include tests that demonstrate both the allowed and denied path. Do not include client code, production data, secrets, copied proprietary assets or credentials.
