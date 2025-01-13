<?php
session_start();
require_once 'conexao.php';

if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // Continua a execução
} else {
    exit('Acesso Negado!');
}
?>

<div class="container">
    <div class="add-emoji-form">
        <h3>Adicionar Novo Emoji</h3>
        <form id="addEmojiForm">
            <input type="text" name="codigo" placeholder="Digite o emoji" required>
            <input type="text" name="significado" placeholder="Digite o significado do emoji" required>
            <button type="submit">Adicionar Emoji</button>
        </form>
    </div>

    <div class="emoji-list">
        <table>
            <tbody>
                <?php
                $sql = "SELECT * FROM emojis ORDER BY id";
                $result = mysqli_query($conn, $sql);
                
while($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td class='emoji-cell'>
            <div class='emoji-action-container'>
                <div class='emoji'>".$row['codigo']."</div>
                <div class='actions-cell'>
                    <button onclick='editEmoji(".$row['id'].")' class='edit-btn'>Editar</button>
                    <button onclick='deleteEmoji(".$row['id'].")' class='delete-btn'>Excluir</button>
                </div>
            </div>
            <div class='significado'>".$row['significado']."</div>
          </td>";
    echo "</tr>";
}
                ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.container {
    padding: 20px;
}

.add-emoji-form {
    margin-bottom: 30px;
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 5px;
}

form {
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-width: 300px;
}

input {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

button {
    padding: 8px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

table {
    width: 100%;
    border-collapse: collapse;
}

tr {
    border-bottom: 1px solid #ddd;
}

td {
    padding: 12px;
    vertical-align: middle;
}

.emoji-cell {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.emoji-action-container {
    display: flex;
    align-items: center;
    gap: 90px;
}

.emoji {
    font-size: 24px;
}

.actions-cell {
    display: flex;
    gap: 5px;
}

.significado {
    font-size: 14px;
    color: #666;
    text-align: left;
    margin-top: 5px;
}

.edit-btn {
    background-color: #ffc107;
    color: black;
}

.delete-btn {
    background-color: #dc3545;
    color: white;
}
</style>

<script>
document.getElementById('addEmojiForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('emoji_actions.php?action=add', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Emoji adicionado com sucesso!');
            loadPage('emoji.php');
        } else {
            alert('Erro ao adicionar emoji!');
        }
    });
});

function editEmoji(id) {
    fetch(`emoji_actions.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                const novoCodigo = prompt('Digite o novo emoji:', data.emoji.codigo);
                if (novoCodigo !== null) {
                    const novoSignificado = prompt('Digite o novo significado:', data.emoji.significado);
                    
                    let updateData = new FormData();
                    updateData.append('id', id);
                    
                    if (novoCodigo !== data.emoji.codigo) {
                        updateData.append('codigo', novoCodigo);
                    }
                    if (novoSignificado !== null && novoSignificado !== data.emoji.significado) {
                        updateData.append('significado', novoSignificado);
                    }
                    
                    if (updateData.has('codigo') || updateData.has('significado')) {
                        fetch('emoji_actions.php?action=edit', {
                            method: 'POST',
                            body: updateData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if(data.success) {
                                alert('Emoji atualizado com sucesso!');
                                loadPage('emoji.php');
                            } else {
                                alert('Erro ao atualizar emoji!');
                            }
                        });
                    }
                }
            } else {
                alert('Erro ao carregar dados do emoji!');
            }
        });
}

function deleteEmoji(id) {
    if(confirm('Tem certeza que deseja excluir este emoji?')) {
        fetch('emoji_actions.php?action=delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Emoji excluído com sucesso!');
                loadPage('emoji.php');
            } else {
                alert('Erro ao excluir emoji!');
            }
        });
    }
}
</script>