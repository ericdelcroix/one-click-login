# OneClick Login for Adminer
Display a list of predefined database servers to login with just one click.
This version of the plugin supports multiple identifiers on the same host.
It now accepts:

- The old format (1 profile per host).
- A new multi-profile format via profiles.

Create a file servers.php and define your database details with the following structure.
## 1) Single profile per host (legacy format)

```php
'db-host.example.local' => [
    'username'  => 'db_user',
    'pass'      => 'db_pass',
    'label'     => 'My App - RW',
    'databases' => [
        'app_main' => 'app_main',
        'app_logs' => 'app_logs',
    ],
],
```

## 2) Multiple profiles on the same host (recommended format)

Use this format when you need multiple accounts (for example: read/write + read-only) on the same server.

```php
'db-host.example.local' => [
    'profiles' => [
        [
            'username'  => 'app_admin',
            'pass'      => 'admin_pass',
            'label'     => 'My App - RW',
            'databases' => [
                'app_main' => 'app_main',
            ],
        ],
        [
            'username'  => 'app_readonly',
            'pass'      => 'readonly_pass',
            'label'     => 'My App - RO',
            'databases' => [
                'app_main' => 'app_main',
            ],
        ],
    ],
],
```

## Notes

- The top-level key (host) must be unique in a PHP array.
- If the same host needs multiple username/password pairs, group them under `profiles`.
- The plugin remains compatible with the legacy format (single profile per host).

Instantiate OnClick Login according to adminer instructions from [adminer](https://www.adminer.org/plugins/#use)
```
new OneClickLogin(include 'path/to/servers.php');
```

📢 Don't use this in production environment unless the access is restricted
