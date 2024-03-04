# Create the database and schema for the application
# Requires sqlite3 to be on the system path
# pdo_sqlite extension must be enabled in php.ini

# Path to the database file and schema file
$DB_PATH = ".\api\database.db"
$SCHEMA_PATH = ".\api\schema.sql"

# Check if the database file already exists
if (Test-Path $DB_PATH) {
    # Ask the user if they want to delete the database
    $delete_db = Read-Host "Database already exists. Do you want to delete it? (y/n)"
    switch ($delete_db.ToLower()) {
        "y" { Remove-Item $DB_PATH -Force }
        "n" { Write-Host "Database not deleted." -ForegroundColor Yellow; exit 1 }
        default { Write-Host "Invalid input." -ForegroundColor Red; exit 1 }
    }
}

# Execute the schema to create the database
Get-Content $SCHEMA_PATH | sqlite3 $DB_PATH

cd api
php createdummydata.php
cd ..
