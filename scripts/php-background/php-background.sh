#!/bin/bash

#Variables auxiliares
PIDS="/var/www/beta.trazeo.es/scripts/php-background/pids.txt";
PROCESSES="/var/www/beta.trazeo.es/scripts/php-background/processes.txt";

touch $PIDS;

#Almacenamos los PIDs de los procesos iniciados en php
echo "$(pgrep php)" > $PIDS;

while read process
do

 find=0;

 while read pid
 do

  if [ "$pid" != "" ]; then
  echo  "Comparamos proceso" $process "con pid: " $pid;
  var1="$(cat /proc/$pid/cmdline)";
  echo "Variable 1" $var1;
  var2="${process//[[:space:]]/}";
  echo -n "Variable 2" $var2;

  if [ "$var1" == "$var2"  ]; then
   find=1;
   echo -e "Proceso" $process  "ejecutandose \n";
   break;
  fi 

  fi

 done < $PIDS;

 if [ "$find" == 0  ]; then
  echo "Ejecutamos el proceso " $process;
  eval "$process &";
 fi

done < $PROCESSES; 
