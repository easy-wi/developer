#!/bin/bash
###########################################################
#                                                         #
#       =========================================         #
#       |       DNW Despe Networks              |         #
#       =========================================         #
#       |>>>>>>>> ManiaPlanet Startscript v3 >>>|         #
#       |>>>>>>>> http://www.despe.de >>>>>>>>>>|         #
#       |>>>>>>>> DO NOT EDIT, only if u know >>|         #
#       |>>>>>>>> what are you doing! >>>>>>>>>>|         #
#       =========================================         #
#                                                         #
###########################################################

#########################################################################
# DONT EDIT BELOW THIS LINE!!! Broken Server is the reason !!!          #
#########################################################################


# Start ManiaPlanet-Server for TrackMania or ShootMania and start a ServerController


if [ "$5" = "/xaseco" ]
		then
			./XAseco2.sh restart & ./ManiaPlanetServer $1 $2 $3 $4 $6 $7

	elif [ "$5" != "/xaseco" ]
		then
			./ManiaPlanetServer $1 $2 $3 $4 $5 $6
fi
