<?php
// Página de orçamento com envio para o banco de dados
$titulo_pagina = 'Solicitar Orçamento';
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/db.php';

// Obter serviços do banco de dados
$db = Database::getInstance();
$servicos = $db->fetchAll("SELECT * FROM servicos WHERE ativo = 1 ORDER BY nome");

// Verificar se foi enviado um serviço específico
$servico_selecionado = isset($_GET['servico']) ? (int)$_GET['servico'] : null;

// Processar o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar token CSRF
    if (!isset($_POST['csrf_token']) || !verificarTokenCSRF($_POST['csrf_token'])) {
        setMensagem('error', 'Erro de validação do formulário. Por favor, tente novamente.');
        header('Location: orcamento.php');
        exit;
    }
    
    // Validar dados
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : '';
    $mensagem = isset($_POST['mensagem']) ? trim($_POST['mensagem']) : '';
    $servicos_selecionados = isset($_POST['servicos']) ? $_POST['servicos'] : [];
    
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
    
    if (empty($servicos_selecionados)) {
        $erros[] = 'Selecione pelo menos um serviço.';
    }
    
    // Verificar upload de arquivos
    $arquivos_salvos = [];
    if (!empty($_FILES['arquivos']['name'][0])) {
        $upload_dir = 'uploads/orcamentos/';
        
        // Criar diretório se não existir
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Processar cada arquivo
        $total_arquivos = count($_FILES['arquivos']['name']);
        
        for ($i = 0; $i < $total_arquivos; $i++) {
            if ($_FILES['arquivos']['error'][$i] === UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['arquivos']['tmp_name'][$i];
                $nome_arquivo = $_FILES['arquivos']['name'][$i];
                $extensao = strtolower(pathinfo($nome_arquivo, PATHINFO_EXTENSION));
                
                // Verificar extensão permitida
                $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'];
                
                if (in_array($extensao, $extensoes_permitidas)) {
                    // Gerar nome único para o arquivo
                    $novo_nome = uniqid('arquivo_') . '.' . $extensao;
                    $caminho_arquivo = $upload_dir . $novo_nome;
                    
                    // Mover o arquivo para o diretório de uploads
                    if (move_uploaded_file($tmp_name, $caminho_arquivo)) {
                        $arquivos_salvos[] = [
                            'nome_original' => $nome_arquivo,
                            'nome_sistema' => $novo_nome,
                            'caminho' => $caminho_arquivo
                        ];
                    } else {
                        $erros[] = 'Erro ao fazer upload do arquivo: ' . $nome_arquivo;
                    }
                } else {
                    $erros[] = 'Tipo de arquivo não permitido: ' . $nome_arquivo;
                }
            } elseif ($_FILES['arquivos']['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                $erros[] = 'Erro no upload do arquivo: ' . $_FILES['arquivos']['name'][$i];
            }
        }
    }
    
    // Se não houver erros, salvar no banco de dados
    if (empty($erros)) {
        try {
            // Iniciar transação
            $db->getConnection()->beginTransaction();
            
            // Inserir orçamento
            $orcamento_id = $db->insert('orcamentos', [
                'nome' => $nome,
                'email' => $email,
                'telefone' => $telefone,
                'mensagem' => $mensagem,
                'status' => 'pendente',
                'data_criacao' => date('Y-m-d H:i:s')
            ]);
            
            // Inserir relação com serviços
            foreach ($servicos_selecionados as $servico_id) {
                $db->insert('orcamentos_servicos', [
                    'orcamento_id' => $orcamento_id,
                    'servico_id' => (int)$servico_id
                ]);
            }
            
            // Inserir arquivos anexados
            foreach ($arquivos_salvos as $arquivo) {
                $db->insert('orcamentos_arquivos', [
                    'orcamento_id' => $orcamento_id,
                    'nome_original' => $arquivo['nome_original'],
                    'nome_sistema' => $arquivo['nome_sistema'],
                    'caminho' => $arquivo['caminho'],
                    'data_upload' => date('Y-m-d H:i:s')
                ]);
            }
            
            // Confirmar transação
            $db->getConnection()->commit();
            
            // Mensagem de sucesso
            setMensagem('success', 'Orçamento solicitado com sucesso! Entraremos em contato em breve.');
            
            // Redirecionar para evitar reenvio do formulário
            header('Location: orcamento.php');
            exit;
        } catch (Exception $e) {
            // Reverter transação em caso de erro
            $db->getConnection()->rollBack();
            
            // Remover arquivos salvos em caso de erro
            foreach ($arquivos_salvos as $arquivo) {
                if (file_exists($arquivo['caminho'])) {
                    unlink($arquivo['caminho']);
                }
            }
            
            setMensagem('error', 'Erro ao solicitar orçamento. Por favor, tente novamente.');
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
            <h1>Solicite um Orçamento</h1>
            <p>Preencha o formulário abaixo e receba um orçamento personalizado</p>
        </div>
    </section>

    <!-- Formulário de Orçamento -->
    <section class="quote-section">
        <div class="container">
            <div class="quote-container">
                <h2>Formulário de Orçamento</h2>
                <p>Preencha os campos abaixo para receber um orçamento personalizado para os serviços que você precisa.</p>
                
                <form action="orcamento.php" method="post" id="quoteForm" enctype="multipart/form-data">
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
                    
                    <div class="services-checkbox">
                        <h3>Serviços de Interesse</h3>
                        <div class="checkbox-group">
                            <?php foreach ($servicos as $servico): ?>
                            <div class="checkbox-item">
                                <input type="checkbox" id="servico<?php echo $servico['id']; ?>" name="servicos[]" value="<?php echo $servico['id']; ?>" <?php echo ($servico_selecionado == $servico['id'] || (isset($_POST['servicos']) && in_array($servico['id'], $_POST['servicos']))) ? 'checked' : ''; ?>>
                                <label for="servico<?php echo $servico['id']; ?>"><?php echo sanitizar($servico['nome']); ?></label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="form-group file-upload-container">
                        <label for="arquivos">Anexar Arquivos (opcional)</label>
                        <div class="file-upload-wrapper">
                            <div class="file-upload-info">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Arraste arquivos aqui ou clique para selecionar</p>
                                <span>Formatos aceitos: JPG, PNG, PDF, DOC, XLS, TXT (máx. 5MB)</span>
                            </div>
                            <input type="file" id="arquivos" name="arquivos[]" multiple class="file-upload-input">
                        </div>
                        <div class="file-list" id="fileList"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="mensagem">Detalhes Adicionais</label>
                        <textarea id="mensagem" name="mensagem" placeholder="Descreva detalhes específicos sobre o serviço que você precisa..."><?php echo isset($_POST['mensagem']) ? sanitizar($_POST['mensagem']) : ''; ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn">Solicitar Orçamento</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Seção de Benefícios -->
    <section class="benefits-section">
        <div class="container">
            <div class="section-title">
                <h2>Por que solicitar um orçamento conosco?</h2>
                <p>Conheça os benefícios de trabalhar com a IDEA Service</p>
            </div>
            
            <div class="benefits-grid">
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3>Orçamento Gratuito</h3>
                    <p>Solicite seu orçamento sem nenhum custo ou compromisso.</p>
                </div>
                
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3>Resposta Rápida</h3>
                    <p>Respondemos todos os orçamentos em até 24 horas úteis.</p>
                </div>
                
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3>Qualidade Garantida</h3>
                    <p>Todos os nossos serviços possuem garantia de qualidade.</p>
                </div>
                
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <h3>Preços Competitivos</h3>
                    <p>Oferecemos o melhor custo-benefício do mercado.</p>
                </div>
            </div>
        </div>
    </section>

<style>
/* Estilos para upload de arquivos */
.file-upload-container {
    margin-bottom: 30px;
}

.file-upload-wrapper {
    border: 2px dashed var(--border-color);
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    position: relative;
    cursor: pointer;
    transition: all 0.3s ease;
    background-color: #f9f9f9;
}

.file-upload-wrapper:hover {
    border-color: var(--primary-color);
    background-color: #f0f0f0;
}

.file-upload-info {
    pointer-events: none;
}

.file-upload-info i {
    font-size: 2.5rem;
    color: var(--primary-color);
    margin-bottom: 10px;
}

.file-upload-info p {
    margin-bottom: 5px;
    font-weight: 500;
}

.file-upload-info span {
    font-size: 0.8rem;
    color: var(--admin-text-light);
}

.file-upload-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.file-list {
    margin-top: 15px;
}

.file-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background-color: #f0f0f0;
    padding: 10px 15px;
    border-radius: 4px;
    margin-bottom: 8px;
}

.file-info {
    display: flex;
    align-items: center;
}

.file-icon {
    margin-right: 10px;
    color: var(--primary-color);
}

.file-name {
    font-size: 0.9rem;
    max-width: 200px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.file-size {
    font-size: 0.8rem;
    color: var(--admin-text-light);
    margin-left: 10px;
}

.file-remove {
    color: var(--danger-color);
    cursor: pointer;
    font-size: 1.2rem;
}

/* Responsividade para upload de arquivos */
@media (max-width: 576px) {
    .file-upload-wrapper {
        padding: 15px;
    }
    
    .file-upload-info i {
        font-size: 2rem;
    }
    
    .file-name {
        max-width: 150px;
    }
}
</style>

<script>
// Script para gerenciar upload de arquivos
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('arquivos');
    const fileList = document.getElementById('fileList');
    const maxFileSize = 5 * 1024 * 1024; // 5MB
    
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            fileList.innerHTML = '';
            
            if (this.files.length > 0) {
                Array.from(this.files).forEach(file => {
                    // Verificar tamanho do arquivo
                    if (file.size > maxFileSize) {
                        alert(`O arquivo ${file.name} excede o tamanho máximo permitido de 5MB.`);
                        return;
                    }
                    
                    // Criar elemento para exibir o arquivo
                    const fileItem = document.createElement('div');
                    fileItem.className = 'file-item';
                    
                    // Determinar ícone com base na extensão
                    const extension = file.name.split('.').pop().toLowerCase();
                    let iconClass = 'fa-file';
                    
                    if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
                        iconClass = 'fa-file-image';
                    } else if (['pdf'].includes(extension)) {
                        iconClass = 'fa-file-pdf';
                    } else if (['doc', 'docx'].includes(extension)) {
                        iconClass = 'fa-file-word';
                    } else if (['xls', 'xlsx'].includes(extension)) {
                        iconClass = 'fa-file-excel';
                    } else if (['txt'].includes(extension)) {
                        iconClass = 'fa-file-alt';
                    }
                    
                    // Formatar tamanho do arquivo
                    const fileSize = file.size < 1024 * 1024 
                        ? (file.size / 1024).toFixed(1) + ' KB' 
                        : (file.size / (1024 * 1024)).toFixed(1) + ' MB';
                    
                    fileItem.innerHTML = `
                        <div class="file-info">
                            <div class="file-icon"><i class="fas ${iconClass}"></i></div>
                            <div class="file-name">${file.name}</div>
                            <div class="file-size">${fileSize}</div>
                        </div>
                        <div class="file-remove" data-name="${file.name}"><i class="fas fa-times"></i></div>
                    `;
                    
                    fileList.appendChild(fileItem);
                });
                
                // Adicionar evento para remover arquivos
                document.querySelectorAll('.file-remove').forEach(button => {
                    button.addEventListener('click', function() {
                        const fileName = this.getAttribute('data-name');
                        const dt = new DataTransfer();
                        
                        Array.from(fileInput.files)
                            .filter(file => file.name !== fileName)
                            .forEach(file => dt.items.add(file));
                        
                        fileInput.files = dt.files;
                        this.closest('.file-item').remove();
                    });
                });
            }
        });
    }
});
</script>

<?php
// Incluir rodapé
require_once 'includes/footer.php';
?>
