#!/usr/bin/env bash
# Local WordPress for developing the its-rossellini theme.
# WordPress + SQLite (no MySQL), theme and dev content model linked from the repo,
# content imported read-only from the public site into the local database.
# Works on Linux, macOS and Windows (Git Bash, with PHP and Python for Windows on the PATH).
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

# Git Bash / MSYS: PHP and Python are native Windows programs and need Windows paths.
WINDOWS=0
case "$(uname -s)" in MINGW* | MSYS* | CYGWIN*) WINDOWS=1 ;; esac
native_path() {
	if [ "$WINDOWS" = 1 ]; then cygpath -m "$1"; else printf '%s' "$1"; fi
}

if ! command -v php > /dev/null 2>&1; then
	echo "PHP non trovato. Installa PHP >= 8.1 e aggiungilo al PATH (vedi README, Sviluppo locale)." >&2
	exit 1
fi
if ! php -r 'exit(PHP_VERSION_ID >= 80100 && extension_loaded("pdo_sqlite") ? 0 : 1);'; then
	echo "Serve PHP >= 8.1 con l'estensione pdo_sqlite attiva (php.ini). Trovato: $(php -r 'echo PHP_VERSION;')" >&2
	exit 1
fi

mkdir -p "$WPDIR"

if [ ! -f "$WP/wp-settings.php" ]; then
	git -c advice.detachedHead=false clone -q --depth 1 --branch "$WP_TAG" https://github.com/WordPress/WordPress.git "$WP"
	rm -rf "$WP/.git"
fi

if [ ! -d "$WPDIR/sqlite-database-integration" ]; then
	git -c advice.detachedHead=false clone -q --depth 1 --branch "$SQLITE_TAG" https://github.com/WordPress/sqlite-database-integration.git "$WPDIR/sqlite-database-integration"
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
	PHP_WPDIR="$(native_path "$WPDIR")"
	SALTS="$(php -r 'foreach (["AUTH_KEY","SECURE_AUTH_KEY","LOGGED_IN_KEY","NONCE_KEY","AUTH_SALT","SECURE_AUTH_SALT","LOGGED_IN_SALT","NONCE_SALT"] as $k) { echo "define(\x27$k\x27, \x27" . bin2hex(random_bytes(32)) . "\x27);\n"; }')"
	cat > "$WP/wp-config.php" <<PHP
<?php
define( 'DB_NAME', 'wordpress' );
define( 'DB_USER', '' );
define( 'DB_PASSWORD', '' );
define( 'DB_HOST', '' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );
define( 'DB_DIR', '$PHP_WPDIR/db/' );
define( 'DB_FILE', 'its.sqlite' );
$SALTS
\$table_prefix = 'wp_';
define( 'WP_ENVIRONMENT_TYPE', 'local' );
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', '$PHP_WPDIR/debug.log' );
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

# Theme and dev-only content model live in the repo; WordPress sees them through links.
THEME_LINK="$WP/wp-content/themes/its-rossellini"
MU="$WP/wp-content/mu-plugins"
mkdir -p "$MU"
if [ "$WINDOWS" = 1 ]; then
	# A junction needs neither admin rights nor Developer Mode; MSYS "ln -s" would copy instead.
	if [ ! -e "$THEME_LINK" ]; then
		MSYS_NO_PATHCONV=1 MSYS2_ARG_CONV_EXCL='*' cmd /c mklink /J \
			"$(cygpath -w "$THEME_LINK")" "$(cygpath -w "$ROOT/wp-content/themes/its-rossellini")" > /dev/null
	fi
	rm -f "$MU/its-dev-content-model.php"
	printf "<?php\nrequire '%s';\n" "$(native_path "$ROOT/dev/mu-plugins/its-dev-content-model.php")" > "$MU/its-dev-content-model.php"
else
	ln -sfn "$ROOT/wp-content/themes/its-rossellini" "$THEME_LINK"
	ln -sfn "$ROOT/dev/mu-plugins/its-dev-content-model.php" "$MU/its-dev-content-model.php"
fi

php "$ROOT/dev/install.php"

if [ "${1:-}" != "--no-seed" ]; then
	if [ ! -f "$WPDIR/seed/data.json" ]; then
		# On Windows "python3" is often the Microsoft Store stub: take the first interpreter that runs.
		PY=""
		for candidate in python3 python py; do
			if command -v "$candidate" > /dev/null 2>&1 && "$candidate" -c 'import sys; sys.exit(sys.version_info < (3, 8))' > /dev/null 2>&1; then
				PY="$candidate"
				break
			fi
		done
		if [ -z "$PY" ]; then
			echo "Python 3.8+ non trovato (serve solo per scaricare i contenuti). Installalo o usa: dev/setup.sh --no-seed" >&2
			exit 1
		fi
		"$PY" -I "$ROOT/dev/seed/fetch.py" --out "$WPDIR/seed"
	fi
	php "$ROOT/dev/seed/import.php"
fi
echo "Ready. Run dev/serve.sh and open http://localhost:8080 (admin / admin)."
