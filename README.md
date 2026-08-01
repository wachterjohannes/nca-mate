# Symfony Mate — Never Code Alone

Begleitmaterial zum Livestream mit **Roland Golla** am **04.08.2026, 14:00** —
[youtube.com/live/RgXZ54O5FYs](https://www.youtube.com/live/RgXZ54O5FYs)

Destillat des Talks von der **SymfonyLive Berlin (23.04.2026)**, zugeschnitten auf
die Themen des Streams: die Bausteine, der konkrete Fall, die Wissensschicht darüber —
und die zwei Fragen, die im Berliner Deck noch nicht drin waren.

> KI-Assistenten fehlt keine Fähigkeit. Ihnen fehlt Kontext im richtigen Moment.
> Sie lesen deinen Code, aber sie sehen nie, was deine Anwendung tatsächlich tut.

## Was in diesem Repo liegt

```
slides.md      # Destillat-Deck, 18 Slides (Marp)
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

## Zwei Stellen, an denen der Stream über Berlin hinausgeht

Das ist der Grund, warum dies eine Destillation und keine Wiederholung ist.

**Skills.** Im Berliner Deck kommen sie nicht vor. Der Gedanke dahinter ist aber die
direkte Fortsetzung von `INSTRUCTIONS.md`: ein Werkzeug zu *haben* heißt nicht zu
wissen, *wann* man es einsetzt. Die Beschreibung eines Skills ist immer im Kontext,
ein paar Zeilen; die Anleitung wird erst geladen, wenn die Beschreibung trifft.

**MCP oder CLI.** In Berlin steht dazu eine Slide mit einer klaren Antwort:
*„Why MCP, Not Just a CLI?"* — weil die CLI den Agenten raten lässt, er kennt
Parameter und Ausgabeformat nicht.

Genau diese Lücke schließen Skills. Damit ist die Transportfrage nicht mehr
selbstverständlich beantwortet, und die Slide von damals ist heute eine offene Frage.
Das ehrlich zu zeigen — inklusive der eigenen Kehrtwende — ist stärker als es
wegzulassen.

## Die Demo

Eine Symfony-Blog-App, 100 Posts mit Kommentaren. `findAll()` im Controller,
`post.comments` im Template — Doctrine lädt lazy nach. Macht **101 Queries** für eine
Seite. Der Fall aus dem Talk, unverändert.

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
php bin/console doctrine:database:create
php bin/console doctrine:schema:create
php bin/console doctrine:fixtures:load --no-interaction

symfony server:start --port=8111
```

Die App startet **ohne** Mate — das ist Absicht, der Kontrast ist der Inhalt.

### Durchgang 1 — ohne Mate

Neue Assistenten-Session im `demo/`-Verzeichnis, dann:

> Die Seite ist langsam. Finde das Performance-Problem.

Zuschauen, was er liest. Das ist die Hälfte der Vorführung.

### Durchgang 2 — mit Mate

```bash
composer require --dev symfony/ai-mate symfony/ai-symfony-mate-extension
vendor/bin/mate init
vendor/bin/mate discover
```

Dann eine **neue** Session starten — der MCP-Server wird beim Sessionstart
eingelesen, eine laufende Session sieht ihn nicht. Dieselbe Frage stellen.

### Vorher zurücksetzen

```bash
cd demo && git checkout . && rm -rf mate/ .mcp.json vendor/symfony/ai-mate*
```

## Slides rendern

```bash
npx @marp-team/marp-cli slides.md -o index.html   # oder --pdf
```

---

Alle Links: [RESOURCES.md](RESOURCES.md) · Vollständiger Berliner Talk mit allen
36 Slides: [symfony-mate-berlin](https://github.com/wachterjohannes/symfony-mate-berlin)
