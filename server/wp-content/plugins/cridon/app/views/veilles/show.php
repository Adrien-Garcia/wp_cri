
<?php
include TEMPLATEPATH.'/single-veilles.php';
if ($download_url) {
    ?>
    <script type="text/javascript">
        jQuery(function() {
            window.location.href = '<?php echo $download_url; ?>';
        });
    </script>
    <?php
}
?>
<!--p>
    <?php // echo $this->html->link('&#8592; All Veilles', array('controller' => 'veilles')); ?>
</p!-->