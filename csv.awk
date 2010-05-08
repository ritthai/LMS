#!/usr/bin/awk

BEGIN {
FS="\n"
}

{ printf("\"%s\",", $1) }

