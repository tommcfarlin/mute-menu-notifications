#!/usr/bin/env bash
#
# Creates or updates GitHub labels from .github/labels.yml.
# No external dependencies (no yq). Auto-detects repo from git remote.
#
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
LABELS_FILE="$SCRIPT_DIR/../.github/labels.yml"

if [ ! -f "$LABELS_FILE" ]; then
	echo "Error: $LABELS_FILE not found." >&2
	exit 1
fi

if ! command -v gh &>/dev/null; then
	echo "Error: GitHub CLI (gh) is required." >&2
	exit 1
fi

REPO=$(gh repo view --json nameWithOwner -q '.nameWithOwner' 2>/dev/null)
if [ -z "$REPO" ]; then
	echo "Error: Could not detect repository. Run from within a git repo with a GitHub remote." >&2
	exit 1
fi

echo "Setting up labels for $REPO..."

name=""
color=""
description=""

while IFS= read -r line || [ -n "$line" ]; do
	# Skip comments and blank lines.
	[[ "$line" =~ ^[[:space:]]*# ]] && continue
	[[ "$line" =~ ^[[:space:]]*$ ]] && continue

	if [[ "$line" =~ ^-\ name:\ *\"(.+)\" ]]; then
		# If we have a pending label, create it first.
		if [ -n "$name" ] && [ -n "$color" ]; then
			gh label create "$name" --color "$color" --description "$description" --repo "$REPO" --force
		fi
		name="${BASH_REMATCH[1]}"
		color=""
		description=""
	elif [[ "$line" =~ color:\ *\"(.+)\" ]]; then
		color="${BASH_REMATCH[1]}"
	elif [[ "$line" =~ description:\ *\"(.+)\" ]]; then
		description="${BASH_REMATCH[1]}"
	fi
done <"$LABELS_FILE"

# Create the last label.
if [ -n "$name" ] && [ -n "$color" ]; then
	gh label create "$name" --color "$color" --description "$description" --repo "$REPO" --force
fi

echo "Done."
