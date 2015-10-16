<?php

/**
 *
 * This file is part of project 
 *
 * File name : Container.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class Container {
    
    private $registries = array();
    private $instances = array();
    private $factories = array();

    /**
     * @var string : login form attribute id
     */
    private $loginFormId;

    /**
     * @var string : login field attribute id
     */
    private $loginFieldId;

    /**
     * @var string : password field attribute id
     */
    private $passwordFieldId;

    /**
     * @var string : error bloc attribute id
     */
    private $errorBlocId;

    /**
     * Save immediately a instance of object in array of instances <br/><br/>
     * 
     * e.g: $dic->setInstance(new DataBase());
     * 
     * @param object $instance
     */
    public function setInstance( $instance ){
        $reflection = new ReflectionClass( $instance );
        $this->instances[$reflection->getName()] = $instance;
    }
   
    /**
     * Register resolver for instance <br/><br/>
     * e.g: 
     * $dic->set('Database', function(){//callable <br/>
     *    return new DataBase(); <br/>
     * });<br/>
     * 
     * @param string $key
     * @param callable $resolver
     */
    public function set( $key,  callable $resolver ){
        $this->registries[ $key ] = $resolver;
    }
    
    
    /**
     * Return instance of object
     * 
     * @param string $key
     * @return object
     * @throws Exception
     */
    public function get( $key ){
        if( isset( $this->factories[$key] ) ){
            return $this->factories[$key]();            
        }
        if( !isset( $this->instances[$key] ) ){
            if( isset( $this->registries[$key] ) ){
                $this->instances[$key] =  $this->registries[$key]();        
                
            }else{
                // See php doc in: http://php.net/manual/class.reflectionclass.php
                $reflected_class = new ReflectionClass($key);
                if( $reflected_class->isInstantiable() ){
                    $constructor = $reflected_class->getConstructor();
                    $this->createObject( $constructor,$reflected_class );
                }
                throw new Exception( $key.' is not instantiable.' );
            }
        }
        return $this->instances[ $key ];
    }
    /**
     * 
     * @param object $constructor
     * @param object $reflected_class
     * @return object
     */
    private function createObject( $constructor,$reflected_class ){
        if( $constructor ){//If have constructor so find parameters
            $parameters = $constructor->getParameters();
            $constructorParameters = array();
            foreach ( $parameters as $parameter ){
                if( $parameter->getClass() ){
                    $constructorParameters[] = $this->get( $parameter->getClass()->getName() );
                }else{
                    $constructorParameters[] = $parameter->getDefaultValue();
                }
            }
            return $this->instances[$key] = $reflected_class->newInstanceArgs( $constructorParameters );                        
        }else{
            return $this->instances[$key] = $reflected_class->newInstance();                        
        }        
    }
    /**
     * Always have a new instance for object
     * 
     * @param string $key
     * @param callable $resolver
     */
    public function setFactory( $key, callable $resolver ){
        $this->factories[$key] = $resolver;
    }

    /**
     * @return string
     */
    public function getLoginFormId()
    {
        return $this->loginFormId;
    }

    /**
     * @param string $loginFormId
     */
    public function setLoginFormId($loginFormId)
    {
        $this->loginFormId = $loginFormId;
    }

    /**
     * @return string
     */
    public function getLoginFieldId()
    {
        return $this->loginFieldId;
    }

    /**
     * @param string $loginFieldId
     */
    public function setLoginFieldId($loginFieldId)
    {
        $this->loginFieldId = $loginFieldId;
    }

    /**
     * @return string
     */
    public function getPasswordFieldId()
    {
        return $this->passwordFieldId;
    }

    /**
     * @param string $passwordFieldId
     */
    public function setPasswordFieldId($passwordFieldId)
    {
        $this->passwordFieldId = $passwordFieldId;
    }

    /**
     * @return string
     */
    public function getErrorBlocId()
    {
        return $this->errorBlocId;
    }

    /**
     * @param string $errorBlocId
     */
    public function setErrorBlocId($errorBlocId)
    {
        $this->errorBlocId = $errorBlocId;
    }
}
