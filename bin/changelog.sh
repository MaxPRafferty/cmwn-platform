#!/usr/bin/env bash

CURRENT_VERSION=`bin/version_bump.sh --print-current`

# destination of the final changelog file
OUTPUT_FILE="CHANGELOG.md"

# generate the changelog
LAST_DATE=$(git show -s --format=%ad `git rev-list --tags --max-count=1`)

CHANGES=`git --no-pager log  --no-merges --pretty=format:"- %h: %s%b" --since="$LAST_DATE"`

HEADER="Version - $CURRENT_VERSION\n----\n\n"

case "$1" in
    --print )
         echo -e -n "$HEADER$CHANGES\n\n";
         exit 0
         ;;
esac

grep -e "^Version - $CURRENT_VERSION$" $OUTPUT_FILE &> /dev/null
if [ $? = 0 ]
then
    echo "Version $CURRENT_VERSION is already in the file: $OUTPUT_FILE"
    exit 0
fi

if [ ! -f $OUTPUT_FILE ]
then
    echo -e "$HEADER$CHANGES" >> $OUTPUT_FILE
    echo "Wrote changes to $OUTPUT_FILE"
    exit 0
fi

{ echo -e -n "$HEADER$CHANGES\n\n"; cat $OUTPUT_FILE; } > $OUTPUT_FILE.new

mv $OUTPUT_FILE.new $OUTPUT_FILE