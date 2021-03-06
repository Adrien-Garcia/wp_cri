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
                if ($model == "flash") {
                    $modelFile = "banner-flash.png";
                    $alt = "Mes flash info";
                } else if ($model == "viecridon") {
                    $modelFile = "banner-vie-cridon.png";
                    $alt = "La vie du Cridon";
                }
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
                <span class="newsletter_date" style="text-transform: uppercase;"><?php echo sprintf(Config::$mailBodyNotification['date'],  $date ); ?></span>
                <br/><br/>
                <?php if (! empty($matiere)) : ?>
                    <table>
                        <tr>
                            <td width="90" style="width:90px;height:90px;">
                                <img src="<?php echo $matiere->picto ?>" alt="icon" width="90" height="90" style="width:90px;height:90px;" /><br/>
                                <span class="section"><?php echo sprintf(Config::$mailBodyNotification['matiere'],  $matiere->label ); ?></span>
                            </td>
                            <td style="width:400px;" width="400">
                                <h1><?php echo  sprintf(Config::$mailBodyNotification['title'],  $title );?></h1>
                            </td>
                        </tr>
                    </table>
                <?php else : ?>
                    <h1><?php echo sprintf(Config::$mailBodyNotification['title'],  $title );?></h1>
                <?php endif; ?>

                <span class="introduction"><?php echo !empty($excerpt) ? sprintf(Config::$mailBodyNotification['excerpt'],  $excerpt ) : "" ?></span><br/>

                <?php echo sprintf(Config::$mailBodyNotification['content'],  $content ); ?>
                <br/>
                <br/>
                <?php
                    $options = array(
                        'controller' => MvcInflector::pluralize(strtolower($model)),
                        'action'     => ( !isset( $model->id ) ) ? 'index' : 'show'
                    );
                    $url = MvcRouter::public_url($options);
                ?>
                <?php echo sprintf(Config::$mailBodyNotification['permalink'], $permalink, "Lire sur site"); ?>
                <p style="margin-top:25px;">

                    <?php
                    if( !empty( $documents ) ){
                        echo '<h2>Documents liés</h2><ul>';
                        foreach( $documents as $document ){
                            echo sprintf ('<li><a href="%s">%s</a></li>',   $home. $documentModel->generatePublicUrl($document->id),$document->name );
                        }
                        echo '</ul>';
                    }?>
                </p>
                <p style="font-weight:bold;">
                <?php
                    if( $tags ){
                        echo Config::$mailBodyNotification['tags'].' ';
                        $a = array();
                        foreach ( $tags as $tag ){
                            $a[] .= $tag->name;
                        }
                        echo implode(' | ',$a);
                    }
                    ?>
                </p>
            <td width="20" style="background-color:#fff;"></td>
        </tr>


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

        <tr>
            <td width="20" style="background-color:#e6e6e6;"></td>
            <td width="560" height="30" style="background-color:#e6e6e6; font-size:11px; color:#15283f; text-align:left">Vous souhaitez vous désabonner de cette liste de veille juridique:
                <?php if ($model == "veille") : ?>
                <a href="<?php echo $home ?>/notaires/profil">cliquez-ici.</a>
                <?php else : ?>
                <a href="<?php echo $home ?>/contact">cliquez-ici.</a>

                <?php endif; ?>

            </td>
            <td width="20" style="background-color:#e6e6e6;"></td>
        </tr>


        </tbody>
    </table>


</div>
</body>
</html>
