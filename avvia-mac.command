#!/usr/bin/env bash
# Mac: doppio clic per avviare il sito in locale (il lavoro lo fa dev/avvia.sh).
# La prima volta macOS può chiedere conferma: tasto destro sul file → Apri.
cd "$(dirname "$0")" || exit 1
bash dev/avvia.sh "$@"
status=$?
if [ "$status" -ne 0 ] && [ "$status" -ne 130 ]; then
	echo
	read -r -p "  Il sito non è partito: leggi il messaggio qui sopra. Invio per chiudere. " _
fi
exit "$status"
