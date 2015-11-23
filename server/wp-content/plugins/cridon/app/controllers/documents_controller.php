<?php

class DocumentsController extends MvcPublicController {
    public function download(){
        $document = $this->model->find_one_by_id( $this->params['id'] );
        if( empty( $document ) ){
            $this->generateError();
        }
        //Check if it's a Notaire and connected
        if( is_user_logged_in() && CriIsNotaire() ){
            $notaire = CriNotaireData();            
        }else{
            $notaire = null;//for User Cridon BO and a user not connected
        }
        $model = mvc_model( $document->type );
        //No model check
        if( empty( $model ) ){
            $this->generateError();
        }
        if( $model->name == 'Question' ){
            $question = $model->find_one_by_id( $document->id_externe );
            //Check user access
            $this->checkAccess($question,$notaire, $document);            
        }
        //Let's begin download
        $uploadDir = wp_upload_dir();
        $file = $uploadDir['basedir'].$document->file_path;
        $tmp  = explode( '/', $document->file_path );
        //Get file name
        $filename = $tmp[ count( $tmp ) - 1 ];
        $this->output_file($file, $filename);
    }
    
    /**
     * Generate error
     * 
     * @global \WP_Query $wp_query
     */
    private function generateError(){
        global $wp_query;
        header("HTTP/1.0 404 Not Found - Archive Empty");
        $wp_query->set_404();
        if( file_exists(TEMPLATEPATH.'/404.php') ){
            require TEMPLATEPATH.'/404.php';
        }
        exit;
    }
    
    /*
     * Downloading file
     * 
     * @param string $file
     * @param string $name
     * @param string $mime_type
     * @return resource
     */
    private function output_file($file, $name, $mime_type='')
    {
        if (!is_readable($file)) {
            die('File not found or inaccessible!');
        }
        $size = filesize($file);
        $name = rawurldecode($name);
        $known_mime_types=array(
            "pdf" => "application/pdf",
            "txt" => "text/plain",
            "html" => "text/html",
            "htm" => "text/html",
            "exe" => "application/octet-stream",
            "zip" => "application/zip",
            "doc" => "application/msword",
            "xls" => "application/vnd.ms-excel",
            "ppt" => "application/vnd.ms-powerpoint",
            "gif" => "image/gif",
            "png" => "image/png",
            "jpeg"=> "image/jpg",
            "jpg" =>  "image/jpg",
            "php" => "text/plain"
        );
        if( $mime_type == '' ){
            $file_extension = strtolower( substr( strrchr( $file,"." ),1 ) );
            if( array_key_exists( $file_extension, $known_mime_types ) ){
                $mime_type = $known_mime_types[ $file_extension ];
            } else {
                $mime_type = "application/force-download";
            };
        };
        //turn off output buffering to decrease cpu usage
        @ob_end_clean(); 
        // required for IE, otherwise Content-Disposition may be ignored
        if ( ini_get( 'zlib.output_compression' ) ) {
            ini_set( 'zlib.output_compression', 'Off' );
        }
        $name = explode('/',$name);
        $name = $name[ count($name)-1 ];
        header( 'Content-Type: ' . $mime_type );
        header( 'Content-Disposition: attachment; filename="'.$name.'"' );
        header( "Content-Transfer-Encoding: binary" );
        header( 'Accept-Ranges: bytes' );
        /* The three lines below basically make the 
        download non-cacheable */
        header( "Cache-control: private" );
        header( 'Pragma: private' );
        header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
        // multipart-download and download resuming support
        if( isset( $_SERVER['HTTP_RANGE'] ) )
        {
            list( $a, $range ) = explode( "=",$_SERVER['HTTP_RANGE'],2 );
            list( $range ) = explode( ",",$range,2 );
            list( $range, $range_end ) = explode( "-", $range );
            $range=intval( $range );
            if(!$range_end) {
                $range_end = $size-1;
            } else {
                $range_end = intval($range_end);
            }
            $new_length = $range_end-$range+1;
            header( "HTTP/1.1 206 Partial Content" );
            header( "Content-Length: $new_length" );
            header( "Content-Range: bytes $range-$range_end/$size" );
        } else {
            $new_length = $size;
            header( "Content-Length: ".$size );
        }
        /* Will output the file itself */
        $chunksize = 1*(1024*1024); //you may want to change this
        $bytes_send = 0;
        if ( $file = fopen( $file, 'r' ) )
        {
            if ( isset( $_SERVER['HTTP_RANGE'] ) ) {
                fseek( $file, $range );
            }

            while( !feof( $file ) && ( !connection_aborted() ) && ( $bytes_send < $new_length ) ){
                $buffer = fread( $file, $chunksize );
                print( $buffer ); 
                flush();
                $bytes_send += strlen( $buffer );
            }
            fclose($file);
        } else
                //If no permissiion
            die('Error - can not open file.'); 			
        die();
    }

    public function importinitial()
    {
        $this->model->importInitial();
    }
    
    private function checkAccess( $question,$notaire,$document ){
        //If we are in BO, logged and not a Notaire
        if ( is_user_logged_in() && empty( $notaire ) ) {
            //If user cridon, they can download with no restriction
            return true;
        }
        //Check if question exist, document file path is valid
        if( empty( $notaire ) || empty( $question ) || empty( $document->file_path ) ){
            $this->generateError();
        }        
        //Check if question is created by current user
        if( $question->client_number != $notaire->client_number ){
            $this->generateError();
        }
        return true;
    }
}