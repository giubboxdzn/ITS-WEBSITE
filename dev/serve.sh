#!/usr/bin/env bash
# Serve the local WordPress with PHP's built-in server.
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
PORT="${PORT:-8080}"
export WP_HOME="${WP_HOME:-http://localhost:$PORT}"
exec php -S "0.0.0.0:$PORT" -t "$ROOT/.wp/wordpress" "$ROOT/dev/router.php"
