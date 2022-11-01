<?php

namespace Deivz\Aluraflix\models;

class Video
{
    private string $titulo;
    private string $descricao;
    private string $url;

    public function __construct(array $dadosRequisicao)
    {

        $erros = $this->verificarDados($dadosRequisicao);

        if (empty($erros)) {
            $this->titulo = $dadosRequisicao['titulo'];
            $this->descricao = $dadosRequisicao['descricao'];
            $this->url = $dadosRequisicao['url'];
        } else {
            http_response_code(422);
            echo json_encode(["erros" => $erros], JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    public function __get($atributo)
    {
        return $this->$atributo;
    }

    private function verificarDados(array $dadosRequisicao): array
    {
        $erros = [];

        if (empty($dadosRequisicao['titulo'])) {
            $erros[] = "O campo título não pode estar vazio!";
        }

        if (empty($dadosRequisicao['descricao'])) {
            $erros[] = "O campo descrição não pode estar vazio!";
        }

        if (empty($dadosRequisicao['url'])) {
            $erros[] = "O campo url não pode estar vazio!";
        }

        return $erros;
    }
}
