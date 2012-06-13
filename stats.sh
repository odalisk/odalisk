#!/bin/bash


for d in data/*; do
	OK=0
	DENIED=0
	NOTFOUND=0
	TIMEOUT=0
	for f in $d/*; do
		CODE=`cut -c 17-19 $f`
		case $CODE in
		200) let OK=$OK+1;;
		403) let DENIED=$DENIED+1;;
		404) let NOTFOUND=$NOTFOUND+1;;
		*) let TIMEOUT=$TIMEOUT+1;;
		esac
	done
	echo $d
	echo "200 $OK"
	echo "403 $DENIED"
	echo "404 $NOTFOUND"
	echo "Timeout $TIMEOUT"
done
