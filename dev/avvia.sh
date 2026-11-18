#!/usr/bin/env bash
# The local site in one step: checks what is needed (and offers to install it), installs WordPress the first
# time (dev/setup.sh), starts the server (dev/serve.sh) on a free port and opens the browser.
# The double-click launchers in the repository root use it: avvia-windows.cmd, avvia-mac.command, avvia-linux.sh.
#
#   dev/avvia.sh             # the site on this PC only
#   dev/avvia.sh --rete      # reachable from a phone on the same Wi-Fi too (the admin as well: trusted networks only)
#   dev/avvia.sh --aggiorna  # runs dev/setup.sh again first: fresh content from the live site, plugins, languages
set -eo pipefail  # no -u: macOS ships bash 3.2, where an empty array counts as unset

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

HOST=127.0.0.1
REFRESH=0
for arg in "$@"; do
	case "$arg" in
		--rete) HOST=0.0.0.0 ;;
		--aggiorna) REFRESH=1 ;;
		-h | --help)
			sed -n '2,8p' "$0" | sed 's/^# \{0,1\}//'
			exit 0
			;;
		*)
			echo "Opzione sconosciuta: $arg (vedi dev/avvia.sh --help)" >&2
			exit 2
			;;
	esac
done

OS="$(uname -s)"
case "$OS" in
	MINGW* | MSYS* | CYGWIN*) OS=Windows ;;
esac

line() { printf '\n  %s\n' "$*"; }

# ---------- What is needed ----------

NEED_SETUP=$REFRESH
if [ ! -f .wp/wordpress/wp-settings.php ] || [ ! -f .wp/wordpress/wp-config.php ] || [ ! -f .wp/db/its.sqlite ]; then
	NEED_SETUP=1
fi

php_ok() {
	command -v php > /dev/null 2>&1 && php -r 'exit(PHP_VERSION_ID >= 80100 && extension_loaded("pdo_sqlite") ? 0 : 1);' 2> /dev/null
}
PY=""
find_python() {
	PY=""
	for candidate in python3 python py; do
		# On Windows "python3" is often the Microsoft Store stub: take the first interpreter that runs.
		if command -v "$candidate" > /dev/null 2>&1 && "$candidate" -c 'import sys; sys.exit(sys.version_info < (3, 8))' > /dev/null 2>&1; then
			PY="$candidate"
			return 0
		fi
	done
	return 1
}

missing() {
	MISSING=()
	command -v git > /dev/null 2>&1 || MISSING+=(git)
	php_ok || MISSING+=(php)
	# Python only downloads the content, the first time.
	if [ "$NEED_SETUP" = 1 ] && ! find_python; then MISSING+=(python); fi
}

# Install commands per system, offered and run only after a yes.
install_command() {
	local pkgs=()
	case "$OS" in
		Darwin)
			if [[ " ${MISSING[*]} " == *" git "* ]] && ! command -v brew > /dev/null 2>&1; then
				echo "xcode-select --install"
				return
			fi
			command -v brew > /dev/null 2>&1 || return 1
			for m in "${MISSING[@]}"; do
				case $m in php) pkgs+=(php) ;; python) pkgs+=(python) ;; git) pkgs+=(git) ;; esac
			done
			echo "brew install ${pkgs[*]}"
			;;
		Linux)
			command -v apt-get > /dev/null 2>&1 || return 1
			for m in "${MISSING[@]}"; do
				case $m in
					php) pkgs+=(php-cli php-sqlite3 php-mbstring php-gd php-curl php-xml php-zip) ;;
					python) pkgs+=(python3) ;;
					git) pkgs+=(git) ;;
				esac
			done
			echo "sudo apt-get install -y ${pkgs[*]}"
			;;
		*) return 1 ;;
	esac
}

label() {
	case $1 in
		php) echo "PHP 8.1 o successivo, con l'estensione pdo_sqlite" ;;
		python) echo "Python 3.8 o successivo (serve solo la prima volta, per scaricare i contenuti)" ;;
		git) echo "git" ;;
	esac
}

missing
if [ ${#MISSING[@]} -gt 0 ]; then
	line "Per avviare il sito in locale manca:"
	for m in "${MISSING[@]}"; do echo "    - $(label "$m")"; done
	if CMD="$(install_command)" && [ -t 0 ]; then
		printf '\n  Lo installo adesso con:\n    %s\n  Procedo? [S/n] ' "$CMD"
		read -r answer
		case "${answer:-s}" in
			[sSyY]*) eval "$CMD" ;;
			*) exit 1 ;;
		esac
		missing
	fi
	if [ ${#MISSING[@]} -gt 0 ]; then
		case "$OS" in
			Windows) line "Su Windows usa avvia-windows.cmd, che installa ciò che manca; a mano: README, «Windows (Git Bash)»." ;;
			Darwin) line "Su Mac: Homebrew (https://brew.sh), poi: brew install php python git" ;;
			*) line "Installa i pacchetti indicati sopra con il gestore della tua distribuzione, poi rilancia." ;;
		esac
		exit 1
	fi
fi

# ---------- WordPress, the first time ----------

if [ "$NEED_SETUP" = 1 ]; then
	line "Preparo WordPress in locale (la prima volta qualche minuto: scarica WordPress e i contenuti del sito)..."
	"$ROOT/dev/setup.sh"
fi

# ---------- Server ----------

free() { php -r '$s = @stream_socket_server("tcp://" . $argv[1] . ":" . $argv[2], $e, $m); if ($s) { fclose($s); exit(0); } exit(1);' "$HOST" "$1" 2> /dev/null; }
ours() { php -r '$h = @file_get_contents("http://127.0.0.1:" . $argv[1] . "/", false, stream_context_create(["http" => ["timeout" => 2]])); exit($h !== false && str_contains($h, "themes/its-rossellini/") ? 0 : 1);' "$1" 2> /dev/null; }

open_browser() {
	case "$OS" in
		Darwin) open "$1" ;;
		Windows) start "" "$1" 2> /dev/null || explorer.exe "$1" || true ;;
		*) if command -v xdg-open > /dev/null 2>&1; then xdg-open "$1" > /dev/null 2>&1 || true; fi ;;
	esac
}

PORT=""
for candidate in "${PORT_START:-8080}" 8081 8082 8090 8888 8899 9080; do
	if free "$candidate"; then
		PORT="$candidate"
		break
	fi
	# Already running from another window: just open it.
	if ours "$candidate"; then
		line "Il sito locale è già acceso: http://localhost:$candidate"
		open_browser "http://localhost:$candidate"
		exit 0
	fi
done
if [ -z "$PORT" ]; then
	line "Nessuna porta libera tra 8080 e 9080. Chiudi i programmi che le usano, oppure: PORT_START=9500 dev/avvia.sh"
	exit 1
fi

URL="http://localhost:$PORT"
line "Sito:   $URL"
echo "  Admin:  $URL/wp-admin   (utente admin, password admin: valgono solo in locale)"
if [ "$HOST" = 0.0.0.0 ]; then
	echo "  In rete locale: l'indirizzo per il telefono è qui sotto. Anche l'admin è raggiungibile: solo su reti fidate."
fi
echo "  Per fermarlo: chiudi questa finestra, oppure Ctrl+C."
echo

# The browser opens as soon as the server answers.
(
	for _ in $(seq 1 60); do
		if php -r '$c = @fsockopen("127.0.0.1", (int) $argv[1], $e, $m, 0.5); exit($c ? 0 : 1);' "$PORT" 2> /dev/null; then
			open_browser "$URL"
			exit 0
		fi
		sleep 0.5
	done
) &

HOST="$HOST" PORT="$PORT" exec "$ROOT/dev/serve.sh"
