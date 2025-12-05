<?php

namespace App\Models;
use \mysqli;

class RelatorioModel
{

    protected $conexao;

    public function __construct(mysqli $conexao)
    {
        $this->conexao = $conexao;
    }

    public function contarTotalClientes(): int
    {
        $sql = "SELECT COUNT(id) AS total FROM usuarios WHERE nivel_acesso != 'admin'";
        $resultado = $this->conexao->query($sql);
        $dados = $resultado->fetch_assoc();
        return (int) ($dados['total'] ?? 0);
    }

    public function contarTotalPedidos(string $status = 'Todos'): int
    {
        $sql = "SELECT COUNT(id) AS total FROM pedidos";
        if ($status !== 'Todos') {
            $sql .= " WHERE status = ?";
        }

        $stmt = $this->conexao->prepare($sql);
        if ($status !== 'Todos') {
            $stmt->bind_param("s", $status);
        }
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return (int) ($resultado['total'] ?? 0);
    }

    public function getVolumeTotalVendas(): float
    {
        $sql = "SELECT SUM(total_geral) AS faturamento FROM pedidos WHERE status != 'Cancelado'";
        $resultado = $this->conexao->query($sql);
        $dados = $resultado->fetch_assoc();
        return (float) ($dados['faturamento'] ?? 0.0);
    }

    public function contarTotalEstoque(): int
    {
        $sql = "SELECT SUM(estoque) AS total FROM produto_variacoes";
        $resultado = $this->conexao->query($sql);
        $dados = $resultado->fetch_assoc();
        return (int) ($dados['total'] ?? 0);
    }
    public function getPedidosPorStatus(): array
    {
        $sql = "SELECT status, COUNT(*) as total 
                FROM pedidos 
                GROUP BY status";
        $resultado = $this->conexao->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    public function getVendasUltimos6Meses(): array
    {

        $sql = "SELECT DATE_FORMAT(data_pedido, '%Y-%m') as mes, SUM(total_geral) as total
                FROM pedidos
                WHERE status != 'Cancelado'
                GROUP BY mes
                ORDER BY mes DESC
                LIMIT 6";

        $resultado = $this->conexao->query($sql);
        $dados = $resultado->fetch_all(MYSQLI_ASSOC);

        return array_reverse($dados);
    }

    public function getNovosClientesUltimos6Meses(): array
{
    $meses = [];
    for ($i = 5; $i >= 0; $i--) {
        $meses[] = date('Y-m', strtotime("-$i months"));
    }
    $selects = [];
    foreach ($meses as $mes) {
        $selects[] = "SELECT '" . $this->conexao->real_escape_string($mes) . "' AS mes";
    }
    $mesesSubquery = implode(' UNION ALL ', $selects);

    $sql = "
        SELECT m.mes,
               COUNT(u.id) AS total
        FROM (
            {$mesesSubquery}
        ) AS m
        LEFT JOIN usuarios u
            ON DATE_FORMAT(u.data_cadastro, '%Y-%m') = m.mes
            AND u.nivel_acesso != 'admin'
        GROUP BY m.mes
        ORDER BY m.mes ASC
    ";

    $resultado = $this->conexao->query($sql);

    if (!$resultado) {
        throw new \Exception('SQL Error: ' . $this->conexao->error . ' --- Query: ' . $sql);
    }

    return $resultado->fetch_all(MYSQLI_ASSOC);
}


    public function getTopProdutosEstoque(int $limite = 47): array
    {
        $sql = "SELECT p.nome, SUM(pv.estoque) as total_estoque
                FROM produtos p
                LEFT JOIN produto_variacoes pv ON p.id = pv.produto_id
                GROUP BY p.id, p.nome
                ORDER BY total_estoque DESC
                LIMIT ?";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("i", $limite);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $resultado;
    }
}