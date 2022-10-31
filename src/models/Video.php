<?php

namespace Deivz\Aluraflix\models;

class Video
{
    private string $titulo;
    private string $descricao;
    private string $url;

    public function __construct(string $titulo, string $descricao, string $url) {
        $this->titulo = $titulo;
        $this->descricao = $descricao;
        $this->url = $url;
    }

    public function __get($atributo)
    {
        return $this->$atributo;
    }
}