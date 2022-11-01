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

        // $sql = 'CREATE TABLE videos (id INTEGER PRIMARY KEY, titulo TEXT, descricao TEXT, url TEXT);';
        // $sql = 'DROP TABLE videos;';
        // $this->connection->exec($sql);
    }

    public function identifyRequest(string $requestMethod, ?string $id)
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
                $video = new Video($dadosRequisicao);
                $idRequisicao = $this->postVideo($video);
                http_response_code(201);
                echo json_encode([
                    'id' => $idRequisicao,
                    'mensagem' => 'Video inserido com sucesso'
                ]);
                break;

            case 'PUT':
                if (empty($id)) {
                    http_response_code(404);
                    echo json_encode([
                        'mensagem' => 'Video não encontrado'
                    ], JSON_UNESCAPED_UNICODE);
                    break;
                }
                $dadosRequisicao = (array) json_decode(file_get_contents("php://input"), true);
                $video = new Video($dadosRequisicao);
                $idRequisicao = $this->putVideo($video, $id);
                http_response_code(201);
                echo json_encode([
                    'id' => $idRequisicao,
                    'mensagem' => 'Video atualizado com sucesso'
                ]);
                break;
            
            case 'DELETE':
                if (empty($id)) {
                    http_response_code(404);
                    echo json_encode([
                        'mensagem' => 'Video não encontrado'
                    ], JSON_UNESCAPED_UNICODE);
                    break;
                }
                $linha = $this->deleteVideo($id);
                echo json_encode([
                    'linhas' => $linha,
                    'mensagem' => "Video {$id} excluído com sucesso"
                ], JSON_UNESCAPED_UNICODE);
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

    public function getVideosById($id): array|false
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

    public function putVideo(Video $video, $id): int
    {
        $sql = 'UPDATE videos SET titulo = :titulo, descricao = :descricao, url = :url WHERE id = :id;';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':titulo' => $video->titulo,
            ':descricao' => $video->descricao,
            ':url' => $video->url,
        ]);
        return $stmt->rowCount();
    }

    public function deleteVideo($id): int
    {
        $sql = 'DELETE FROM videos WHERE id = :id;';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            ':id' => $id,
        ]);
        return $stmt->rowCount();
    }
}
