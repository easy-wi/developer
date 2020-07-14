<section class="content-header">
    <h1><?php echo $gsprache->support;?></h1>
    <ol class="breadcrumb">
        <li><a href="admin.php"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="admin.php?w=ti"><i class="fa fa-life-ring"></i> <?php echo $gsprache->support;?></a></li>
        <li class="active"><?php echo $topic;?></li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-body">
                    <dl class="dl-horizontal">
                        <dt><?php echo $sprache->status;?></dt>
                        <dd><?php echo $status;?></dd>
                        <br>
                        <dt><?php echo $sprache->priority;?></dt>
                        <dd><?php echo $priority;?></dd>
                        <br>
                        <dt><?php echo $gsprache->user.' '.$sprache->priority;?></dt>
                        <dd><?php echo $userPriority;?></dd>
                        <br>
                        <dt><?php echo $sprache->edit2;?></dt>
                        <dd><?php if(isset($supporterList[$supporter])) echo $supporterList[$supporter];?></dd>
                        <br>
                        <?php if($open=="Y") { ?>
                        <dt><?php echo $gsprache->mod;?></dt>
                        <dd><a href="admin.php?w=ti&d=md&amp;id=<?php echo $id;?>&amp;action=md"><span class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a></dd>
                        <?php } ?>
                    </dl>
                </div>
            </div>

            <div class="jumbotron">
  <h2 class="display-4">Häufige Anwtworten</h2>
    <br><h3>Teamspeak</h3><hr><h4>Absagen</h4><hr><br>
    Vielen Dank f&uuml;r dein Antwort.<br /><br />Leider w&uuml;rdet ihr in diesem Fale noch nicht f&uuml;r ein Sponsoring in Frage kommen. Solltet ihr mit eurem jetzigen Voiceserver angebot nicht zufrieden sein, kann ich euch 2 Vorschl&auml;ge geben.<br /><br />1. Ihr hastet euch einen V-Server uns habt dannd ie M&ouml;glichkeit Lizenzfrei einen Teamspeak mit bis zu 32 Slots zu hosten. Das geht schon ab 3&euro;/Monat.<br /><br />2. Ihr k&ouml;nnt bei uns auf unserem Freien Community Teamspeak unterkommen. Dieser hat stehts gen&uuml;gend Platz und kann auch von anderen Parteien f&uuml;r ihre Clans und Gilden genutzt werden. Teamspeakadresse: glitch
    <br><hr><br>
    Vielen Dank f&uuml;r dein Interesse an ein Sponsoring bei KingsHost.<br />Leider k&ouml;nnen wir dir keinen privaten Teamspeak zur Verf&uuml;gung stellen.<br /><br />Was wir dir aber anbieten k&ouml;nnen, ist es unseren offiziellen Community Teamspeak f&uuml;r dich und deine Freunde zu nutzen.<br />Falls das Interesse an einer administrativen oder moderativen Rolle besteht, kannst du dich diesbz&uuml;glich unter https://board.glitch.community bewerben.<br /><br />Vielen Dank f&uuml;r dein Verst&auml;ndnis.
    <br><hr><br>
    vielen Dank, f&uuml;r dein Interesse.<br /><br />Nach viel &Uuml;berlegung haben wir uns gegen ein Sponsoring des Teamspeaks entschieden.<br />Solltest du in Zukunft aber wieder Hilfe bei einem gr&ouml;&szlig;eren Projekt oder mit einem Konzept ben&ouml;tigen, kannst du gerne wieder einen Teamspeak beantragen.<br /><br />Wir w&uuml;nschen dir viel Erfolg bei der Suche und einen angenehmen Abend.
    <br><br><hr><h3>Gameserver</h3><hr>SOOOOON 




</div>
        </div>

        <div class="col-md-6">
            <ul class="timeline">
                <?php foreach ($table as $table_row) { ?>
                <?php if($lastdate!=$table_row['writedate']){ ?>
                <li class="time-label"><span class="bg-green"><?php echo $table_row['writedate'];?></span></li>
                <?php }; $lastdate=$table_row['writedate'];?>

                <li>
                    <i class="fa fa-envelope bg-blue"></i>
                    <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i> <?php echo $table_row['writeTime'];?></span>
                        <h3 class="timeline-header"><?php echo $sprache->writer.': '.$table_row['writer'];?> ...</h3>
                        <div class="timeline-body">
                            <?php echo $table_row['ticket'];?>
                        </div>
                    </div>
                </li>
                <?php } ?>

                <li>
                    <i class="fa fa-clock-o"></i>
                </li>
            </ul>
        </div>
    </div>
</section>