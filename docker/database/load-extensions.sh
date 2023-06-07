#!/bin/sh

extensions="citext"

echo "Installing extensions: "

for extension in ${extensions}
do
    echo "  Installing extension '$extension'"

    echo "    Installing for the database '$POSTGRES_DB'..."
    psql -v ON_ERROR_STOP=1 -U "$POSTGRES_USER" -d "$POSTGRES_DB" -c "CREATE EXTENSION IF NOT EXISTS $extension;"

    echo "    Installing for the database 'postgres'..."
    psql -v ON_ERROR_STOP=1 -U "$POSTGRES_USER" -d postgres -c "CREATE EXTENSION IF NOT EXISTS $extension;"

    echo "    Installing for the template 'template1'..."
    psql -v ON_ERROR_STOP=1 -U "$POSTGRES_USER" -T template1 -c "CREATE EXTENSION IF NOT EXISTS $extension;"

    echo "    Installing for the template 'template0'..."
    psql -v ON_ERROR_STOP=1 -U "$POSTGRES_USER" -T template0 -c "CREATE EXTENSION IF NOT EXISTS $extension;"

    echo ""

done


