<?php

namespace Deivz\Aluraflix\controllers;

use PDO;

class ConnectionController
{
    public function __construct(private $caminho) {
        
    }

    public function connect()
    {
        return new PDO('sqlite:' . $this->caminho);
    }
}