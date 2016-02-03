<?php

/**
 *
 * This file is part of project 
 *
 * File name : functions.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

/**
 * Global function to find posts in table associate 
 * 
 * @global object $cri_container
 * @param array $options See options of query builder in models/query_builder.php 
 * @param string $primaryKey The primary for ordering result
 * @return array or object
 */
function criQueryPosts( $options = array(),$primaryKey = 'p.ID' ){
    global $cri_container;
    $table = 'posts';
    $defaultOptions = array(
        'synonym' => 'p',
        'limit'   => false,
        'join'    => array(
            'veille' => array(
                'table'  => 'veille v',
                'column' => 'v.post_id = p.ID',
                'type'   => 'left'
            )
        )
    );
    $options = array_merge( $defaultOptions, $options );
    $query_builder = $cri_container->get( 'query_builder' );
    if( $options['limit'] ){
        return $query_builder->findOne( $table, $options,$primaryKey );
    }else{
        if ( $options['limit'] === false ) {
            unset( $options['limit'] );
        }
        return $query_builder->findAll( $table, $options,$primaryKey );
    }    
}
/**
 * Find a latest post by model
 * 
 * @param string $model Model name <b>without prefix, e.g: cri_veille</b>
 * @return null or object
 */
function criGetLatestPost( $model ){
    if( !is_string( $model ) || empty( $model ) ){
        return null;
    }
    if( $model === 'veille' ){
        return criQueryPostVeille( 1,'DESC' );
    }
    global $cri_container;
    $tools = $cri_container->get( 'tools' );
    $options = array(
        'fields' => $tools->getFieldPost().$model[0].'.id as join_id',
        'limit' => 1,
        'join'  => array(
            $model => array(
                'table' => $model.' '.$model[0],
                'column' => $model[0].'.post_id = p.ID'
            )
        ),
        'conditions' => 'p.post_status = "publish"',
        'order' => 'DESC'
    );
    $result = criQueryPosts( $options , "post_date");
    if( $result ){
        $latest = new stdClass();
        $latest->title   = $result->post_title;
        $latest->content = $result->post_content;
        $latest->link = CridonPostUrl::generatePostUrl( $model, $result->post_name );
        $latest->post = $tools->createPost( $result ); // Create Object WP_Post
        return $latest;
    }
    return null;
}

/**
 * Form params setter (to be called on the login template)
 *
 * @param string $formAttributeId
 * @param string $loginAttributeId
 * @param string $passwdAttributeId
 * @param string $errorBlocAttributeId
 */
if (!function_exists('criSetLoginFormOptions')) {
    function criSetLoginFormOptions(
        $formAttributeId
        , $loginAttributeId
        , $passwdAttributeId
        , $errorBlocAttributeId
    ) {
        // all params are needed to be set
        if ($formAttributeId
            && $loginAttributeId
            && $passwdAttributeId
            && $errorBlocAttributeId
        ) {
            global $cri_container;

            $cri_container->setLoginFormId($formAttributeId);
            $cri_container->setLoginFieldId($loginAttributeId);
            $cri_container->setPasswordFieldId($passwdAttributeId);
            $cri_container->setErrorBlocId($errorBlocAttributeId);

            add_action('wp_enqueue_scripts', 'append_js_var');
        }
    }

    // hook for overriding default login form  var
    function append_js_var()
    {
        global $cri_container;

        require_once ABSPATH . WPINC . '/pluggable.php';

        // only in front
        if (!is_admin()) {
        ?>
        <script type="text/javascript">
            var loginFormIdOverride = '<?php echo $cri_container->getLoginFormId() ?>',
                errorBlocIdOverride = '<?php echo $cri_container->getErrorBlocId() ?>',
                loginFieldIdOverride = '<?php echo $cri_container->getLoginFieldId() ?>',
                passwordFieldIdOverride = '<?php echo $cri_container->getPasswordFieldId() ?>';
        </script>
    <?php
        }
    }
}

/**
 * Form params setter (to be called on the lost password template)
 *
 * @param string $formAttributeId
 * @param string $emailAttributeId
 * @param string $crpcenAttributeId
 * @param string $msgBlocAttributeId
 */
if (!function_exists('criSetLostPwdOptions')) {
    function criSetLostPwdOptions(
        $formAttributeId
        , $emailAttributeId
        , $crpcenAttributeId
        , $msgBlocAttributeId
    ) {
        // all params are needed to be set
        if ($formAttributeId
            && $emailAttributeId
            && $crpcenAttributeId
            && $msgBlocAttributeId
        ) {
            global $cri_container;

            $cri_container->setLostPwdFormId($formAttributeId);
            $cri_container->setEmailFieldId($emailAttributeId);
            $cri_container->setCrpcenFieldId($crpcenAttributeId);
            $cri_container->setMsgBlocId($msgBlocAttributeId);

            add_action('wp_enqueue_scripts', 'append_js_lostpwd_var');
        }
    }

    // hook for overriding default form  var
    function append_js_lostpwd_var()
    {
        global $cri_container;

        require_once ABSPATH . WPINC . '/pluggable.php';

        // only in front
        if (!is_admin()) {
        ?>
        <script type="text/javascript">
            var lostPwdFormIdOverride = '<?php echo $cri_container->getLostPwdFormId() ?>',
                msgBlocIdOverride = '<?php echo $cri_container->getMsgBlocId() ?>',
                emailFieldIdOverride = '<?php echo $cri_container->getEmailFieldId() ?>',
                crpcenFieldIdOverride = '<?php echo $cri_container->getCrpcenFieldId() ?>';
        </script>
    <?php
        }
    }
}

/**
 * Filter post per date by model
 * 
 * 
 * @global object $cri_container
 * @param string $model Model name <b>without prefix, e.g: cri_veille</b>
 * @param integer $nb_date How many date must contain array of result?
 * @param integer $nb_per_date How many object must contain date?
 * @param string $index Index of array who contain object per date
 * @param string $format_date Format of date, default is french format
 * @return null or array of objects
 */
function criFilterByDate( $model,$nb_date,$nb_per_date,$index, $format_date = 'd/m/Y' ){
    if( !is_string( $model ) || empty( $model ) ){
        return null;
    }
    global $cri_container;
    $nestedOptions = array(
        'synonym' => 'p',
        'fields' => 'CAST(p.post_date AS DATE) AS date',
        'join'  => array(
            $model => array(
                'table' => $model.' '.$model[0],
                'column' => $model[0].'.post_id = p.ID'
            )
        ),
        'conditions' => 'p.post_status = "publish"',
        'group' => 'date',
        'limit' => $nb_date,
        'order' => 'DESC'
    );
    $query_builder = $cri_container->get( 'query_builder' );
    $nested = $query_builder->buildQuery( 'posts',$nestedOptions,'p.ID' );// Nested query ( simple string )
    $tools = $cri_container->get( 'tools' );
    $options = array(
        'fields' => $tools->getFieldPost().'CAST(p.post_date AS DATE) AS date,p.post_title,'.$model[0].'.id as join_id',
        'join'  => array(
            $model => array(
                'table' => $model.' '.$model[0],
                'column' => $model[0].'.post_id = p.ID'
            ),
            'nested' => array(
                'table' => '('.$nested.') AS nested',
                'column' => 'CAST(p.post_date AS DATE) = nested.date',
                'nested' => true
            )
        ),
        'conditions' => 'p.post_status = "publish"',
        'order' => 'DESC'
    );
    if( $model === 'veille' ){
        $fields = array('id','code','label','short_label','displayed','picto');
        $mFields = '';// fields of model Matiere
        foreach ( $fields as $v ){
            $mFields .= ',m.'.$v;
        }
        $options['fields'] = $options['fields'].$mFields;
        $options['join']['matiere'] = array(
                'table' => 'matiere m',
                'column' => 'm.id = '.$model[0].'.id_matiere'
            );
        $sortDate = 'CAST(p.post_date AS DATE)';
    }
    if ($model === 'formation' ){
        $options['fields'] = $options['fields'].',CAST(f.custom_post_date AS DATE) AS formation_date';
        $sortDate = 'CAST(f.custom_post_date AS DATE)';
    }
    $results = criQueryPosts( $options, $sortDate );
    //To have others attributes in array result. Default is object WP_Post
    //$res = $tools->buildSubArray( $model,$results, 'date',$nb_per_date,$index,$format_date, array('post_title','post_date','post_excerpt','post_content','join_id'), array('title','datetime','excerpt','content','join_id') );
    if( $model === 'veille' ){// If model Veille, so associate model Matiere in result
        $res = $tools->buildSubArray( $model,$results, 'date', $nb_per_date,$index,$format_date,array('matiere'),array('matiere'=>$fields) );        
    }else{
        $res = $tools->buildSubArray( $model,$results, 'formation_date', $nb_per_date,$index,$format_date );
    }
    return $res;
}

/**
 * If you want use all functions in WP for Post.
 * Init this function whith post data contain an instance of WP_Post
 * 
 * @global object $cri_container
 * @param array|object $data It's an object WP_Post or array of WP_Post
 */
function criWpPost( $data ){
    global $cri_container;
    $oPostQuery = $cri_container->get( 'post_query' );
    $oPostQuery->init( $data );
}

/**
 * Get the link of the current post ( link of model in WP_MVC ).
 * It's equivalent of the_permalink in WP
 * 
 * @global object $post WP_Post
 * @return string|null
 */
function criGetPostLink(){
    global $post;
    return ( ( $post ) && ( $post instanceof WP_Post ) ) ? CridonPostStorage::get( $post->ID ) : null;
}

/**
 * Function to fetch data in table cri_veille with Matiere
 * 
 * @global object $cri_container
 * @param integer $limit Limit result
 * @param string $order Order result ( ASC or DESC ) 
 * @return array
 */
function criQueryPostVeille( $limit = false,$order = 'ASC' ){
    $model = 'veille';
    global $cri_container;
    $tools = $cri_container->get( 'tools' );
    //All fields of table cri_matiere
    $fields = array('id','code','label','short_label','displayed','picto');
    $mFields = '';// fields of model Matiere
    foreach ( $fields as $v ){
        $mFields .= ',m.'.$v;
    }
    $options = array(
        'fields' => $tools->getFieldPost().$model[0].'.id as join_id'.$mFields,
        'join'  => array(
            $model => array(
                'table' => $model.' '.$model[0],
                'column' => $model[0].'.post_id = p.ID'
            ),//use join clause with table cri_matiere
            'matiere' => array(
                'table' => 'matiere m',
                'column' => 'm.id = '.$model[0].'.id_matiere'
            )
        ),
        'conditions' => 'p.post_status = "publish"',
        'order' => $order
    );
    if( $limit ){//It's limited?
        $options['limit'] = $limit;
    }
    $results = criQueryPosts( $options );//Get associated post 
    //When it's limited ( get one result), we got one object not an array
    if( !is_array( $results ) ){
        $std = new stdClass();
        $std->matiere = CridonObjectFactory::create( $results, 'matiere', $fields);
        $std->link = CridonPostUrl::generatePostUrl( $model, $results->post_name );
        $std->post = $tools->createPost( $results ); // Create Object WP_Post
        return $std;
    }
    // The result is an array of object ( stdClass )
    $aFinal = array();// Final result
    foreach( $results as $value ){
        $std = new stdClass();
        //Dissociate current objet to get an object Matiere ( only an object stdClass with all attributes as in table cri_matiere )
        $std->matiere = CridonObjectFactory::create( $value, 'matiere', $fields);
        $std->link = CridonPostUrl::generatePostUrl( $model, $value->join_id );
        //Dissociate current object to get an object Post ( WP_Post )
        $std->post = $tools->createPost( $value ); // Create Object WP_Post
        $aFinal[] = $std;
    }
    return $aFinal;
}


/**
 * Restore current data in loop while of WP ( object with their all attributes ( post, link, ... )
 * 
 * @global WP_Post $post
 * @return object
 */
function criRestoreData(){
    global $post; 
    return ( ( $post ) && ( $post instanceof WP_Post ) ) ? CridonPostStorage::get( $post->ID,'all' ) : null;
}

/**
 * Return current Matiere 
 * 
 * @return array|null
 */
function get_the_matiere() {
    $data = criRestoreData();//Get current result from query
    if( !empty( $data ) && !empty( $data->matiere ) ){
        //Construct result
        $mat = array(
            'name' => $data->matiere->label,
            'picto_url' => $data->matiere->picto
        );
        return $mat;
    }
    return null;
}

/**
 * Check if user logged in is Notaire
 *
 * @return bool
 */
function CriIsNotaire() {
    global $cri_container;

    // user logged in is notaire
    return $cri_container->get('tools')->isNotary();
}

/**
 * Get connected  notaire data
 *
 * @return mixed|null
 */
function CriNotaireData() {
    // check if user connected is notaire
    if (CriIsNotaire()) {
        // user data
        return mvc_model('notaire')->getUserConnectedData();
    }

    return null;
}

/**
 * Get all Matieres of current Notaire
 * 
 * @return array|null
 */
function getMatieresByNotaire(){
    //output
    $aResults = array();
    // check if user connected is notaire
    if ( CriIsNotaire() ) {
        $notaire = CriNotaireData();
        $options = array(
            'conditions' => array(
                'Matiere.displayed' => 1
            )
        );
        $aSubscribed = array();
        $matieres = mvc_model('matiere')->find( $options );
        if( isset( $notaire->matieres ) && !empty( $notaire->matieres ) ){
            //Matiere subscribed by Notaire
            foreach( $notaire->matieres as $mat ){
                $aSubscribed[] = $mat->id;
            }
        }
        foreach( $matieres as $mat ){
            $isSubscribed = false;
            //Check if it is subscribed by Notaire
            if( in_array( $mat->id,$aSubscribed ) ){
                $isSubscribed = true;
            }
            $aResults[$mat->id] = array(
                'name' => $mat->label,
                'subscribed' => $isSubscribed
            );
        }
        return $aResults;
    }
    return null;
}

/**
 * Check if notaire can access finances
 *
 * @return bool
 */
function CriCanAccessFinance() {
    // check if user connected is notaire
    if (CriIsNotaire()) {
        // user data
        return mvc_model('notaire')->userCanAccessFinance();
    }
    return false;
}

/**
 * List displayed matieres
 *
 * output format array_key = Matiere.id && array_value = Matiere.label
 *
 * ordered by label
 *
 * @return array
 */
function CriListMatieres()
{
    // init
    $matieres = array();

    // query options
    $options = array(
        'selects' => array('Matiere.id', 'Matiere.label', 'Matiere.code'),
        'conditions' => array(
            'Matiere.displayed' => 1
        ),
        'order' => 'Matiere.label ASC'
    );
    $items = mvc_model('Matiere')->find( $options );

    // format output
    if (is_array($items) && count($items) > 0) {
        foreach ($items as $item) {
            $matieres[$item->id]['label'] = $item->label;
            $matieres[$item->id]['code'] = $item->code;
        }
    }

    return $matieres;
}

/**
 * List of competences by id matiere
 *
 * output format array_key = Competence.id && array_value = Competence.label
 *
 * @param int $matiereId
 * @return array
 */
function CriCompetenceByMatiere($matiereId)
{
    $comptetences = array();

    // get code_matiere by matiere.id
    $matiere = mvc_model('Matiere')->find_by_id($matiereId);

    if ($matiere->code) {
        // query optoins
        $options = array(
            'selects'    => array('Competence.id', 'Competence.label', 'Competence.code_matiere'),
            'conditions' => array(
                'Competence.code_matiere' => $matiere->code,
                'Competence.displayed'    => 1,
            ),
            'order'      => 'Competence.label ASC'
        );
        $items   = mvc_model('Competence')->find($options);

        // format output
        if (is_array($items) && count($items) > 0) {
            foreach ($items as $item) {
                $comptetences[$item->id] = $item->label;
            }
        }
    }

    return $comptetences;
}

/**
 * List displayed competences
 *
 * output format array_key = Competence.id && array_value = Competence.label
 *
 * ordered by label
 *
 * @return array
 */
function CriListCompetences()
{
    // init
    $competences = array();

    // query optoins
    $options = array(
        'selects'    => array('Competence.id', 'Competence.label', 'Competence.code_matiere'),
        'conditions' => array(
            'Competence.displayed' => 1
        ),
        'order'      => 'Competence.label ASC'
    );
    $items   = mvc_model('Competence')->find($options);

    // format output
    if (is_array($items) && count($items) > 0) {
        foreach ($items as $item) {
            $competences[$item->id] = $item->label;
        }
    }

    return $competences;
}

/**
 * Action for post question
 *
 * @return boolean
 */
function CriPostQuestion() {
    return mvc_model('Question')->uploadDocuments( $_POST );
}

/**
 * List of displayed Support order by priority (order field)
 *
 * @return array
 */
function CriListSupport()
{
    // init
    $supports = array();

    // query optoins
    $options = array(
        'selects'    => array('Support.id', 'Support.label_front', 'Support.value', 'Support.description'),
        'conditions' => array(
            'Support.displayed' => 1
        ),
        'order'      => 'Support.order ASC',
        'limit'      => 3
    );
    $items   = mvc_model('Support')->find($options);

    // format output
    if (is_array($items) && count($items) > 0) {
        foreach ($items as $item) {
            $object = new \stdClass();
            $object->id = $item->id;
            $object->value = $item->value;
            $object->label_front = $item->label_front;
            $object->description = $item->description;

            $supports[] = clone $object;
        }
    }

    return $supports;

}

/**
 * Restore the questions asked by the notary
 * 
 * @return array
 */
function criRestoreQuestions(){
    $question = new QuestionNotaire();
    return $question;
}

/*
 * End restore
 */

function CriDisableAdminBarForExistingNotaire() {
    // start/end query block
    global $wpdb;

    $queryStart = " UPDATE `{$wpdb->usermeta}` ";
    $queryEnd   = ' END ';
    $updateMetaKey = $updateMetaValue = array();
    foreach (mvc_model('notaire')->find() as $notaire) {
        $updateMetaKey[] = " user_id = {$notaire->id_wp_user} THEN 'show_admin_bar_front' ";
        $updateMetaValue[] = " user_id = {$notaire->id_wp_user} THEN 'false' ";
    }

    // execute update query
    if (count($updateMetaKey) > 0) {
        // meta_key
        $query = ' SET `meta_key` = CASE ';
        $query .= ' WHEN ' . implode(' WHEN ', $updateMetaKey);
        $query .= ' ELSE `meta_key` ';
        $wpdb->query($queryStart . $query . $queryEnd);

        // meta_value
        $query = ' SET `meta_value` = CASE ';
        $query .= ' WHEN ' . implode(' WHEN ', $updateMetaValue);
        $query .= ' ELSE `meta_value` ';
        $wpdb->query($queryStart . $query . $queryEnd);
    }
}

/**
 * Recherche d'un fichier de facon recursif dans un repertoire
 *
 * @param string $path
 * @param string $file
 * @return string
 */
function CriRecursiveFindingFileInDirectory($path, $file)
{
    $path       = realpath($path);
    $fileSource = '';
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $filename) {
        if ($file == $filename->getFileName()) {
            $fileSource = $filename->getPathname();
        }
    }

    return $fileSource;
}

function CriRefuseAccess($error_code = "PROTECTED_CONTENT") {
    $referer = $_SERVER['HTTP_REFERER'];
    $request = "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    if (! empty($referer) /*&& strripos( $request , $referer)*/ ){
        $redirect = $referer;
    } else {
        $redirect = get_home_url();
    }

    $request = urlencode(htmlspecialchars("//" . $request, ENT_QUOTES, "UTF-8"));

    if( empty($request) ) {
        $request = false;
    }

    if (
        preg_match("/.*?[\?\&]openLogin=1.*?/", $referer) === 1 &&
        preg_match("/.*?[\?\&]messageLogin=" . $error_code . ".*?/", $referer) === 1
    ) {
        wp_redirect($redirect);
        return;
    }

    if (preg_match("/.*\?.*/", $referer)) {
        $redirect .= "&";
    } else {
        $redirect .= "?";
    }

    $redirect .= "openLogin=1&messageLogin=" . $error_code;
    if ($request) {
        $redirect .= "&requestUrl=" . $request;
    }
    wp_redirect($redirect);
}

/**
 *  Menu principal
 *
 */
function criNavPrincipal() {
    // Affiche le menu wp3 si possible (sinon, fallback)
    wp_nav_menu(array(
        'container' => false,                           // Supprime le conteneur par défaut de la navigation
        'container_class' => 'menu clearfix',           // Classe du conteneur
        'menu' => 'Menu principal',                     // Nom du menu
        'menu_class' => 'nav top-nav clearfix',         // Classe du menu
        'theme_location' => 'main-nav',           // Localisation du menu dans le thème
        'before' => '',                                 // Balisage avant le menu
        'after' => '',                                  // Balisage après le menu
        'link_before' => '',                            // Balisage avant chaque lien
        'link_after' => '',                             // Balisage après chaque lien
        'depth' => 0,                                   // Profondeur du menu (0 : aucune)
        'fallback_cb' => 'ao_nav_principale_fallback',  // fallback fonction (si pas de support du menu)
        'walker' => new CriCustomWalker			// Utilisation de la description
    ));
}
// update download_url field in cri_document when it's empty
function updateEmptyDownloadUrlFieldsDocument() {
    global $wpdb;
    $documents = $wpdb->get_results("SELECT id FROM ".$wpdb->prefix."document WHERE download_url = ''");
    if( !empty($documents)){
        $queryStart = " UPDATE `{$wpdb->prefix}document` ";
        $queryEnd   = ' END '; 
        $updateValues = array();
        foreach( $documents as $document ){
            $updateValues[] = " id = {$document->id}  THEN '/documents/download/{$document->id}'";
        }
        $query = ' SET `download_url` = CASE ';
        $query .= ' WHEN ' . implode(' WHEN ', $updateValues);
        $query .= ' ELSE `download_url` ';
        $wpdb->query($queryStart . $query . $queryEnd);
    }
}

/**
 * Custom breadcrumbs
 */
function CriBreadcrumb() {
    global $post,
           $mvc_params;

    // prepare vars
    $home        = new stdClass();
    $home->title = 'Accueil';
    $home->url   = home_url();
    $vars        = array(
        'breadcrumb'        => array($home),
        'separator'         => ' + ',
        'containerId'       => 'inner-content',
        'containerClass'    => 'wrap cf'
    );

    if (is_mvc_page()) { // WPMVC page (single, archives,...)
        if (isset($mvc_params['action']) && $mvc_params['action']) {
            if ($mvc_params['controller'] == 'notaires') { // page notaire
                // archive model
                $archive              = new stdClass();
                $archive->title       = 'Mon compte';
                $archive->url         = mvc_public_url(array(
                    'controller' => $mvc_params['controller']
                ));
                $vars['breadcrumb'][] = $archive;
            } else {
                // archive model
                $archive              = new stdClass();
                $archive->title       = isset(Config::$breadcrumbModelParams[$mvc_params['controller']]) ?
                    Config::$breadcrumbModelParams[$mvc_params['controller']] : ucfirst($mvc_params['controller']);
                $archive->url         = mvc_public_url(array(
                    'controller' => $mvc_params['controller']
                ));
                $vars['breadcrumb'][] = $archive;

                // single model
                if (isset($mvc_params['id']) && $mvc_params['id']) {
                    $singles              = mvc_model('QueryBuilder')->getPostByMVCParams();
                    $single               = new stdClass();
                    $single->title        = isset($singles->post_title) ? $singles->post_title : '';
                    $single->url          = mvc_public_url(array(
                        'controller' => $mvc_params['controller'],
                        'action'     => $mvc_params['action'],
                        'id'         => $mvc_params['id'],
                    ));
                    $vars['breadcrumb'][] = $single;
                    $vars['containerId']  = '';
                }
            }
        }
    } elseif ((is_single() || is_page()) && !is_attachment()) { // page or post single
        $single              = new stdClass();
        $single->title       = $post->post_title;
        $single->url         = get_the_permalink($post->ID);
        $vars['breadcrumb'][] = $single;
    }

    // render view
    CriRenderView('breadcrumb', $vars);
}

// Hook of the_permalink() and get_permalink()
function append_custom_link( $url, $post ) {
    if ( $post->post_type === 'post' ) {
        $newUrl = criGetPostLink();//Get custom post link
        if( $newUrl ){
            $url = $newUrl;
        }
    }
    return $url;
}
add_filter( 'post_link', 'append_custom_link', 10, 2 );
//get affectation label
/**
 * Obtenir l'étiquette d'une affectation
 *
 * @param integer $id
 * @return string
 */
function getAffectation($id){
    return isset(Config::$labelAffection[$id]) ? Config::$labelAffection[$id] : '';
}

function getMatieresByQuestionNotaire(){
    return mvc_model('Matiere')->getMatieresByNotaireQuestion();
}
/**
 * Redirect to 404
 *
 * @global \WP_Query $wp_query
 */
function redirectTo404(){
    global $wp_query;
    header("HTTP/1.0 404 Not Found");
    $wp_query->set_404();
    if( file_exists(TEMPLATEPATH.'/404.php') ){
        require TEMPLATEPATH.'/404.php';
    }
    exit;
}

/**
 * Add custom capabilities to admins Cridon user
 */
function CriSetAdminCridonCaps() {
    $role = get_role(CONST_ADMINCRIDON_ROLE);
    if ($role instanceof WP_Role) { // role already defined in WP Core
        if (is_array(Config::$authorizedCapsForAdminsCridon)
            && count(Config::$authorizedCapsForAdminsCridon) > 0
        ) { // custom capabilities defined
            foreach (Config::$authorizedCapsForAdminsCridon as $cap) {
                if (!array_key_exists($cap, $role->capabilities)) { // check if capability not yet in list
                    $role->add_cap($cap);
                }
            }
        }
    }
}
