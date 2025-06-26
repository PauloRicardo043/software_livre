<?php
// Página de configurações do administrador
$titulo_pagina = 'Configurações';
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

// Verificar se o usuário está logado
iniciarSessao();
verificarLogin();

// Obter dados do usuário atual
$db = Database::getInstance();
$usuario = $db->fetchOne("SELECT * FROM usuarios WHERE id = ?", [$_SESSION['usuario_id']]);

// Processar formulário de alteração de senha
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'alterar_senha') {
    // Validar token CSRF
    if (!isset($_POST['csrf_token']) || !verificarTokenCSRF($_POST['csrf_token'])) {
        setMensagem('error', 'Erro de validação do formulário. Por favor, tente novamente.');
        header('Location: configuracoes.php');
        exit;
    }
    
    // Validar dados
    $senha_atual = isset($_POST['senha_atual']) ? $_POST['senha_atual'] : '';
    $nova_senha = isset($_POST['nova_senha']) ? $_POST['nova_senha'] : '';
    $confirmar_senha = isset($_POST['confirmar_senha']) ? $_POST['confirmar_senha'] : '';
    
    $erros = [];
    
    // Validações básicas
    if (empty($senha_atual)) {
        $erros[] = 'A senha atual é obrigatória.';
    } elseif (!verificarSenha($senha_atual, $usuario['senha'])) {
        $erros[] = 'Senha atual incorreta.';
    }
    
    if (empty($nova_senha)) {
        $erros[] = 'A nova senha é obrigatória.';
    } elseif (strlen($nova_senha) < 6) {
        $erros[] = 'A nova senha deve ter pelo menos 6 caracteres.';
    }
    
    if ($nova_senha !== $confirmar_senha) {
        $erros[] = 'As senhas não coincidem.';
    }
    
    // Se não houver erros, atualizar a senha
    if (empty($erros)) {
        try {
            // Gerar hash da nova senha
            $hash = gerarHash($nova_senha);
            
            // Atualizar senha no banco de dados
            $db->update('usuarios', ['senha' => $hash], 'id = ?', [$_SESSION['usuario_id']]);
            
            // Registrar log
            registrarLog('alteracao_senha', 'Senha alterada com sucesso', $_SESSION['usuario_id']);
            
            // Mensagem de sucesso
            setMensagem('success', 'Senha alterada com sucesso!');
            
            // Redirecionar para evitar reenvio do formulário
            header('Location: configuracoes.php');
            exit;
        } catch (Exception $e) {
            setMensagem('error', 'Erro ao alterar senha. Por favor, tente novamente.');
        }
    } else {
        // Exibir erros
        setMensagem('error', implode('<br>', $erros));
    }
}

// CSS adicional
$css_adicional = ['admin.css'];

// Incluir cabeçalho administrativo
require_once '../includes/admin_header.php';
?>

    <!-- Conteúdo Principal -->
    <div class="admin-content">
        <div class="admin-header">
            <h2>Configurações</h2>
            <p>Gerencie suas configurações de conta e sistema</p>
        </div>
        
        <div class="admin-tabs">
            <ul class="tabs-nav">
                <li class="active"><a href="#tab-perfil">Perfil</a></li>
                <li><a href="#tab-senha">Alterar Senha</a></li>
            </ul>
            
            <div class="tabs-content">
                <!-- Tab Perfil -->
                <div id="tab-perfil" class="tab-pane active">
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h3>Informações do Perfil</h3>
                        </div>
                        <div class="admin-card-body">
                            <div class="profile-info">
                                <div class="profile-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="profile-details">
                                    <h3><?php echo sanitizar($usuario['nome']); ?></h3>
                                    <p><strong>Usuário:</strong> <?php echo sanitizar($usuario['usuario']); ?></p>
                                    <p><strong>Email:</strong> <?php echo sanitizar($usuario['email']); ?></p>
                                    <p><strong>Nível de Acesso:</strong> <?php echo ucfirst(sanitizar($usuario['nivel_acesso'])); ?></p>
                                    <p><strong>Data de Cadastro:</strong> <?php echo formatarData($usuario['data_cadastro']); ?></p>
                                    <p><strong>Último Acesso:</strong> <?php echo formatarData($usuario['ultimo_acesso']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tab Alterar Senha -->
                <div id="tab-senha" class="tab-pane">
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h3>Alterar Senha</h3>
                        </div>
                        <div class="admin-card-body">
                            <form action="configuracoes.php" method="post" id="changePasswordForm">
                                <input type="hidden" name="csrf_token" value="<?php echo gerarTokenCSRF(); ?>">
                                <input type="hidden" name="acao" value="alterar_senha">
                                
                                <div class="form-group">
                                    <label for="senha_atual">Senha Atual</label>
                                    <input type="password" id="senha_atual" name="senha_atual" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="nova_senha">Nova Senha</label>
                                    <input type="password" id="nova_senha" name="nova_senha" required>
                                    <small>A senha deve ter pelo menos 6 caracteres.</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="confirmar_senha">Confirmar Nova Senha</label>
                                    <input type="password" id="confirmar_senha" name="confirmar_senha" required>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Alterar Senha</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Tab Cadastro de Clientes -->
                <div id="tab-clientes" class="tab-pane">
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h3>Cadastro de Clientes</h3>
                            <a href="cliente-cadastrar.php" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Novo Cliente</a>
                        </div>
                        <div class="admin-card-body">
                            <div class="admin-search">
                                <input type="text" id="searchClienteInput" placeholder="Buscar cliente...">
                                <button type="button"><i class="fas fa-search"></i></button>
                            </div>
                            
                            <?php
                            // Obter lista de clientes
                            $clientes = $db->fetchAll("SELECT * FROM clientes ORDER BY nome");
                            ?>
                            
                            <?php if (empty($clientes)): ?>
                                <div class="empty-state">
                                    <i class="fas fa-users"></i>
                                    <h3>Nenhum cliente cadastrado</h3>
                                    <p>Cadastre novos clientes para gerenciar seus dados e orçamentos.</p>
                                    <a href="cliente-cadastrar.php" class="btn btn-primary">Cadastrar Cliente</a>
                                </div>
                            <?php else: ?>
                                <table class="admin-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Email</th>
                                            <th>Telefone</th>
                                            <th>Data de Cadastro</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($clientes as $cliente): ?>
                                            <tr>
                                                <td>#<?php echo $cliente['id']; ?></td>
                                                <td><?php echo sanitizar($cliente['nome']); ?></td>
                                                <td><?php echo sanitizar($cliente['email']); ?></td>
                                                <td><?php echo sanitizar($cliente['telefone']); ?></td>
                                                <td><?php echo formatarData($cliente['data_cadastro']); ?></td>
                                                <td class="actions">
                                                    <a href="cliente-visualizar.php?id=<?php echo $cliente['id']; ?>" class="btn-icon" title="Visualizar">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="cliente-editar.php?id=<?php echo $cliente['id']; ?>" class="btn-icon" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="cliente-excluir.php?id=<?php echo $cliente['id']; ?>" class="btn-icon btn-delete" title="Excluir">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript para as abas -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Funcionalidade das abas
            const tabLinks = document.querySelectorAll('.tabs-nav a');
            const tabPanes = document.querySelectorAll('.tab-pane');
            
            tabLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Remover classe active de todas as abas
                    tabLinks.forEach(function(link) {
                        link.parentElement.classList.remove('active');
                    });
                    
                    // Adicionar classe active à aba clicada
                    this.parentElement.classList.add('active');
                    
                    // Mostrar o conteúdo da aba
                    const targetId = this.getAttribute('href');
                    
                    tabPanes.forEach(function(pane) {
                        pane.classList.remove('active');
                    });
                    
                    document.querySelector(targetId).classList.add('active');
                });
            });
            
            // Verificar se há um hash na URL para ativar a aba correspondente
            if (window.location.hash) {
                const hash = window.location.hash;
                const tabLink = document.querySelector(`.tabs-nav a[href="${hash}"]`);
                
                if (tabLink) {
                    tabLink.click();
                }
            }
        });
    </script>

<?php
// Incluir rodapé administrativo
require_once '../includes/admin_footer.php';
?>
