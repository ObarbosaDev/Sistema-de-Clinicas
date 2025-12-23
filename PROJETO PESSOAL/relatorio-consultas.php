<?php
// Conexão com o banco de dados
include('config.php');

// Query para buscar os dados
$sql = "SELECT 
            m.nome_medico AS medico, 
            p.nome_paciente AS paciente, 
            c.data_consulta, 
            c.hora_consulta, 
            c.descricao_consulta 
        FROM consulta c
        JOIN medico m ON c.medico_id_medico = m.id_medico
        JOIN paciente p ON c.paciente_id_paciente = p.id_paciente
        ORDER BY m.nome_medico, c.data_consulta, c.hora_consulta";

$res = $conn->query($sql);

if ($res->num_rows > 0) {
    echo "<h1>Relatório de Consultas</h1>";
    $current_medico = '';
    while ($row = $res->fetch_assoc()) {
        if ($current_medico !== $row['medico']) {
            if ($current_medico !== '') {
                echo "</ul>";
            }
            $current_medico = $row['medico'];
            echo "<h3>Médico: {$current_medico}</h3>";
            echo "<ul>";
        }
        echo "<li>
                Paciente: <strong>{$row['paciente']}</strong><br>
                Data: {$row['data_consulta']} - Hora: {$row['hora_consulta']}<br>
                Descrição: {$row['descricao_consulta']}
              </li>";
    }
    echo "</ul>";
} else {
    echo "<p>Não há consultas registradas.</p>";
}
?>