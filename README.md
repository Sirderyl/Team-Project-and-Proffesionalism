# Volunteering Web Application

## React modules installed and configured
React Router DOM
Tailwind CSS
React Hot Toast
uuid
@heroicons/react/outline

## How to use
Pull from the main branch
```
cd Volunteering
npm install
npm run dev
```
Note: You can omit `npm install` if you have the `node_modules` folder up-to-date

## Dummy Data Creation
The `createdb.ps1` script is used to generate a dummy database. For it to function,
you need to have `sqlite3` and `php` available on your PATH. PHP must also be configured
with:
```ini
extension=gd
extension=pdo_sqlite
```

### Credentials For Dummy Users
Dummy email addresses and passwords are generated in a deterministic manner with email `$id@example.com` and password `password$id`
as shown in the table below:
| Email           | Password    |
| --------------- | ----------- |
| `1@example.com` | `password1` |
| `2@example.com` | `password2` |
| ...             | ...         |
| `1@manager.com` | `password1` |
| `2@manager.com` | `password2` |
| ...             | ...         |

## Unit Testing
A test task is configured to run the unit tests via `Tasks: Run Test Task` in VSCode. This requires the XDebug container to be built via
`Tasks: Run Task` -> `Build XDebug Container` before running the tests.

Tests are written with the PHPUnit framework and are located in the `./api/tests` directory. Coverage reports are generated in the `./coverage` directory.
