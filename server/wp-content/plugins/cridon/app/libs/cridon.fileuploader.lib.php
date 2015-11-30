<?php

/**
 * Description of cridon.fileuploader.lib.php
 *
 * @package wp_cridon
 * @author Etech
 * @contributor Joelio
 */
class CriFileUploader
{

    /**
     * @var int
     */
    const CONST_MAX_FILES = 5;

    /**
     * @var int
     */
    const CONST_MAX_FILE_SIZE = 8000000;

    /**
     * @var string
     */
    const CONST_UPLOAD_DIR = 'uploads';

    /**
     * @var int
     */
    const CONST_FILE_PERMISSION = 0777;


    /**
     * Upload path
     *
     * @var string
     */
    protected $uploaddir;

    /***
     * Max file number
     *
     * @var int
     */
    protected $max_files;

    /**
     * Max file size
     *
     * @var mixed
     */
    protected $max_size;

    /**
     * Permission
     *
     * @var int
     */
    protected $permission;

    /**
     * Flag for display error
     *
     * @var boolean
     */
    protected $show;

    /**
     * Files list
     *
     * @var mixed
     */
    protected $files;

    /**
     * List of not allowed file type
     *
     * @var array
     */
    protected $notallowed;

    /**
     * Erros list
     *
     * @var array
     */
    protected $errors = array();

    /**
     * CriFileUploader constructor.
     */
    public function __construct()
    {
        // preset some values
        $this->uploaddir  = self::CONST_UPLOAD_DIR;
        $this->max_files  = self::CONST_MAX_FILES;
        $this->max_size   = self::CONST_MAX_FILE_SIZE;
        $this->permission = self::CONST_FILE_PERMISSION;
        $this->notallowed = array();
    }

    /**
     * Check if we need to add slash if necessary
     *
     * @param string $content
     * @return string
     */
    public function hasLastSlash($content){
        $loc = $content;
        $last_slash = (substr($content,strlen($content)-1,1)=="/");
        if (!$last_slash) {
            $loc = ($content . "/");
        }
        return $loc;
    }

    /**
     * Validate file
     *
     * @return bool
     */
    public function validate() {
        $num = count($this->files['name']);
        // Control for max_files
        if ($num > $this->max_files) {
            $this->errors[0][] = "To many files! max allowed = " . $this->max_files;
            return FALSE;
        } else {
            //check for all files, SIZE and FILE_TYPE
            for ($i = 0; $i < $num; $i++) {
                //Check SIZE
                if ($this->files['size'][$i] > $this->max_size) {
                    $this->errors[1][] = "File: " . $this->files['name'][$i] .
                                         " size: " . $this->files['size'][$i] .
                                         " not allowed Max=: " .
                                         ($this->max_size/1000) . " kb";
                }
                // file type informations
                $files = pathinfo($this->files['name'][$i]);

                // Check if file-type ALLOWED
               if (in_array($files['extension'], $this->notallowed)) {
                    $this->errors[2][] = "File: " . $this->files['name'][$i] .
                                         " type: " . $this->files['type'][$i] .
                                         " not allowed";
                }
            }
            if (count($this->errors)>0) {
                return false;
            }
        }
        return TRUE;
    }

    /**
     * Upload file action
     *
     * @return array|bool
     */
    public function execute()
    {
        // Get directory
        $remdir = $this->uploaddir;

        // Add when nessecary a slash
        $remdir = $this->hasLastSlash($remdir);

        // Is dir writeable
        if (!is_writable($remdir)) {
            $this->errors[0][] = "Not allowed to write to dir:" . $remdir;
            return false;
        }

        // output
        $outputs = array();

        // files count
        $num = count($this->files['name']);

        // Control for max_files
        if ($num > $this->max_files) {
            $this->errors[0][] = "To many files! max allowed = " . $this->max_files;
            return false;
        } else {
            // check for all files, SIZE and FILE_TYPE
            for ($i = 0;$i < $num; $i++) {
                $files = pathinfo($this->files['name'][$i]);
                $filename = sanitize_title($files['basename']) . '.' . $files['extension'];
                $output = $filename;
                //$this->errors[0][] = $filename;
                if ( !empty( $filename)) {			   // this will check if any blank field is entered
                    $add = $remdir . $filename;		   // upload directory path is set
                    if (file_exists($add)) {
                        $output = mt_rand(1, 10) . '_' . $filename;
                        $add = $remdir . $output;
                    }
                    if(is_uploaded_file($this->files['tmp_name'][$i]))
                    {
                        move_uploaded_file($this->files['tmp_name'][$i], $add);
                        if (!chmod( "$add", $this->permission)) { // set permission to the file.
                            $this->errors[0][] = "Problems with copy of: " . $filename;
                        }

                        $outputs[] = $output;
                    }
                }
            }
            return $outputs;
        }
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @param string $fieldName
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;
    }

    /**
     * @return string
     */
    public function getUploaddir()
    {
        return $this->uploaddir;
    }

    /**
     * @param string $uploaddir
     */
    public function setUploaddir($uploaddir)
    {
        $this->uploaddir = $uploaddir;
    }

    /**
     * @return int
     */
    public function getMaxFiles()
    {
        return $this->max_files;
    }

    /**
     * @param int $max_files
     */
    public function setMaxFiles($max_files)
    {
        $this->max_files = $max_files;
    }

    /**
     * @return mixed
     */
    public function getMaxSize()
    {
        return $this->max_size;
    }

    /**
     * @param mixed $max_size
     */
    public function setMaxSize($max_size)
    {
        $this->max_size = $max_size;
    }

    /**
     * @return int
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * @param int $permission
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;
    }

    /**
     * @return mixed
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param mixed $files
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }

    /**
     * @return array
     */
    public function getNotallowed()
    {
        return $this->notallowed;
    }

    /**
     * @param array $notallowed
     */
    public function setNotallowed($notallowed)
    {
        $this->notallowed = $notallowed;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return boolean
     */
    public function isShow()
    {
        return $this->show;
    }

    /**
     * @param boolean $show
     */
    public function setShow($show)
    {
        $this->show = $show;
    }

    /**
     * @param array $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }
}