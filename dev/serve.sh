#!/usr/bin/env bash
# Serve the local WordPress with PHP's built-in server, on this PC and on the local network
# (open it from a phone on the same Wi-Fi with the PC's IP). HOST=127.0.0.1 keeps it on this PC only.
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
HOST="${HOST:-0.0.0.0}"
PORT="${PORT:-8080}"
export WP_HOME="${WP_HOME:-http://localhost:$PORT}"
# The PC's address on the local network: the interface used for outgoing traffic (nothing is sent).
LAN_IP="$(php -r '$s = @stream_socket_client("udp://8.8.8.8:53", $e, $m, 1); if ($s) { echo explode(":", (string) stream_socket_get_name($s, false))[0]; }' 2> /dev/null || true)"
echo "Su questo PC:    http://localhost:$PORT  (admin / admin)"
if [ "$HOST" = "0.0.0.0" ] && [ -n "$LAN_IP" ] && [ "$LAN_IP" != "0.0.0.0" ]; then
	echo "Dal telefono:    http://$LAN_IP:$PORT  (stessa rete Wi-Fi)"
fi
echo "Ctrl+C per fermare"
# expose_php=0: no "X-Powered-By: PHP/x.y" header, as production should be (see server/htaccess-sicurezza.conf).
exec php -d expose_php=0 -S "$HOST:$PORT" -t "$ROOT/.wp/wordpress" "$ROOT/dev/router.php"
