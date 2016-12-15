<?php
    $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
    header( "$protocol 200 OK" );
    // Send content header and start ATOM output
    header('Content-Type: '.feed_content_type('rss-http').'; charset='.get_option('blog_charset'), true);
    // Disabling caching
    header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
    header('Pragma: no-cache'); // HTTP 1.0.
    header('Expires: 0'); // Proxies.
    echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
?>

<rss version="2.0"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:wfw="http://wellformedweb.org/CommentAPI/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:atom="http://www.w3.org/2005/Atom"
    xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
    xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
    xmlns:media="http://search.yahoo.com/mrss/" >
    <channel>
        <title><?php echo $title ?></title>
        <atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
        <link><?php bloginfo_rss('url') ?></link>
        <description><?php echo $description  ?></description>
        <lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
        <language><?php echo get_option('rss_language'); ?></language>
        <sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
        <sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
        <?php do_action('rss2_head'); ?>
        <?php 
        foreach ( $objects as $object ) : 
            $post = $object->post;
            $matiere = $object->matiere;
            criWpPost($object); 
        ?>
            <item>
                <title><?php the_title_rss(); ?></title>
                <guid isPermaLink="false"><?php the_guid(); ?></guid>
                <description><![CDATA[Matière: <?php echo $matiere->label.'<br/>'; ?> 
                    <?php if (!empty($post->post_excerpt)): ?>
                        <?php the_excerpt_rss() ?>
                    <?php else: ?>
                        <?php echo wp_trim_words( wp_strip_all_tags( get_the_content(), true ), 85, "..." ) ?>
                    <?php endif ?>
                    <ul class="mots_cles">
			<?php 
                            $tags = get_the_tags();
                            if( $tags ) : foreach ($tags as $tag) :
			 ?>
                                <li><?php echo $tag->name; ?></li>
			<?php endforeach; endif; ?>
                    </ul>
                ]]>
                </description>
                <content:encoded><![CDATA[Matière: <?php echo $matiere->label.'<br/>'; ?>                    
                    <?php if (!empty($post->post_excerpt)): ?>
                        <?php the_excerpt_rss() ?>
                    <?php else: ?>
                        <?php echo wp_trim_words( wp_strip_all_tags( get_the_content(), true ), 85, "..." ) ?>
                    <?php endif ?>
                    <ul class="mots_cles">
			<?php 
                            $tags = get_the_tags();
                            if( $tags ) : foreach ($tags as $tag) :
			 ?>
                                <li><?php echo $tag->name; ?></li>
			<?php endforeach; endif; ?>
                    </ul>]]>
                </content:encoded>
                <?php 
                $class = $object->__model_name;
                if (property_exists($object,'documents') || method_exists($class, "getDocuments")) : 
                    $model = mvc_model('Document');
                    if(property_exists($object,'documents')){
                        $documents = $object->documents;
                    }else{
                        $documents = $class::getDocuments($object);
                    }
                    foreach($documents as $document):
                        $url = home_url().$model->generatePublicUrl($document->id);
                        $uploadDir = wp_upload_dir();
                        $file = $uploadDir['basedir'].$document->file_path;
                        $size = 0;
                        if(file_exists($file)){
                            $size = filesize($file);
                        } else {
                            continue;
                        }
                ?>
                <enclosure url="<?php echo $url?>" length="<?php echo $size?>" type="application/pdf" />
                <?php 
                    endforeach;
                endif 
                ?>
                <?php do_action('rss2_item'); ?>
            </item>
        <?php endforeach; ?>
    </channel>
</rss>
