<?php
// Página de contato
$titulo_pagina = 'Contato';
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/db.php';

// Processar o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar token CSRF
    if (!isset($_POST['csrf_token']) || !verificarTokenCSRF($_POST['csrf_token'])) {
        setMensagem('error', 'Erro de validação do formulário. Por favor, tente novamente.');
        header('Location: contato.php');
        exit;
    }
    
    // Validar dados
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : '';
    $setor = isset($_POST['setor']) ? trim($_POST['setor']) : '';
    $mensagem = isset($_POST['mensagem']) ? trim($_POST['mensagem']) : '';
    
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
    
    if (empty($setor) || !in_array($setor, ['tecnico', 'financeiro', 'assistencia'])) {
        $erros[] = 'Selecione um setor válido.';
    }
    
    if (empty($mensagem)) {
        $erros[] = 'A mensagem é obrigatória.';
    }
    
    // Se não houver erros, salvar no banco de dados
    if (empty($erros)) {
        try {
            $db = Database::getInstance();
            
            // Inserir contato
            $db->insert('contatos', [
                'nome' => $nome,
                'email' => $email,
                'telefone' => $telefone,
                'setor' => $setor,
                'mensagem' => $mensagem,
                'status' => 'novo',
                'data_criacao' => date('Y-m-d H:i:s') // Adiciona data de criação
            ]);
            
            // Mensagem de sucesso
            setMensagem('success', 'Mensagem enviada com sucesso! Entraremos em contato em breve.');
            
            // Redirecionar para evitar reenvio do formulário
            header('Location: contato.php');
            exit;
        } catch (Exception $e) {
            setMensagem('error', 'Erro ao enviar mensagem. Por favor, tente novamente.');
        }
    } else {
        // Exibir erros
        setMensagem('error', implode('<br>', $erros));
    }
}

// Incluir cabeçalho
require_once 'includes/header.php';
?>

    <!-- Banner da Página -->
    <section class="hero-banner">
        <div class="hero-content">
            <h1>Entre em Contato</h1>
            <p>Estamos prontos para atender você</p>
        </div>
    </section>

    <!-- Contato -->
    <section class="contact-section">
        <div class="container">
            <div class="contact-container">
                <div class="contact-info">
                    <h2>Informações de Contato</h2>
                    <ul class="contact-details">
                        <li>
                            <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div class="contact-text">
                                <strong>Endereço</strong>
                                Rua Exemplo, 123 - Centro<br>
                                CEP: 00000-000
                            </div>
                        </li>
                        <li>
                            <div class="contact-icon"><i class="fas fa-phone"></i></div>
                            <div class="contact-text">
                                <strong>Telefone</strong>
                                (11) 99999-9999<br>
                                (11) 88888-8888
                            </div>
                        </li>
                        <li>
                            <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                            <div class="contact-text">
                                <strong>E-mail</strong>
                                contato@ideaservices.com.br<br>
                                suporte@ideaservices.com.br
                            </div>
                        </li>
                        <li>
                            <div class="contact-icon"><i class="fas fa-clock"></i></div>
                            <div class="contact-text">
                                <strong>Horário de Atendimento</strong>
                                Segunda - Sexta: 08:00 - 18:00<br>
                                Sábado: 08:00 - 12:00
                            </div>
                        </li>
                    </ul>
                </div>
                
                <div class="contact-form">
                    <h2>Envie sua Mensagem</h2>
                    <form action="contato.php" method="post" id="contactForm">
                        <input type="hidden" name="csrf_token" value="<?php echo gerarTokenCSRF(); ?>">
                        
                        <div class="form-group">
                            <label for="nome">Nome Completo</label>
                            <input type="text" id="nome" name="nome" required value="<?php echo isset($_POST['nome']) ? sanitizar($_POST['nome']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">E-mail</label>
                            <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? sanitizar($_POST['email']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="telefone">Telefone</label>
                            <input type="tel" id="telefone" name="telefone" required value="<?php echo isset($_POST['telefone']) ? sanitizar($_POST['telefone']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="setor">Setor</label>
                            <select id="setor" name="setor" required>
                                <option value="">Selecione um setor</option>
                                <option value="tecnico" <?php echo (isset($_POST['setor']) && $_POST['setor'] == 'tecnico') ? 'selected' : ''; ?>>Técnico</option>
                                <option value="financeiro" <?php echo (isset($_POST['setor']) && $_POST['setor'] == 'financeiro') ? 'selected' : ''; ?>>Financeiro</option>
                                <option value="assistencia" <?php echo (isset($_POST['setor']) && $_POST['setor'] == 'assistencia') ? 'selected' : ''; ?>>Assistência</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="mensagem">Mensagem</label>
                            <textarea id="mensagem" name="mensagem" required><?php echo isset($_POST['mensagem']) ? sanitizar($_POST['mensagem']) : ''; ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn">Enviar Mensagem</button>
                    </form>
                </div>
            </div>
            
            <div class="direct-contacts">
                <div class="section-title">
                    <h2>Contatos Diretos</h2>
                    <p>Entre em contato diretamente com nossos especialistas</p>
                </div>
                
                <div class="team-grid">
                    <div class="team-member-card">
                        <h3>Paulo Ricardo</h3>
                        <p>Diretor Técnico</p>
                        <div class="team-contact-info">
                            <span><i class="fas fa-phone"></i> (11) 99999-9999</span>
                            <span><i class="fas fa-envelope"></i> pauloricardo@ideaservices.com.br</span>
                        </div>
                    </div>
                    
                    <div class="team-member-card">
                        <h3>Glauco Schneider</h3>
                        <p>Gerente de Projetos</p>
                        <div class="team-contact-info">
                            <span><i class="fas fa-phone"></i> (11) 99999-9999</span>
                            <span><i class="fas fa-envelope"></i> glaucoschneider@ideaservices.com.br</span>
                        </div>
                    </div>
                    
                    <div class="team-member-card">
                        <h3>Caetano Souto</h3>
                        <p>Suporte Técnico</p>
                        <div class="team-contact-info">
                            <span><i class="fas fa-phone"></i> (11) 99999-9999</span>
                            <span><i class="fas fa-envelope"></i> caetanosouto@ideaservices.com.br</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
// Incluir rodapé
require_once 'includes/footer.php';
?>
