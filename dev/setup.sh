#!/usr/bin/env bash
# Local WordPress for developing the its-rossellini theme.
# WordPress + SQLite (no MySQL), theme and dev content model symlinked from the repo,
# content imported read-only from the public site into the local database.
#
#   dev/setup.sh            # install (idempotent) + import seed
#   dev/setup.sh --no-seed  # install only
#   dev/serve.sh            # http://localhost:8080  (admin / admin)
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
WPDIR="$ROOT/.wp"
WP="$WPDIR/wordpress"
WP_TAG="${WP_TAG:-7.1.4}"
SQLITE_TAG="${SQLITE_TAG:-v3.1.3}"

mkdir -p "$WPDIR"

if [ ! -f "$WP/wp-settings.php" ]; then
	git clone -q --depth 1 --branch "$WP_TAG" https://github.com/WordPress/WordPress.git "$WP"
	rm -rf "$WP/.git"
fi

if [ ! -d "$WPDIR/sqlite-database-integration" ]; then
	git clone -q --depth 1 --branch "$SQLITE_TAG" https://github.com/WordPress/sqlite-database-integration.git "$WPDIR/sqlite-database-integration"
	rm -rf "$WPDIR/sqlite-database-integration/.git"
fi

PLUGIN="$WP/wp-content/plugins/sqlite-database-integration"
if [ ! -f "$PLUGIN/load.php" ]; then
	rm -rf "$PLUGIN"
	cp -R "$WPDIR/sqlite-database-integration/packages/plugin-sqlite-database-integration" "$PLUGIN"
	rm -rf "$PLUGIN/wp-includes/database"
	mkdir -p "$PLUGIN/wp-includes"
	cp -R "$WPDIR/sqlite-database-integration/packages/mysql-on-sqlite/src" "$PLUGIN/wp-includes/database"
fi
if [ ! -f "$WP/wp-content/db.php" ]; then
	sed -e "s#'{SQLITE_IMPLEMENTATION_FOLDER_PATH}'#__DIR__.'/plugins/sqlite-database-integration'#g" \
		-e "s#{SQLITE_PLUGIN}#sqlite-database-integration/load.php#g" \
		"$PLUGIN/db.copy" > "$WP/wp-content/db.php"
fi

if [ ! -f "$WP/wp-config.php" ]; then
	SALTS="$(php -r 'foreach (["AUTH_KEY","SECURE_AUTH_KEY","LOGGED_IN_KEY","NONCE_KEY","AUTH_SALT","SECURE_AUTH_SALT","LOGGED_IN_SALT","NONCE_SALT"] as $k) { echo "define(\x27$k\x27, \x27" . bin2hex(random_bytes(32)) . "\x27);\n"; }')"
	cat > "$WP/wp-config.php" <<PHP
<?php
define( 'DB_NAME', 'wordpress' );
define( 'DB_USER', '' );
define( 'DB_PASSWORD', '' );
define( 'DB_HOST', '' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );
define( 'DB_DIR', '$WPDIR/db/' );
define( 'DB_FILE', 'its.sqlite' );
$SALTS
\$table_prefix = 'wp_';
define( 'WP_ENVIRONMENT_TYPE', 'local' );
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', '$WPDIR/debug.log' );
define( 'WP_DEBUG_DISPLAY', true );
define( 'WP_HOME', getenv( 'WP_HOME' ) ?: 'http://localhost:8080' );
define( 'WP_SITEURL', getenv( 'WP_HOME' ) ?: 'http://localhost:8080' );
define( 'AUTOMATIC_UPDATER_DISABLED', true );
define( 'WP_HTTP_BLOCK_EXTERNAL', true );
define( 'DISALLOW_FILE_EDIT', true );
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
require_once ABSPATH . 'wp-settings.php';
PHP
fi
mkdir -p "$WPDIR/db"

# Theme and dev-only content model live in the repo; WordPress sees them through symlinks.
ln -sfn "$ROOT/wp-content/themes/its-rossellini" "$WP/wp-content/themes/its-rossellini"
mkdir -p "$WP/wp-content/mu-plugins"
ln -sfn "$ROOT/dev/mu-plugins/its-dev-content-model.php" "$WP/wp-content/mu-plugins/its-dev-content-model.php"

php "$ROOT/dev/install.php"

if [ "${1:-}" != "--no-seed" ]; then
	if [ ! -f "$WPDIR/seed/data.json" ]; then
		python3 -I "$ROOT/dev/seed/fetch.py" --out "$WPDIR/seed"
	fi
	php "$ROOT/dev/seed/import.php"
fi
echo "Ready. Run dev/serve.sh and open http://localhost:8080 (admin / admin)."
