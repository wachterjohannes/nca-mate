# Symfony Mate — Never Code Alone

Companion material for the livestream with **Roland Golla** on **2026-08-04, 14:00** —
[youtube.com/live/RgXZ54O5FYs](https://www.youtube.com/live/RgXZ54O5FYs)

Distilled from the talk at **SymfonyLive Berlin (2026-04-23)**, cut down to the topics
of the stream: the building blocks, the concrete case, the knowledge layer on top —
and the two spots that were not in the Berlin deck yet.

> AI assistants are not missing a capability. They are missing context at the right moment.
> They read your code, but they never see what your application actually does.

## What is in this repo

```
slides.md      # distilled deck, 18 slides + 2 backup slides (Marp)
theme/         # the theme from the Berlin deck, recovered from the HTML
assets/        # graphics from the Berlin deck
demo/          # the blog app with the N+1 — 101 queries, live to follow along
RESOURCES.md   # every link from the stream
```

## The arc

| # | Topic | Slide |
|---|---|---|
| 1 | Why the agent does not see your behaviour, only your code | The blind spot |
| 2 | What happens when the AI has to guess | Why guessing does not scale |
| 3 | What Mate is — and why it has no business in production | What Mate is |
| 4 | Tools and resources: the building blocks | The building blocks |
| 5 | **Profiler, container, logs on a concrete case** | LIVE — the N+1 |
| 6 | Your own extensions and the MatesOfMate collection | Your own extensions |
| 7 | Skills: description always there, instructions only on a match | The knowledge layer |
| 8 | Context as a budget | Context is a budget |
| 9 | Redaction: what the agent must not see | Redaction |
| 10 | What is next: skills as a specification, MCP or CLI | What is next |

## The demo

A Symfony blog app, 100 posts with comments. `findAll()` in the controller,
`post.comments` in the template — Doctrine lazy-loads the rest. That makes **101 queries**
for one page.

The point is **not** that a good assistant cannot find the N+1 without Mate.
It can, the example is small enough. The point is **how** it gets there:
reading half a dozen files and reconstructing the application from them — or
looking into the profiler once.

### Setup

```bash
cd demo
composer install

# Load the fixtures BEFORE starting the server, otherwise the page is empty
# and there is no N+1 to find.
#
# No doctrine:database:create — current Doctrine versions cannot do that for
# SQLite any more, the command fails. schema:create creates the file itself.
php bin/console doctrine:schema:create
php bin/console doctrine:fixtures:load --no-interaction

symfony server:start --port=8111
```

Last verified on 2026-08-02 with PHP 8.5: the page runs **101 queries**.
The first request after starting the server takes about 750 ms (cache warmup), after
that about 70 ms — the number that matters is the **101**.

The app starts **without** Mate — that is deliberate, the contrast is the content.

### Run 1 — without Mate

Important: **start without foreign MCP servers.** If you have a database, filesystem
or Sentry server configured globally, you measure its effect as well — and half the
demo is about showing what an agent does *without* tools.

```bash
cd demo
claude --strict-mcp-config
```

`--strict-mcp-config` ignores everything configured as an MCP server in the user or
project configuration. Then the question:

> The page is slow. Find the performance problem.

Watch what it reads. That is half the demo.

<details>
<summary>Even stricter — without personal skills and settings as well</summary>

`--strict-mcp-config` only turns off MCP. Your own skills, `~/.claude/CLAUDE.md` and
settings still apply. For a truly bare agent you need a separate configuration
directory — including the credentials, otherwise you end up at the login:

```bash
export CLAUDE_CONFIG_DIR=$(mktemp -d)
cp ~/.claude/.credentials.json "$CLAUDE_CONFIG_DIR/"
claude --strict-mcp-config
```

</details>

### Run 2 — with Mate

```bash
composer require --dev symfony/ai-mate symfony/ai-symfony-mate-extension
vendor/bin/mate init
vendor/bin/mate discover
```

Then start a **new** session — the MCP server is read at session start, a running
session does not see it. Strict again, but this time with exactly *one*
allowed configuration:

```bash
claude --strict-mcp-config --mcp-config mcp.json
```

That way Mate is the only difference between the two runs. Ask the same question.

### If something goes wrong live

The last two slides in the deck are backup: the recorded transcripts of both
runs from the rehearsal (2026-08-02, Fable 5).

### Resetting beforehand

```bash
cd demo && git checkout . && git clean -fd . && composer install
```

`git clean` removes what `mate init` leaves behind (`mate/`, `mcp.json`, `.agents/`
and others), `composer install` brings `vendor/` back to the state of the lock file.

## What is new compared to Berlin

If you know the Berlin talk, there are two spots here that were not in it.

**Skills.** They do not appear in the Berlin deck. *Having* a tool does not mean
knowing *when* to use it — the description of a skill is always in context, a few
lines; the instructions are only loaded once the description matches.

**MCP or CLI.** In Berlin there was a slide with a clear answer:
*“Why MCP, Not Just a CLI?”* — because a CLI makes the agent guess, it does not know
the parameters or the output format. That is exactly the gap skills close.
Which makes the question from back then an open one again.

## Rendering the slides

```bash
# Mind the order: --theme-set is an array option and would swallow
# an input file placed after it.
marp slides.md --theme-set theme/ -o index.html --allow-local-files
```

---

All links: [RESOURCES.md](RESOURCES.md) · Full Berlin talk with all
36 slides: [symfony-mate-berlin](https://github.com/wachterjohannes/symfony-mate-berlin)
