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

                $modelFile = "banner.png";
                $alt = "Ma veille juridique";
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

                <?php if (! empty($date)) : ?>
                    <span class="" style="text-transform: uppercase;"><?php echo 'QUESTION DU ' . sprintf(Config::$mailBodyQuestionStatusChange['date'],  $date ); ?></span>
                <?php endif ?>
                <br/>
                <?php if (! empty($numero_question)) : ?>
                    <span class="" style="text-transform: uppercase;"><?php echo 'N° ' . sprintf(Config::$mailBodyQuestionStatusChange['numero_question'],  $numero_question ); ?></span>
                <?php endif ?>


                <?php if (! empty($matiere)) : ?>
                    <table>
                        <tr>
                            <td width="90" style="width:90px;height:90px;">
                                <img src="<?php echo $matiere->picto ?>" alt="icon" width="90" height="90" style="width:90px;height:90px;" /><br/>
                                <span class="section"><?php echo sprintf(Config::$mailBodyQuestionStatusChange['matiere'],  $matiere->label ); ?></span>
                            </td>
                            <br/>

                            <td style="width:400px;" width="400">
                                <h1> <?php echo sprintf(Config::$mailBodyQuestionStatusChange['resume'],  $resume )?>
                                </h1>
                            </td>
                            <br />
                        </tr>
                    </table>
                        <?php if (! empty($competence)) : ?>
                            <p><?php echo '> '.sprintf(Config::$mailBodyQuestionStatusChange['competence'],  $competence ) ?></p>
                        <?php endif ?>
                <?php else : ?>
                    <h1><?php echo sprintf(Config::$mailBodyQuestionStatusChange['resume'],  $resume )?>
                    </h1>
                    <?php if (! empty($competence)) : ?>
                        <?php echo '> '.sprintf(Config::$mailBodyQuestionStatusChange['competence'],  $competence ) ?>
                    <?php endif ?>
                <?php endif; ?>
                <br/>

                <?php if (!empty($content)) : ?>
                <h2>
                    <?php echo sprintf(Config::$mailBodyQuestionStatusChange['content'],  $content )?>
                </h2>
                <?php endif ?>

                <?php if (! empty($support)) : ?>
                    <?php echo 'Délai '.sprintf(Config::$mailBodyQuestionStatusChange['support'],  $support) ?>
                <?php endif; ?>
                <br />
                <?php if (! empty($juriste)) : ?>
                    <?php echo 'En cours de traitement par '.sprintf(Config::$mailBodyQuestionStatusChange['juriste'],  $juriste) ?>
                <?php endif; ?>


                <br/>
                <?php if (! empty($wish_date)) : ?>
                    <?php echo 'Réponse estimée le '.sprintf(Config::$mailBodyQuestionStatusChange['wish_date'],  $wish_date ) ?>
                <?php endif; ?>
                <br/>

                <?php if ($type_question == 1) { echo sprintf(Config::$mailContentQuestionStatusChange['1'],$creation_date,$support);}
                      if (in_array($type_question, array(2, 3))) { echo sprintf(Config::$mailContentQuestionStatusChange[$type_question],$numero_question,$affectation_date,$support);}
                      if ($type_question == 4) { echo sprintf(Config::$mailContentQuestionStatusChange[$type_question],$numero_question,$support,$affectation_date,$juriste,$wish_date);}
                      if (in_array($type_question, array(5, 6))) { echo sprintf(Config::$mailContentQuestionStatusChange[$type_question],$numero_question,$support,$affectation_date);}?>

                <br/>
                <br/>
                <?php if ($type_question !== 6) : ?>
                    <?php echo 'La question est disponible sur votre espace privée : ' ?>
                <?php endif ?>
                <?php if (!empty($notaire->id)) : ?>
                    <a href="<?php echo $home ?>/notaires/<?php echo $notaire->id ?>/questions">Lire sur site</a>
                <?php else : ?>
                    <a href="<?php echo $home ?>">Lire sur site</a>
                <?php endif ?>
                <p></p>
            <td width="20" style="background-color:#fff;"></td>
        </tr>
        </td>

        <tr>
            <td colspan="3" width="600" height="30" style="background-color:#fff"></td>
        </tr>



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