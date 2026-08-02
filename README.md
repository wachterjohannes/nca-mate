# Symfony Mate — Never Code Alone

Begleitmaterial zum Livestream mit **Roland Golla** am **04.08.2026, 14:00** —
[youtube.com/live/RgXZ54O5FYs](https://www.youtube.com/live/RgXZ54O5FYs)

Destillat des Talks von der **SymfonyLive Berlin (23.04.2026)**, zugeschnitten auf
die Themen des Streams: die Bausteine, der konkrete Fall, die Wissensschicht darüber —
und die zwei Stellen, die im Berliner Deck noch nicht drin waren.

> KI-Assistenten fehlt keine Fähigkeit. Ihnen fehlt Kontext im richtigen Moment.
> Sie lesen deinen Code, aber sie sehen nie, was deine Anwendung tatsächlich tut.

## Was in diesem Repo liegt

```
slides.md      # Destillat-Deck, 18 Slides + 2 Backup-Slides (Marp)
theme/         # Das Theme aus dem Berliner Deck, aus dem HTML zurückgewonnen
assets/        # Grafiken aus dem Berliner Deck
demo/          # Die Blog-App mit dem N+1 — 101 Queries, live zum Mitmachen
RESOURCES.md   # Alle Links aus dem Stream
```

## Der Bogen

| # | Thema | Slide |
|---|---|---|
| 1 | Warum der Agent dein Verhalten nicht sieht, nur deinen Code | Der blinde Fleck |
| 2 | Was passiert, wenn die KI raten muss | Warum Raten nicht skaliert |
| 3 | Was Mate ist — und warum es nichts in Produktion zu suchen hat | Was Mate ist |
| 4 | Tools und Resources: die Bausteine | Die Bausteine |
| 5 | **Profiler, Container, Logs am konkreten Fall** | LIVE — der N+1 |
| 6 | Eigene Extensions und die MatesOfMate-Sammlung | Eigene Extensions |
| 7 | Skills: Beschreibung immer da, Anleitung erst bei Treffer | Die Wissensschicht |
| 8 | Kontext als Budget | Kontext ist ein Budget |
| 9 | Redaction: was der Agent nicht sehen darf | Redaction |
| 10 | Ausblick: Skills als Spezifikation, MCP oder CLI | Ausblick |

## Die Demo

Eine Symfony-Blog-App, 100 Posts mit Kommentaren. `findAll()` im Controller,
`post.comments` im Template — Doctrine lädt lazy nach. Macht **101 Queries** für eine
Seite.

Der Punkt ist **nicht**, dass ein guter Assistent den N+1 ohne Mate nicht findet.
Findet er, das Beispiel ist klein genug. Der Punkt ist, **wie** er dorthin kommt:
ein halbes Dutzend Dateien lesen und daraus die Anwendung rekonstruieren — oder
einmal in den Profiler schauen.

### Setup

```bash
cd demo
composer install

# Fixtures VOR dem Serverstart laden, sonst ist die Seite leer
# und es gibt keinen N+1 zu finden.
#
# Kein doctrine:database:create — das kann SQLite in aktuellen Doctrine-Versionen
# nicht mehr, der Befehl schlägt fehl. schema:create legt die Datei selbst an.
php bin/console doctrine:schema:create
php bin/console doctrine:fixtures:load --no-interaction

symfony server:start --port=8111
```

Zuletzt geprüft am 02.08.2026 mit PHP 8.5: die Seite führt **101 Queries** aus.
Der erste Request nach dem Serverstart braucht rund 750 ms (Cache-Warmup), danach
rund 70 ms — die Zahl, auf die es ankommt, ist die **101**.

Die App startet **ohne** Mate — das ist Absicht, der Kontrast ist der Inhalt.

### Durchgang 1 — ohne Mate

Wichtig: **ohne fremde MCP-Server starten.** Wer global einen Datenbank-, Filesystem-
oder Sentry-Server konfiguriert hat, misst sonst dessen Wirkung mit — und die halbe
Vorführung besteht darin, zu zeigen, was ein Agent *ohne* Werkzeuge tut.

```bash
cd demo
claude --strict-mcp-config
```

`--strict-mcp-config` ignoriert alles, was in der Benutzer- oder Projektkonfiguration
an MCP-Servern steht. Dann die Frage:

> Die Seite ist langsam. Finde das Performance-Problem.

Zuschauen, was er liest. Das ist die Hälfte der Vorführung.

<details>
<summary>Noch strenger — auch ohne persönliche Skills und Einstellungen</summary>

`--strict-mcp-config` schaltet nur MCP ab. Eigene Skills, `~/.claude/CLAUDE.md` und
Einstellungen wirken weiter. Für einen wirklich nackten Agenten braucht es ein eigenes
Konfigurationsverzeichnis — inklusive der Zugangsdaten, sonst steht man vor dem Login:

```bash
export CLAUDE_CONFIG_DIR=$(mktemp -d)
cp ~/.claude/.credentials.json "$CLAUDE_CONFIG_DIR/"
claude --strict-mcp-config
```

</details>

### Durchgang 2 — mit Mate

```bash
composer require --dev symfony/ai-mate symfony/ai-symfony-mate-extension
vendor/bin/mate init
vendor/bin/mate discover
```

Dann eine **neue** Session starten — der MCP-Server wird beim Sessionstart eingelesen,
eine laufende Session sieht ihn nicht. Wieder streng, aber diesmal mit genau *einer*
zugelassenen Konfiguration:

```bash
claude --strict-mcp-config --mcp-config mcp.json
```

So ist Mate der einzige Unterschied zwischen den beiden Durchgängen. Dieselbe Frage
stellen.

### Falls live etwas klemmt

Die letzten beiden Slides im Deck sind Backup: die aufgezeichneten Verläufe beider
Durchgänge aus dem Probelauf (02.08.2026, Fable 5).

### Vorher zurücksetzen

```bash
cd demo && git checkout . && git clean -fd . && composer install
```

`git clean` räumt weg, was `mate init` hinterlässt (`mate/`, `mcp.json`, `.agents/`
u. a.), `composer install` bringt `vendor/` zurück auf den Lock-Stand.

## Was gegenüber Berlin neu ist

Wer den Berliner Talk kennt, findet hier zwei Stellen, die dort noch nicht drin waren.

**Skills.** Im Berliner Deck kommen sie nicht vor. Ein Werkzeug zu *haben* heißt
nicht zu wissen, *wann* man es einsetzt — die Beschreibung eines Skills ist immer
im Kontext, ein paar Zeilen; die Anleitung wird erst geladen, wenn die Beschreibung
trifft.

**MCP oder CLI.** In Berlin stand dazu eine Slide mit einer klaren Antwort:
*„Why MCP, Not Just a CLI?"* — weil die CLI den Agenten raten lässt, er kennt
Parameter und Ausgabeformat nicht. Genau diese Lücke schließen Skills.
Damit ist die Frage von damals heute wieder offen.

## Slides rendern

```bash
# Reihenfolge beachten: --theme-set ist eine Array-Option und würde
# eine danach stehende Eingabedatei verschlucken.
marp slides.md --theme-set theme/ -o index.html --allow-local-files
```

---

Alle Links: [RESOURCES.md](RESOURCES.md) · Vollständiger Berliner Talk mit allen
36 Slides: [symfony-mate-berlin](https://github.com/wachterjohannes/symfony-mate-berlin)
