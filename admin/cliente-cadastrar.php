<?php
// Página de cadastro de cliente
$titulo_pagina = 'Cadastrar Cliente';
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/db.php';

// Verificar se o usuário está logado
iniciarSessao();
verificarLogin();

// Processar o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar token CSRF
    if (!isset($_POST['csrf_token']) || !verificarTokenCSRF($_POST['csrf_token'])) {
        setMensagem('error', 'Erro de validação do formulário. Por favor, tente novamente.');
        header('Location: cliente-cadastrar.php');
        exit;
    }
    
    // Validar dados
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : '';
    $endereco = isset($_POST['endereco']) ? trim($_POST['endereco']) : '';
    $cidade = isset($_POST['cidade']) ? trim($_POST['cidade']) : '';
    $estado = isset($_POST['estado']) ? trim($_POST['estado']) : '';
    $cep = isset($_POST['cep']) ? trim($_POST['cep']) : '';
    
    $erros = [];
    
    // Validações básicas
    if (empty($nome)) {
        $erros[] = 'O nome é obrigatório.';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'Email inválido.';
    }
    
    if (empty($telefone)) {
        $erros[] = 'O telefone é obrigatório.';
    }
    
    // Verificar se o email já está cadastrado
    $db = Database::getInstance();
    $cliente_existente = $db->fetchOne("SELECT id FROM clientes WHERE email = ?", [$email]);
    
    if ($cliente_existente) {
        $erros[] = 'Este email já está cadastrado para outro cliente.';
    }
    
    // Se não houver erros, salvar no banco de dados
    if (empty($erros)) {
        try {
            // Inserir cliente
            $cliente_id = $db->insert('clientes', [
                'nome' => $nome,
                'email' => $email,
                'telefone' => $telefone,
                'endereco' => $endereco,
                'cidade' => $cidade,
                'estado' => $estado,
                'cep' => $cep,
                'data_cadastro' => date('Y-m-d')
            ]);
            
            // Registrar log
            registrarLog('cadastro_cliente', 'Cliente #' . $cliente_id . ' cadastrado', $_SESSION['usuario_id']);
            
            // Mensagem de sucesso
            setMensagem('success', 'Cliente cadastrado com sucesso!');
            
            // Redirecionar para a página de visualização
            header('Location: cliente-visualizar.php?id=' . $cliente_id);
            exit;
        } catch (Exception $e) {
            setMensagem('error', 'Erro ao cadastrar cliente. Por favor, tente novamente.');
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
            <h2>Cadastrar Cliente</h2>
            <p>Adicione um novo cliente ao sistema</p>
        </div>
        
        <div class="admin-actions">
            <a href="configuracoes.php#tab-clientes" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar para Lista</a>
        </div>
        
        <div class="admin-card">
            <div class="admin-card-header">
                <h3>Informações do Cliente</h3>
            </div>
            <div class="admin-card-body">
                <form action="cliente-cadastrar.php" method="post" id="clienteForm">
                    <input type="hidden" name="csrf_token" value="<?php echo gerarTokenCSRF(); ?>">
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="nome">Nome Completo</label>
                            <input type="text" id="nome" name="nome" required value="<?php echo isset($_POST['nome']) ? sanitizar($_POST['nome']) : ''; ?>">
                        </div>
                        
                        <div class="form-group col-md-6">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? sanitizar($_POST['email']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="telefone">Telefone</label>
                            <input type="tel" id="telefone" name="telefone" required value="<?php echo isset($_POST['telefone']) ? sanitizar($_POST['telefone']) : ''; ?>">
                        </div>
                        
                        <div class="form-group col-md-6">
                            <label for="cep">CEP</label>
                            <input type="text" id="cep" name="cep" value="<?php echo isset($_POST['cep']) ? sanitizar($_POST['cep']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="endereco">Endereço</label>
                        <input type="text" id="endereco" name="endereco" value="<?php echo isset($_POST['endereco']) ? sanitizar($_POST['endereco']) : ''; ?>">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label for="cidade">Cidade</label>
                            <input type="text" id="cidade" name="cidade" value="<?php echo isset($_POST['cidade']) ? sanitizar($_POST['cidade']) : ''; ?>">
                        </div>
                        
                        <div class="form-group col-md-4">
                            <label for="estado">Estado</label>
                            <select id="estado" name="estado">
                                <option value="">Selecione...</option>
                                <option value="AC" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'AC') ? 'selected' : ''; ?>>AC</option>
                                <option value="AL" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'AL') ? 'selected' : ''; ?>>AL</option>
                                <option value="AP" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'AP') ? 'selected' : ''; ?>>AP</option>
                                <option value="AM" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'AM') ? 'selected' : ''; ?>>AM</option>
                                <option value="BA" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'BA') ? 'selected' : ''; ?>>BA</option>
                                <option value="CE" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'CE') ? 'selected' : ''; ?>>CE</option>
                                <option value="DF" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'DF') ? 'selected' : ''; ?>>DF</option>
                                <option value="ES" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'ES') ? 'selected' : ''; ?>>ES</option>
                                <option value="GO" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'GO') ? 'selected' : ''; ?>>GO</option>
                                <option value="MA" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'MA') ? 'selected' : ''; ?>>MA</option>
                                <option value="MT" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'MT') ? 'selected' : ''; ?>>MT</option>
                                <option value="MS" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'MS') ? 'selected' : ''; ?>>MS</option>
                                <option value="MG" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'MG') ? 'selected' : ''; ?>>MG</option>
                                <option value="PA" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'PA') ? 'selected' : ''; ?>>PA</option>
                                <option value="PB" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'PB') ? 'selected' : ''; ?>>PB</option>
                                <option value="PR" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'PR') ? 'selected' : ''; ?>>PR</option>
                                <option value="PE" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'PE') ? 'selected' : ''; ?>>PE</option>
                                <option value="PI" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'PI') ? 'selected' : ''; ?>>PI</option>
                                <option value="RJ" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'RJ') ? 'selected' : ''; ?>>RJ</option>
                                <option value="RN" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'RN') ? 'selected' : ''; ?>>RN</option>
                                <option value="RS" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'RS') ? 'selected' : ''; ?>>RS</option>
                                <option value="RO" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'RO') ? 'selected' : ''; ?>>RO</option>
                                <option value="RR" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'RR') ? 'selected' : ''; ?>>RR</option>
                                <option value="SC" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'SC') ? 'selected' : ''; ?>>SC</option>
                                <option value="SP" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'SP') ? 'selected' : ''; ?>>SP</option>
                                <option value="SE" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'SE') ? 'selected' : ''; ?>>SE</option>
                                <option value="TO" <?php echo (isset($_POST['estado']) && $_POST['estado'] == 'TO') ? 'selected' : ''; ?>>TO</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Cadastrar Cliente</button>
                        <a href="configuracoes.php#tab-clientes" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php
// Incluir rodapé administrativo
require_once '../includes/admin_footer.php';
?>
