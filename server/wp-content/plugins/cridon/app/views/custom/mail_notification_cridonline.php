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
        h3 {font-size: 20px; line-height: 18px; text-transform: uppercase; margin:10px 0px 2px 0px; color:#009999; font-weight:normal; padding:0px; font-family: 'Dosis', Arial, Helvetica, sans-serif;}

        a {
            cursor:pointer;
            color:#b9003f;
            text-decoration:underline;
            border:none;
        }

        .footerlinks a{color:#fff; text-decoration:none;}
        .cridon_footer{font-weight:bold; font-size:16px; font-family:Dosis, Arial, Helvetica, sans-serif;}

        p{margin:10px 0 10px 0; padding:0px}

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

                $modelFile = "banner-cridonline.png";
                $alt = "Inscription Crid'online";
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


                <p><strong>Étude : <?php echo $etude->office_name ?></strong> - CRPCEN : <?php echo $etude->crpcen ?> - Identifiant SEPA : <?php echo $id_sepa ?></p>
                <h2>Vous avez choisi l’offre CRID’ONLINE <?php echo $level_label ?> pour <?php echo $price ?> € HT par an.</h2>

                <p>Votre abonnement a bien été pris en compte pour la période du  <strong><?php echo date_i18n('d F Y',strtotime($start_subscription_date)) ?></strong> au <strong><?php echo date_i18n('d F Y',strtotime($end_subscription_date)) ?></strong>.</p>

                <p>Nous vous remercions de votre confiance</p>

                <h3>Le service est actif dès à présent.</h3>

                <p>Vous trouverez en pièces jointes deux documents, les Conditions Générales d'Utilisation et de Vente ainsi que le mandat de prélèvement SEPA</p>

                <p>Veuillez imprimer, remplir et nous envoyer le mandat de prélèvement SEPA signé par une personne habilitée ainsi qu’un RIB à l’adresse suivante :</p>

                <p><i>CRIDON LYON<br />
                    Service comptabilité<br />
                    37 boulevard des Brotteaux<br />
                    69455 LYON CEDEX 06</i>
                </p>

                <p>La <strong>R</strong>éférence <strong>U</strong>nique du <strong>M</strong>andat à reporter est la suivante : <?php echo $id_sepa ?>.</p>

                <?php if ($B2B_B2C == 'B2B'): ?>
                    <p>Veuillez également envoyer une copie du mandat de prélèvement SEPA signé à votre banque.</p>

                    <p><strong>Nous attirons votre attention sur le fait que l’envoi d’une copie à votre banque est obligatoire sous peine de rejet du prélèvement et de facturation de frais bancaires.
                        </strong></p>
                <?php endif; ?>




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