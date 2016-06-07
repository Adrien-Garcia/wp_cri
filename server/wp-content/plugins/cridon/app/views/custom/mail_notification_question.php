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

                <?php if (! empty($date)) : ?>
                    <h2><?php echo 'QUESTION DU ' . sprintf(Config::$mailBodyQuestionStatusChange['date'],  $date ); ?><h2>
                <?php endif ?>
                <br/>
                <?php if (! empty($numero_question)) : ?>
                    <span class="" style="text-transform: uppercase;"><?php echo 'N° ' . sprintf(Config::$mailBodyQuestionStatusChange['numero_question'],  $numero_question ); ?></span>
                <?php endif ?>


                <?php if (! empty($matiere)) : ?>
                    <table>
                        <tr>
                            <td width="560" style="width:90px;height:90px;">
                               <!--  <img src="<?php echo $matiere->picto ?>" alt="icon" width="90" height="90" style="width:90px;height:90px;" /><br/> -->
                                <span class="section"><?php echo sprintf(Config::$mailBodyQuestionStatusChange['matiere'],  $matiere->label ); ?></span>
                            </td>
                        </tr>
                        <?php if (! empty($competence)) : ?>
                        <tr>
                            <td>                                 
                                <p><?php echo '> '.sprintf(Config::$mailBodyQuestionStatusChange['competence'],  $competence ) ?></p>
                            </td>
                        </tr>
                        <?php endif ?>
                        <tr>
                            <td style="width:560px;" width="560">
                                <p><strong><?php echo sprintf(Config::$mailBodyQuestionStatusChange['resume'],  $resume )?></strong></p>
                            </td>
                        </tr>
                    </table>                       
                <?php else : ?>
                    <?php if (! empty($competence)) : ?>
                        <?php echo '> '.sprintf(Config::$mailBodyQuestionStatusChange['competence'],  $competence ) ?>
                    <?php endif ?>
                        <p><strong><?php echo sprintf(Config::$mailBodyQuestionStatusChange['resume'],  $resume )?></strong></p>
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


















                <!-- A CABLER :) -->








                <!-- ETAPE 1 ENREGISTREMENT QUESTION -->
                <!-- Objet du mail = Question CRIDON LYON numéro <numéro question> transmise -->

                <p><h2>Votre question N° 123456789 du 12.09.2015 de niveau d'expertise initiale en délai support a bien été transmise.</h2></p>

                <p><strong>Objet de la question</strong></p>
                <br />
                <span class="section">Matière</span>
                <p>>  Compétence</p>
                <br />
                <p>
                    TEXTE DE LA QUESTION Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer euismod, enim a accumsan varius, lectus turpis ultrices lectus, eget blandit nisi leo a libero. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam eget finibus leo. Morbi at nisl tincidunt, convallis ante tristique, aliquam nunc. Nam sit amet cursus urna. Morbi viverra ligula urna, id sagittis risus fringilla quis. Praesent efficitur eu ligula faucibus bibendum. Quisque nec felis consectetur, dignissim diam non, tempus felis.
                </p>


                <br /><br />
                    /-------------------------------------/
                <br /><br />
                <!-- ETAPE 2 •   Dispatching  -->
                <!-- Objet du mail = Question CRIDON LYON numéro <numéro question> prise en compte -->

                <p><h2>Nous avons bien reçu votre question N° 123456789 du 12.09.2015 de niveau d'expertise initiale en délai support.</h2>.</p>

                <p><strong>Objet de la question</strong></p>
                <br />
                <span class="section">Matière</span>
                <p>>  Compétence</p>

                <p><h2>Réponse souhaitée le 11.10.2015</h2></p>
                <br />
                <p>
                    TEXTE DE LA QUESTION Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer euismod, enim a accumsan varius, lectus turpis ultrices lectus, eget blandit nisi leo a libero. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam eget finibus leo. Morbi at nisl tincidunt, convallis ante tristique, aliquam nunc. Nam sit amet cursus urna. Morbi viverra ligula urna, id sagittis risus fringilla quis. Praesent efficitur eu ligula faucibus bibendum. Quisque nec felis consectetur, dignissim diam non, tempus felis.
                </p>

                <br /><br />
                    /-------------------------------------/
                <br /><br />

                <!-- ETAPE 3 •   •  Déclassement   -->
                <!-- Objet du mail = Requalification de la question CRIDON LYON numéro <numéro question> -->
                
                <p><h3>Compte tenu de l’affluence des demandes, il ne nous sera pas possible de respecter le délai demandé de votre question N° 123456789 du 12.09.2015.</h3></p>
                <p><h2>Nous enregistrons votre question en délai « support » et faisons le nécessaire pour vous donner satisfaction.</h2></p>
                <br />
                <p><strong>Objet de la question</strong></p>
                <br />
                <span class="section">Matière</span>
                <p>>  Compétence</p>

                <p><h2>Réponse souhaitée le 11.10.2015</h2></p>
                <br />
                <p>
                    TEXTE DE LA QUESTION Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer euismod, enim a accumsan varius, lectus turpis ultrices lectus, eget blandit nisi leo a libero. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam eget finibus leo. Morbi at nisl tincidunt, convallis ante tristique, aliquam nunc. Nam sit amet cursus urna. Morbi viverra ligula urna, id sagittis risus fringilla quis. Praesent efficitur eu ligula faucibus bibendum. Quisque nec felis consectetur, dignissim diam non, tempus felis.
                </p>


                <br /><br />
                    /-------------------------------------/
                <br /><br />

                <!-- ETAPE 4 •   Attente -->
                <!-- Objet du mail = Question CRIDON LYON numéro <numéro question> en cours de traitement --> 

                <p><h2>Votre question N° 123456789 de niveau d'expertise initiale en délai support a été attribuée le 12.03.16 à Nom du chercheur.</h2></p>
                <p><h3>Une réponse vous sera apportée au plus tard le 15.03.2016</h3></p>
                <br />
                <p><strong>Objet de la question</strong></p>
                <br />
                <span class="section">Matière</span>
                <p>>  Compétence</p>
                <br />
                <p>
                    TEXTE DE LA QUESTION Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer euismod, enim a accumsan varius, lectus turpis ultrices lectus, eget blandit nisi leo a libero. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam eget finibus leo. Morbi at nisl tincidunt, convallis ante tristique, aliquam nunc. Nam sit amet cursus urna. Morbi viverra ligula urna, id sagittis risus fringilla quis. Praesent efficitur eu ligula faucibus bibendum. Quisque nec felis consectetur, dignissim diam non, tempus felis.
                </p>

                <br /><br />
                    /-------------------------------------/
                <br /><br />

                
                 <!-- ETAPE 5 •   • Suspendue -->
                <!-- Objet du mail = Question CRIDON LYON numéro <numéro question> en attente de renseignements complémentaires --> 

                <p><h3>Merci de nous adresser les renseignements complémentaires demandés qui nous sont indispensables pour répondre à votre question N° 123456789 de niveau d'expertise initiale en délai support du 12.09.2015.</h3></p>
                <br />
                <p><strong>Objet de la question</strong></p>
                <br />
                <span class="section">Matière</span>
                <p>>  Compétence</p>
                <br />
                <p><strong>En cours de traitement par : Nom du chercheur</strong></p>
                <p><h2>Réponse souhaitée le 11.10.2015</h2></p>
                <br />
                <p>
                    TEXTE DE LA QUESTION Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer euismod, enim a accumsan varius, lectus turpis ultrices lectus, eget blandit nisi leo a libero. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam eget finibus leo. Morbi at nisl tincidunt, convallis ante tristique, aliquam nunc. Nam sit amet cursus urna. Morbi viverra ligula urna, id sagittis risus fringilla quis. Praesent efficitur eu ligula faucibus bibendum. Quisque nec felis consectetur, dignissim diam non, tempus felis.
                </p>


                <br /><br />
                    /-------------------------------------/
                <br /><br />

                <!-- ETAPE 6 •     Close -->
                <!-- Objet du mail = Réponse à votre question CRIDON LYON numéro <numéro question> -->

                <p><h2>La réponse à votre question N° 123456789 de niveau d'expertise initiale en délai support du 12.09.2015 est disponible depuis votre espace privé.</h2></p>
                <br />
                <p><strong>Objet de la question</strong></p>
                <br />
                <span class="section">Matière</span>
                <p>>  Compétence</p>
                <br />
                <p><strong>Traité par : Nom du chercheur</strong></p>
                <br />
                <p>
                    TEXTE DE LA QUESTION Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer euismod, enim a accumsan varius, lectus turpis ultrices lectus, eget blandit nisi leo a libero. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam eget finibus leo. Morbi at nisl tincidunt, convallis ante tristique, aliquam nunc. Nam sit amet cursus urna. Morbi viverra ligula urna, id sagittis risus fringilla quis. Praesent efficitur eu ligula faucibus bibendum. Quisque nec felis consectetur, dignissim diam non, tempus felis.
                </p>






















































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