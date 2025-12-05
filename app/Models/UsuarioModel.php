<?php

namespace App\Models;

use \mysqli;
use \DateTime;

class UsuarioModel
{

    protected $conexao;

    public function __construct(mysqli $conexao)
    {
        $this->conexao = $conexao;
    }

    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT id, nome, email, senha FROM usuarios WHERE email = ?";
        if ($stmt = $this->conexao->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $stmt->close();
            return $resultado->num_rows === 1 ? $resultado->fetch_assoc() : null;
        }
        return null;
    }
    public function savePasswordResetToken(int $user_id, string $token, string $expira_em): bool
    {
        $sql_delete = "DELETE FROM password_resets WHERE usuario_id = ?";
        $stmt_delete = $this->conexao->prepare($sql_delete);
        if (!$stmt_delete) {
            error_log("Erro no prepare: " . $this->conexao->error);
            return false;
        }
        $stmt_delete->bind_param("i", $user_id);
        $stmt_delete->execute();
        $stmt_delete->close();

        $sql_insert = "INSERT INTO password_resets (usuario_id, token, expira_em) VALUES (?, ?, ?)";
        $stmt_insert = $this->conexao->prepare($sql_insert);
        if (!$stmt_insert) {
            error_log("Erro no prepare: " . $this->conexao->error);
            return false;
        }

        $stmt_insert->bind_param("iss", $user_id, $token, $expira_em);
        $success = $stmt_insert->execute();
        $stmt_insert->close();

        return $success;
    }
    public function findDadosById(int $id): ?array
    {
        $sql = "SELECT nome, email, telefone, endereco FROM usuarios WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $stmt->close();
            return $resultado->num_rows === 1 ? $resultado->fetch_assoc() : null;
        }
        return null;
    }

    public function findAdminByEmail(string $email): ?array
    {
        $sql = "SELECT id, nome, email, senha, nivel_acesso 
                FROM usuarios 
                WHERE email = ? AND nivel_acesso = 'admin'";

        if ($stmt = $this->conexao->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $stmt->close();

            if ($resultado->num_rows === 1) {
                return $resultado->fetch_assoc();
            }
        }
        return null;
    }

    public function updateDados(int $id, string $nome, string $email, string $telefone, string $endereco): bool
    {
        $sql_update = "UPDATE usuarios SET nome = ?, email = ?, telefone = ?, endereco = ? WHERE id = ?";
        $stmt_update = $this->conexao->prepare($sql_update);

        if ($stmt_update) {
            $stmt_update->bind_param("ssssi", $nome, $email, $telefone, $endereco, $id);
            $sucesso = $stmt_update->execute();
            $stmt_update->close();
            return $sucesso;
        }
        return false;
    }

    public function updateSenha(int $id, string $senha_atual, string $nova_senha): string
    {
        $sql_check = "SELECT senha FROM usuarios WHERE id = ?";
        $stmt_check = $this->conexao->prepare($sql_check);
        if (!$stmt_check)
            return 'erro_db';

        $stmt_check->bind_param("i", $id);
        $stmt_check->execute();
        $usuario = $stmt_check->get_result()->fetch_assoc();
        $stmt_check->close();

        if ($usuario && password_verify($senha_atual, $usuario['senha'])) {
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

            $sql_update = "UPDATE usuarios SET senha = ? WHERE id = ?";
            $stmt_update = $this->conexao->prepare($sql_update);
            if (!$stmt_update)
                return 'erro_db';

            $stmt_update->bind_param("si", $senha_hash, $id);
            if ($stmt_update->execute()) {
                $stmt_update->close();
                return 'sucesso';
            } else {
                $stmt_update->close();
                return 'erro_db';
            }
        } else {
            return 'senha_invalida';
        }
    }

    public function getClientesComContagemPedidos(string $termo_busca = ''): array
    {
        $usuarios = [];
        $params = [];
        $types = '';

        $where_clause = "WHERE u.nivel_acesso != 'admin'";

        if (!empty($termo_busca)) {
            $where_clause .= " AND (u.nome LIKE ? OR u.email LIKE ?)";
            $like_termo = "%" . $termo_busca . "%";
            $params[] = $like_termo;
            $params[] = $like_termo;
            $types = "ss";
        }

        $sql = "SELECT 
                    u.id, 
                    u.nome, 
                    u.email, 
                    u.telefone, 
                    u.data_cadastro,
                    (SELECT COUNT(p.id) FROM pedidos p WHERE p.usuario_id = u.id) AS total_pedidos
                FROM usuarios u
                {$where_clause}
                ORDER BY u.id DESC";

        $stmt = $this->conexao->prepare($sql);

        if ($stmt) {
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $resultado = $stmt->get_result();
            $usuarios = $resultado->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        }

        return $usuarios;
    }

    public function getAdmins(): array
    {
        $admins = [];
        $sql = "SELECT id, nome, email, nivel_acesso, data_cadastro 
                FROM usuarios 
                WHERE nivel_acesso = 'admin' 
                ORDER BY nome";

        if ($resultado = $this->conexao->query($sql)) {
            $admins = $resultado->fetch_all(MYSQLI_ASSOC);
            $resultado->free();
        }
        return $admins;
    }

    public function createUser(string $nome, string $email, string $senha_plana, string $nivel_acesso = 'cliente'): bool
    {

        $senha_hash = password_hash($senha_plana, PASSWORD_DEFAULT);
        if ($senha_hash === false)
            return false;

        $sql_insert = "INSERT INTO usuarios (nome, email, senha, nivel_acesso) 
                       VALUES (?, ?, ?, ?)";

        if ($stmt_insert = $this->conexao->prepare($sql_insert)) {
            $stmt_insert->bind_param("ssss", $nome, $email, $senha_hash, $nivel_acesso);
            $sucesso = $stmt_insert->execute();
            $stmt_insert->close();
            return $sucesso;
        }
        return false;
    }

    public function deleteUser(int $id): bool
    {
        if ($id === 1) {
            return false;
        }

        $sql = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $sucesso = $stmt->execute();
        $stmt->close();
        return $sucesso;
    }
    public function validatePasswordResetToken(string $email, string $token): bool
    {
        $sql = "SELECT pr.expira_em, u.id as usuario_id
                FROM password_resets pr
                JOIN usuarios u ON pr.usuario_id = u.id
                WHERE u.email = ? AND pr.token = ?";

        $stmt = $this->conexao->prepare($sql);
        if (!$stmt) {
            error_log("Erro no prepare: " . $this->conexao->error);
            return false;
        }

        $stmt->bind_param("ss", $email, $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();

        if (!$data) {
            return false;
        }

        try {
            $expiracao = new DateTime($data['expira_em']);
            $agora = new DateTime();

            return $expiracao > $agora;

        } catch (\Exception $e) {
            error_log("Erro ao processar data de expiração: " . $e->getMessage());
            return false;
        }
    }
    public function updatePasswordAndInvalidateToken(string $email, string $nova_senha, string $token): bool
    {

        $this->conexao->begin_transaction();

        try {
            $hash_senha = password_hash($nova_senha, PASSWORD_DEFAULT);

            $sql_update_user = "UPDATE usuarios SET senha = ? WHERE email = ?";
            $stmt_update_user = $this->conexao->prepare($sql_update_user);
            if (!$stmt_update_user)
                throw new \Exception("Erro no prepare (UPDATE USER): " . $this->conexao->error);

            $stmt_update_user->bind_param("ss", $hash_senha, $email);
            $stmt_update_user->execute();

            if ($stmt_update_user->affected_rows === 0) {
            }
            $stmt_update_user->close();

            $sql_delete_token = "DELETE pr FROM password_resets pr JOIN usuarios u ON pr.usuario_id = u.id WHERE u.email = ? AND pr.token = ?";
            $stmt_delete_token = $this->conexao->prepare($sql_delete_token);
            if (!$stmt_delete_token)
                throw new \Exception("Erro no prepare (DELETE TOKEN): " . $this->conexao->error);

            $stmt_delete_token->bind_param("ss", $email, $token);
            $stmt_delete_token->execute();
            $stmt_delete_token->close();

            $this->conexao->commit();
            return true;

        } catch (\Exception $e) {
            $this->conexao->rollback();
            error_log("Erro na redefinição de senha: " . $e->getMessage());
            return false;
        }

    }
    public function validarTokenReset($email, $token)
    {
        $sql = "SELECT pr.expira_em 
            FROM password_resets pr
            JOIN usuarios u ON u.id = pr.usuario_id
            WHERE u.email = ? AND pr.token = ?";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("ss", $email, $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            return false;
        }

        $data = $result->fetch_assoc();
        return strtotime($data['expira_em']) > time();
    }


    public function atualizarSenha($email, $novaSenha)
    {
        $hash = password_hash($novaSenha, PASSWORD_DEFAULT);

        $sql = "UPDATE usuarios SET senha = ? WHERE email = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("ss", $hash, $email);
        $stmt->execute();
        $stmt->close();

        $sql2 = "DELETE pr FROM password_resets pr 
             JOIN usuarios u ON u.id = pr.usuario_id
             WHERE u.email = ?";
        $stmt2 = $this->conexao->prepare($sql2);
        $stmt2->bind_param("s", $email);
        return $stmt2->execute();
    }
    public function limparTokenReset($token)
    {
        $sql = "UPDATE usuarios SET token_reset = NULL WHERE token_reset = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("s", $token);
        return $stmt->execute();
    }
    public function findById(int $id): ?array
    {
        $sql = "SELECT id, nome, email, telefone, endereco, nivel_acesso, data_cadastro FROM usuarios WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $stmt->close();
            return $resultado->num_rows === 1 ? $resultado->fetch_assoc() : null;
        }
        return null;
    }
}