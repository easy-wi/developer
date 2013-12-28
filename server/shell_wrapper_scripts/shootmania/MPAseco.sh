#!/bin/bash
###########################################################
#                                                         #
#       =========================================         #
#       |       DNW Despe Networks              |         #
#       =========================================         #
#       |>>>>>>>> MPaseco Startscript v2     >>>|         #
#       |>>>>>>>> http://www.despe.de >>>>>>>>>>|         #
#       |>>>>>>>> DO NOT EDIT, only if u know >>|         #
#       |>>>>>>>> what are you doing! >>>>>>>>>>|         #
#       =========================================         #
#  more info: https://github.com/ManiaAseco/MPAseco       #
###########################################################

#########################################################################
# DONT EDIT BELOW THIS LINE!!! Broken Server is the reason !!!          #
#########################################################################

NAME="MPAseco"
PIDFILE="mpaseco.pid"

# 	Absolute path to this script, e.g. /home/user/bin/foo.sh
#SCRIPT=$(readlink -f $0)
# 	Absolute path this script is in, thus /home/user/bin
#SCRIPTPATH=$(dirname $SCRIPT)
#SCRIPTPATH=$(dirname "$(readlink -fn "$0")")
SCRIPTPATH=$PWD/servercontroller
cd $SCRIPTPATH

#########################################################################
# ONLY FOR DNW	-	SUPPORT !!!				        				#
#########################################################################

function start {
	echo "Starting $NAME ..."
		if test -f $SCRIPTPATH/$PIDFILE;
			then
				echo "PID file exists, Restart?"
		elif test $SCRIPTPATH/$PIDFILE;
			then
				echo "PID file not exists..."
				#touch $PIDFILE
			fi
		php mpaseco.php SM </dev/null >mpaseco2.log 2>&1 & echo $! > $SCRIPTPATH/$PIDFILE
		sleep 1
		PID="`cat $SCRIPTPATH/$PIDFILE`"
		echo "...$NAME started. ID $PID"
}
###################

function stop {
	PID="`cat $SCRIPTPATH/$PIDFILE`"
	kill $PID
	echo "$NAME stopped. ID $PID"
	rm -f $SCRIPTPATH/$PIDFILE
	echo "$PIDFILE removed"
}

###################
		
case "$1" in
	start)
		start
	;;

	stop)
		stop
	;;

	restart)
		stop
		sleep 2
		start
	;;
	
	status)
		if [ -f $SCRIPTPATH/$PIDFILE ]; then
			PID="`cat $SCRIPTPATH/$PIDFILE`"
			echo "$NAME is runing! ID $PID . Server offline? Restart?"
			else
			echo 'MPAseco has crashed or is stopped incorrect.'
		fi
	;;



*)
	echo "Usage: $0  {start|stop|restart|status}"
;;

esac
exit 0
