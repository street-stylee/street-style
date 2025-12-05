<?php


namespace App\Models;
use \mysqli;

class CarrosselModel {

    protected $conexao;

    public function __construct(mysqli $conexao) {
        $this->conexao = $conexao;
    }


    public function getSlides(): array {
        $slides = [];
        $sql = "SELECT * FROM carrossel_slides ORDER BY ordem ASC, id DESC";
        if ($resultado = $this->conexao->query($sql)) {
            $slides = $resultado->fetch_all(MYSQLI_ASSOC);
            $resultado->free();
        }
        return $slides;
    }

    public function createSlide(string $imagem_url, ?string $link, ?string $titulo, ?string $subtitulo): bool {
        $sql = "INSERT INTO carrossel_slides (imagem_url, link_url, titulo, subtitulo) VALUES (?, ?, ?, ?)";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("ssss", $imagem_url, $link, $titulo, $subtitulo);
        $sucesso = $stmt->execute();
        $stmt->close();
        return $sucesso;
    }

    public function deleteSlide(int $id): bool {
        $sql = "DELETE FROM carrossel_slides WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $sucesso = $stmt->execute();
        $stmt->close();
        return $sucesso;
    }
}