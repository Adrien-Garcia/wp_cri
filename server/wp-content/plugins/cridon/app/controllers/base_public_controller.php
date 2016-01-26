<?php

/**
 * Base Controller for public option
 * @author Etech
 * @contributor Joelio
 * @version 1.0
 */
abstract class BasePublicController extends MvcPublicController
{

    /**
     * @var mixed
     */
    protected $data;

    public function __construct()
    {
        parent::__construct();

        $this->data = json_decode(file_get_contents('php://input'));
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }


    /**
     * Action switcher for creating | listing data
     */
    public function index_json()
    {
        $errors = new WP_Error();
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                // Create
                $status = $this->create_object();
                break;
            default:
                // Read list
                $status = $this->read_objects();
                break;
        }
        if (isset($status) && is_wp_error($status)) {
            $error_code = $status->get_error_code();
            $errors->add($error_code, $status->get_error_message($error_code));
        }
        if (!empty($errors->errors)) {
            $this->set('errors', $errors);
        }

        $this->render_view('index_json', array('layout' => 'json'));
    }

    /**
     * Action switcher for update | delete | read individual object
     */
    public function show_json()
    {
        $errors = new WP_Error();
        $id = $this->params['id'];
        if (isset($id)) {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'DELETE':
                    // Delete
                    $status = $this->delete_object($id);
                    break;
                case 'PUT':
                    // Update
                    $status = $this->update_object($id);
                    break;
                default:
                    // Read individual
                    $status = $this->read_object($id);
                    break;
            }
        }
        if (is_wp_error($status)) {
            $error_code = $status->get_error_code();
            $errors->add($error_code, $status->get_error_message($error_code));
        }
        if (!empty($errors->errors)) {
            $this->set('errors', $errors);
        }

        $this->render_view('show_json', array('layout' => 'json'));
    }

    /**
     * Action for create object
     *
     * @return WP_Error
     */
    protected function create_object()
    {
        $data = json_decode(@file_get_contents('php://input'));
        if (isset($data)) {
            $status = $this->model->create($data);
            if (!is_wp_error($status)) {
                $object = $this->model->find_by_id($status);
                $this->set('object', $object);
            }
        } else {
            $status = new WP_Error('invalid_request', 'No data specified');
        }

        return $status;
    }

    /**
     * Action for listing data
     */
    protected function read_objects()
    {
        return $this->set_objects();
    }

    /**
     * Action for read individual object
     *
     * @param int $id
     *
     * @return mixed
     */
    protected function read_object($id)
    {
        $status = $this->model->find_by_id($id);
        if (!is_wp_error($status)) {
            $this->set('object', $status);
        }

        return $status;
    }

    /**
     * Action for deleting object
     *
     * @param int $id
     *
     * @return mixed
     */
    protected function delete_object($id)
    {
        return $this->model->delete($id);
    }

    /**
     * Action for updating object
     *
     * @param $id
     *
     * @return mixed
     */
    protected function update_object($id)
    {
        $d = @file_get_contents('php://input');
        $data = json_decode($d);
        if (!empty($data)) {
            $data->id = $id;
            $status = $this->model->save($data);
            if (!is_wp_error($status)) {
                $this->set('object', $this->model->find_by_id($id));
            }
        }

        $this->render_view('update_object', array('layout' => 'json'));

        return $status;
    }

    /**
     * Object setter into layout (view)
     *
     * @return mixed
     */
    public function set_objects()
    {
        $this->process_params_for_search();
        $status = $this->model->paginate($this->params);
        if (!is_wp_error($status)) {
            $this->set('objects', $status['objects']);
            $this->set_pagination($status);
        } else {
            return $status;
        }
    }
    
    /**
     * Vérification du token 
     * 
     * @param \CridonRequest $request
     * @return boolean
     */
    public function checkToken( $request ) {
        $token = null;
        if( isset( $request->query['token'] ) ){
            $token = $request->query['token'];
        }else{
            if( isset( $request->request['token'] ) ){
                $token = $request->request['token'];
            }
        }
        if( $token === null  ){            
            return false;
        }
        $model = mvc_model('notaire');//load model notaire
        $result = $model->checkLastConnect( $token );
        return $result;
    }
}