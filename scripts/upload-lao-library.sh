#!/bin/sh
# Publish storage/tts-library to the `tts-library` branch of the Space repo (over the hf-ssh remote).
# The Space builds from `main` only, so the audio never bloats deploys; the app reads it from
#   https://huggingface.co/spaces/buddhaword/web/resolve/tts-library/<path>
# Re-run after every scripts/generate-lao-library.php run — it commits only what changed.
set -e

ROOT=$(cd "$(dirname "$0")/.." && pwd)
SRC="$ROOT/storage/tts-library"
REMOTE=${REMOTE:-hf-ssh}
BRANCH=tts-library
WT="$ROOT/storage/tmp/tts-library-worktree"

[ -f "$SRC/index.json" ] || { echo "No $SRC/index.json — run scripts/generate-lao-library.php first"; exit 1; }
cd "$ROOT"

git worktree remove --force "$WT" 2>/dev/null || true
if git fetch "$REMOTE" "$BRANCH" 2>/dev/null; then
    git worktree add --force -B "$BRANCH" "$WT" FETCH_HEAD
else
    git worktree add --orphan -b "$BRANCH" "$WT"
fi

# mp3 files must go through Git LFS (the Hub rejects plain binary files)
printf '*.mp3 filter=lfs diff=lfs merge=lfs -text\n' > "$WT/.gitattributes"
mkdir -p "$WT/lo"
rsync -a --delete "$SRC/lo/" "$WT/lo/"
cp "$SRC/index.json" "$WT/index.json"

cd "$WT"
git add -A
if git diff --cached --quiet; then
    echo "Library already up to date on $REMOTE/$BRANCH"
else
    git commit -q -m "Lao TTS library: $(ls lo/*.mp3 | wc -l | tr -d ' ') chunks (Gemini Charon)"
    git push "$REMOTE" "$BRANCH:$BRANCH"
fi

cd "$ROOT"
git worktree remove --force "$WT"
