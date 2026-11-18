#!/usr/bin/env bash
# Linux: avvia il sito in locale (il lavoro lo fa dev/avvia.sh). Da terminale: ./avvia-linux.sh
cd "$(dirname "$0")" || exit 1
bash dev/avvia.sh "$@"
status=$?
if [ "$status" -ne 0 ] && [ "$status" -ne 130 ]; then
	echo
	read -r -p "  Il sito non è partito: leggi il messaggio qui sopra. Invio per chiudere. " _
fi
exit "$status"
