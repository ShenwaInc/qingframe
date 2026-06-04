<?php

namespace App\Services;

class ExceptionService extends MicroService {

    public $errno = -1;
    public $enabled = false;

    public function __construct($error, $errno = -1)
    {
        parent::__construct('Exception');
        $this->error = $error;
        $this->errno = $errno;
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        return array(
            'errno' => $this->errno,
            'message' => $this->error
        );
    }

}
