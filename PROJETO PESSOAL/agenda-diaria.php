<?php
// Conexão com o banco de dados
include('config.php');

// Capturar a data selecionada
$data = isset($_GET['data']) ? $_GET['data'] : date('Y-m-d');

// Query para buscar as consultas da data selecionada
$sql = "SELECT 
            c.id_consulta, 
            c.hora_consulta, 
            c.descricao_consulta, 
            m.nome_medico AS medico, 
            p.nome_paciente AS paciente 
        FROM consulta c
        JOIN medico m ON c.medico_id_medico = m.id_medico
        JOIN paciente p ON c.paciente_id_paciente = p.id_paciente
        WHERE c.data_consulta = ?
        ORDER BY c.hora_consulta";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $data);
$stmt->execute();
$res = $stmt->get_result();

echo "<h1>Agenda Diária</h1>";

// Formulário para selecionar a data
echo "<form method='GET'>
        <input type='hidden' name='page' value='agenda-diaria'>
        <div class='mb-3'>
          <label for='data' class='form-label'>Selecionar Data</label>
          <input type='date' name='data' id='data' value='$data' class='form-control'>
        </div>
        <button type='submit' class='btn btn-primary'>Buscar</button>
      </form>";

// Mostrar os resultados
if ($res->num_rows > 0) {
    echo "<h3>Consultas para o dia: " . date('d/m/Y', strtotime($data)) . "</h3>";
    echo "<ul class='list-group'>";
    while ($row = $res->fetch_assoc()) {
        echo "<li class='list-group-item'>
                <strong>Hora:</strong> {$row['hora_consulta']}<br>
                <strong>Médico:</strong> {$row['medico']}<br>
                <strong>Paciente:</strong> {$row['paciente']}<br>
                <strong>Descrição:</strong> {$row['descricao_consulta']}
              </li>";
    }
    echo "</ul>";
} else {
    echo "<p>Não há consultas agendadas para esta data.</p>";
}
?>