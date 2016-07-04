<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Cridon</title>


    <!--[if !mso]><!-- -->
    <link href='https://fonts.googleapis.com/css?family=Dosis' rel='stylesheet' type='text/css'>
    <!--<![endif]-->


    <style type="text/css">
        <!--

        @import url('https://fonts.googleapis.com/css?family=Dosis');

        body {
            background-color: #e6e6e6;
            color:#2e4867;
        }

        table, div {
            font-family:Arial, Helvetica, sans-serif;
            font-size:12px;
        }

        h1 {color:#009999; font-weight:normal; font-size:28px; text-transform:uppercase; line-height:25px; margin:15px 0px 5px 0px; padding:0px; font-family:'Dosis', Arial, Helvetica, sans-serif;}
        h2 {font-size: 20px; line-height: 18px; text-transform: uppercase;  margin:10px 0px; color:#2e4867; font-weight:normal; padding:0px; font-family:'Dosis', Arial, Helvetica, sans-serif;}
        h3 {font-size: 20px; line-height: 18px; text-transform: uppercase; margin:10px 0px 2px 20px; color:#009999; font-weight:normal; padding:0px; font-family: 'Dosis', Arial, Helvetica, sans-serif;}

        a {
            cursor:pointer;
            color:#b9003f;
            text-decoration:underline;
            border:none;
        }

        .footerlinks a{color:#fff; text-decoration:none;}
        .cridon_footer{font-weight:bold; font-size:16px; font-family:Dosis, Arial, Helvetica, sans-serif;}

        p{margin:0px; padding:0px}

        .s{background-color:#ffd500; text-decoration: none;}
        .introduction{color:#b9003f; font-size:16px; line-height:22px; font-weight:normal;}
        .couleur2{color:#009999;margin-left:20px;}
        .section{background-color:#009999; color:#fff; text-transform:uppercase; font-family:'Dosis', Arial, Helvetica, sans-serif; padding:0px 3px;}
        .newsletter_date{color:#009999; font-size:28px; text-transform:uppercase; font-family:'Dosis', Arial, Helvetica, sans-serif;}


        -->
    </style>
</head>
<body>
<div align="center" style="width:100%; background-color:#e6e6e6; padding:15px 0px; color:#2e4867;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" style="background-color:#fff; color:#2e4867; margin:auto; line-height:18px;" width="600">
        <tbody>

        <tr>
            <td width="600" height="100" colspan="3" valign="bottom" style="background-color:#fff; line-height:0px; text-align:left"><a href="http://www.cridon-lyon.fr/" target="_blank"><img src="<?php echo plugins_url("../public/images/mail/cridon_logo.png", dirname(__FILE__)) ?>" height="100" width="264" alt="Cridon Lyon, partenaire expert du notaire" style="border:none" /></a></td>
        </tr>

        <tr>
            <td colspan="3" width="600" style="background-color:#fff">
                <?php
                $home = home_url();

                $modelFile = "banner-question.png";
                $alt = "Poser une question";
                ?>
                <img src="<?php echo plugins_url( "../public/images/mail/".$modelFile, dirname(__FILE__) ) ?>" alt="<?php echo $alt ; ?>" />
            </td>
        </tr>

        <tr>
            <td colspan="3" width="600" height="30" style="background-color:#fff"></td>
        </tr>

        <tr>
            <td width="20" style="background-color:#fff;"><?php //var_dump($post) ?></td>
            <td width="560" style="background-color:#fff; text-align:left; color:#2e4867; font-size:14px;">


                <!-- Mail final ! -->

                <?php if ($type_question == 1) : ?> <p><h2> <?php echo sprintf(Config::$mailContentQuestionStatusChange[$type_question],$creation_date,$expertise,$support); ?> </h2></p> <?php endif; ?>
                <?php if ($type_question == 2) : ?> <p><h2> <?php echo sprintf(Config::$mailContentQuestionStatusChange[$type_question],$numero_question,$affectation_date,$expertise,$support); ?> </h2></p> <?php endif; ?>
                <?php if ($type_question == 3) : ?> <p><h3> <?php echo sprintf(Config::$mailContentQuestionStatusChange[$type_question][0],$numero_question,$affectation_date); ?> </h3></p>
                                                    <p><h2> <?php echo sprintf(Config::$mailContentQuestionStatusChange[$type_question][1],$support); ?> </h2></p><?php endif; ?>
                <?php if ($type_question == 4) : ?> <p><h2> <?php echo sprintf(Config::$mailContentQuestionStatusChange[$type_question][0],$numero_question,$expertise,$support,$affectation_date,$juriste); ?> </h2></p>
                                                    <p><h3> <?php echo sprintf(Config::$mailContentQuestionStatusChange[$type_question][1],$wish_date); ?> </h3></p> <?php endif; ?>
                <?php if ($type_question == 5) : ?> <p><h3> <?php echo sprintf(Config::$mailContentQuestionStatusChange[$type_question],$numero_question,$expertise,$support,$affectation_date); ?> </h3></p> <?php endif; ?>
                <?php if ($type_question == 6) : ?> <p><h2> <?php echo sprintf(Config::$mailContentQuestionStatusChange[$type_question],$numero_question,$expertise,$support,$affectation_date); ?> </h2></p> <?php endif; ?>

                <!-- Titre de la question -> $resume -->
                <p><strong><?php echo sprintf(Config::$mailBodyQuestionStatusChange['resume'], $resume ) ?></strong></p>
                <br />

                <!-- Matière -> $matiere-->
                <span class="section"><?php echo sprintf(Config::$mailBodyQuestionStatusChange['matiere'],  $matiere->label ); ?></span>
                <!-- Compétence -> $competence-->
                <p> <?php echo empty($competence) ? '' : '> '.sprintf(Config::$mailBodyQuestionStatusChange['competence'],  $competence ) ?></p>

                <?php if ($type_question == 5) : ?>
                    <br />
                    <p><strong><?php echo 'En cours de traitement par : ' . sprintf(Config::$mailBodyQuestionStatusChange['juriste'], $juriste); ?></strong></p>
                <?php endif; ?>
                <?php if ($type_question == 6) : ?>
                    <br />
                    <p><strong><?php echo 'Traité par : ' . sprintf(Config::$mailBodyQuestionStatusChange['juriste'], $juriste); ?></strong></p>
                <?php endif; ?>
                <?php if (in_array($type_question,array(2,3,5))) : ?>
                      <p><h2><?php echo 'Réponse souhaitée le ' . sprintf(Config::$mailBodyQuestionStatusChange['wish_date'], $wish_date);?></h2></p>
                <?php endif; ?>

                <br />
                <p>
                    <?php echo sprintf(Config::$mailBodyQuestionStatusChange['content'],  $content )?>
                </p>

            </td>
            <td width="20" style="background-color:#fff;"></td>
        </tr>

        <tr>
            <td colspan="3" width="600" height="30" style="background-color:#fff"></td>
        </tr>

        <?php if (isFaxAccepted()): ?>
            <tr>
                <td colspan="3" width="600" height="30" style="background-color:#ccc"></td>
            </tr>

            <tr>
                <td width="20" style="background-color:#ccc;"><?php //var_dump($post) ?></td>
                <td width="560" style="background-color:#ccc; text-align:left; color:#2e4867; font-size:11px; line-height: 16px;">


                    <h2><center>SéCURITE - OPTIMISATION DE VOTRE TEMPS - MOBILITé – DIGITAL</center></h2>

                    <p> Nous vous rappelons que depuis le <u>18 janvier</u> vous pouvez poser vos questions écrites via votre site extranet <a href="http://www.cridon-lyon.fr" target="_blank">www.cridon-lyon.fr</A>, ce, quel que soit votre support, PC, tablette ou votre smartphone. <br />
                    <br />
                    Ce faisant, vous :<br />
                    ‐   faites l’économie d’une lettre d’accompagnement,<br />
                    ‐   êtes certain qu’elle nous est bien parvenue car vous recevez un accusé réception automatique,<br />
                    ‐   gagnez du temps,<br />
                    ‐   pouvez joindre jusqu’à 5 pièces, <br />
                    ‐   trouvez toutes vos consultations en cours de traitement sur le site « fonction » suivre mon compte,<br />
                    ‐   recevez en toute sécurité votre consultation sur votre espace privé après réception d’un avis d’envoi,<br />
                    ‐   visualisez votre consultation sur votre support, PC, tablette ou votre smartphone où que vous soyez. <br />
                    </p>
                    <br />
                    <p><u><center>Les fax ne seront plus traités dès le 1er janvier 2017</center></u></p>



                </td>
                <td width="20" style="background-color:#ccc;"></td>
            </tr>

            <tr>
                <td colspan="3" width="600" height="30" style="background-color:#ccc"></td>
            </tr>
        <?php endif; ?>


        <tr>
            <td colspan="3" width="600" style="background-color:#15283f; line-height:0px; text-align:left"><a href="http://www.cridon-lyon.fr/" target="_blank"><img src="<?php echo plugins_url("../public/images/mail/cridon_footer.png", dirname(__FILE__)) ?>" width="265" height="120" alt="Cridon Lyon, partenaire expert du notaire" style="border:none" /></a></td>
        </tr>

        <tr>
            <td width="20" style="background-color:#15283f;"></td>
            <td width="560" height="1" style="background-color:#15283f; border-bottom:1px solid #445365"></td>
            <td width="20" style="background-color:#15283f;"></td>
        </tr>

        <tr>
            <td colspan="3" width="600" height="30" style="background-color:#15283f"></td>
        </tr>

        <tr>
            <td width="20"  style="background-color:#15283f"></td>
            <td width="560" height="30" style="background-color:#15283f; color:#fff; text-align:left"><span class="cridon_footer">CRIDON LYON</span><br/>
                37 Bd des Brotteaux - 69455 LYON CEDEX 06</td>
            <td width="20"  style="background-color:#15283f"></td>
        </tr>

        <tr>
            <td width="20"  style="background-color:#15283f"></td>
            <td width="560" height="30" style="background-color:#15283f; color:#cdced2; text-align:left">Consultation téléphonique - de 14h00 à 17h30 du Lundi au Vendredi<br/>
                04 37 24 79 24</td>
            <td width="20"  style="background-color:#15283f"></td>
        </tr>






        <tr>
            <td colspan="3" width="600" style="background-color:#15283f; line-height:0px" valign="bottom"><img src="<?php echo plugins_url("../public/images/mail/arrow.png", dirname(__FILE__)) ?>" alt="--" /></td>
        </tr>


        <tr>
            <td colspan="3" width="600" height="15" style="background-color:#0b1828"></td>
        </tr>

        <tr>
            <td width="20" style="background-color:#0b1828;"></td>
            <td width="560" height="1" style="background-color:#0b1828; color:#fff; text-align:left" class="footerlinks"><a href="http://www.cridon-lyon.fr/" target="_blank">www.cridonlyon.fr</a></td>
            <td width="20" style="background-color:#0b1828;"></td>
        </tr>

        <tr>
            <td colspan="3" width="600" height="15" style="background-color:#0b1828"></td>
        </tr>



        </tbody>
    </table>


</div>
</body>
</html>