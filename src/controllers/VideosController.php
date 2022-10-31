<?php

namespace Deivz\Aluraflix\controllers;

use PDO;
use Deivz\Aluraflix\controllers\ConnectionController;
use Deivz\Aluraflix\models\Video;

class VideosController
{
    private PDO $connection;

    public function __construct(ConnectionController $connection)
    {
        $this->connection = $connection->connect();
    }

    public function identifyRequest(string $requestMethod, ?int $id)
    {
        switch ($requestMethod) {
            case ('GET'):
                if (!isset($id)) {
                    $videos = json_encode($this->getVideos(), JSON_UNESCAPED_UNICODE);
                    if ($videos) {
                        echo $videos;
                        http_response_code(200);
                        break;
                    }
                }

                $video = json_encode($this->getVideosById($id), JSON_UNESCAPED_UNICODE);
                echo $video;
                http_response_code(200);
                break;

            case 'POST':
                $dadosRequisicao = (array) json_decode(file_get_contents("php://input"), true);

                try {
                    $video = new Video($dadosRequisicao['titulo'], $dadosRequisicao['descricao'], $dadosRequisicao['url']);
                } catch (\Throwable $th) {
                    echo $th->getMessage();
                }
                
                $idRequisicao = $this->postVideo($video);
                http_response_code(201);
                echo json_encode([
                    'id' => $idRequisicao,
                    'mensagem' => 'Video inserido com sucesso'
                ]);
                break;
        }
    }

    public function getVideos(): array
    {
        $sql = 'SELECT * FROM videos';
        $stmt = $this->connection->query($sql);
        $videos = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $videos[] = $row;
        }

        return $videos;
    }

    public function getVideosById(int $id): array|false
    {
        $sql = 'SELECT * FROM videos WHERE id = :id';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function postVideo(Video $video): int
    {
        $sql = 'INSERT INTO videos (titulo, descricao, url) VALUES (:titulo, :descricao, :url);';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            ':titulo' => $video->titulo,
            ':descricao' => $video->descricao,
            ':url' => $video->url,
        ]);
        return $this->connection->lastInsertId();
    }
}
