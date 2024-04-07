# Volunteering Web Application

## React modules installed and configured
React Router DOM  
Tailwind CSS  
React Hot Toast  
uuid  
@heroicons/react/outline

## How to use

### Frontend

Pull from the main branch
```sh
cd Volunteering
npm install
npm run dev
```
Note: You can omit `npm install` if you have the `node_modules` folder up-to-date

### Backend

Pull from the main branch
```sh
cd api
composer install

# Create the database
cd ..
./createdb.ps1

# Upload api to the server. Subdirectories of the webroot are not supported
scp -r api/ user@server:/var/www/html/
```
Note: You can omit `composer install` if you have the `vendor` folder up-to-date

Note: The `createdb.ps1` script requires `sqlite3` and `php` to be available on your PATH

Note: PHP must have the following extensions enabled in `php.ini`:
```ini
extension=gd
extension=pdo_sqlite
```

Note: The `createdb.ps1` script is for Windows. No Linux or MacOS equivalent is provided, but the script is simple enough to be easily ported
or run manually.

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

## Attributions
"Notification" icon by Icons8 - icons8.com  
Volunteering browser tab icon by Freepik - Flaticon: https://www.flaticon.com/free-icon/help_4767194?term=volunteering&page=1&position=8&origin=search&related_id=4767194
