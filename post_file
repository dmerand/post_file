#!/bin/sh

# Use a DELETE if there was a command line parameter for it
DELETE=0
if [ "$1" = "-d" ]; then
  DELETE=1
  shift
fi

# This variable represents the file passed from the command line
FILE="$1"
# Authentication token for posting to the site
TOKEN="ba38a82e75d4faf026822455026df469"
# Remote URL
URL="http://embed.donaldmerand.com/post_file/post_file.php"
# Local debugging
#URL="http://localhost/post_file/post_file.php"
USER="postmaster"
PASS="senditup"

# Display how to use the program
usage() {
  cat <<HOWTO
post_file.sh

Posts a file to embed.donaldmerand.com

Usage: \`post_file.sh <file_name>\`

Options:
  -d – delete a folder that already exists
HOWTO
}

# If a file is not passed, then display usage and exit
if [ ! -f "$FILE" -a $DELETE = 0 ]; then
  usage
  exit 1
fi


# If we got to this point, we either have a file to post, or a file to delete. Send it up
if [ $DELETE = 0 ]; then
  RESULT=$(curl -u "$USER:$PASS" -sF file=@"$FILE" -F token="$TOKEN" "$URL")
else
  #delete
  RESULT=$(curl -u "$USER:$PASS" -sX DELETE -d dir="$FILE" -d token="$TOKEN" "$URL")
fi

# Display the result on the command line
echo "$RESULT"

# Copy the result to the clipboard if pbcopy is available
which pbcopy &>/dev/null && echo "$RESULT" | pbcopy
