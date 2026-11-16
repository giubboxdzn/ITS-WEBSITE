#!/usr/bin/env bash
# Serve the local WordPress with PHP's built-in server (this machine only: HOST=0.0.0.0 to expose it).
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
HOST="${HOST:-127.0.0.1}"
PORT="${PORT:-8080}"
export WP_HOME="${WP_HOME:-http://localhost:$PORT}"
echo "http://localhost:$PORT  (admin / admin) — Ctrl+C per fermare"
exec php -S "$HOST:$PORT" -t "$ROOT/.wp/wordpress" "$ROOT/dev/router.php"
