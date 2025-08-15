#!/bin/bash
#enter input encoding here
FROM_ENCODING="WINDOWS-1251"
#output encoding(UTF-8)
TO_ENCODING="UTF-8"
#convert
CONVERT=" iconv  -f   $FROM_ENCODING  -t   $TO_ENCODING"
#loop to convert multiple files 
#for  file  in  *.txt; do
     $CONVERT   "$1"   -o  "$1"
#echo $1
#done
exit 0