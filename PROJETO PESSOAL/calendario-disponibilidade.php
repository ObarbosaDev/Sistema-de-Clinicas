<?php
// Conexão com o banco de dados
include('config.php');

// Função para gerar os dias úteis de um mês
function getDiasUteis($ano, $mes) {
    $dias_uteis = [];
    $total_dias = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

    for ($dia = 1; $dia <= $total_dias; $dia++) {
        $data = sprintf('%04d-%02d-%02d', $ano, $mes, $dia);
        $dia_semana = date('N', strtotime($data)); // 1 = segunda-feira, 7 = domingo
        if ($dia_semana < 6) { // Dias úteis: de segunda a sexta-feira
            $dias_uteis[] = $data;
        }
    }
    return $dias_uteis;
}

// Capturar mês e ano atual ou selecionado
$mes = isset($_GET['mes']) ? intval($_GET['mes']) : date('m');
$ano = isset($_GET['ano']) ? intval($_GET['ano']) : date('Y');

// Dias úteis do mês selecionado
$dias_uteis = getDiasUteis($ano, $mes);

// Dias preenchidos (consultas já agendadas no banco de dados)
$sql = "SELECT DISTINCT data_consulta FROM consulta WHERE MONTH(data_consulta) = ? AND YEAR(data_consulta) = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $mes, $ano);
$stmt->execute();
$res = $stmt->get_result();

$dias_preenchidos = [];
while ($row = $res->fetch_assoc()) {
    $dias_preenchidos[] = $row['data_consulta'];
}

// Exibir calendário
echo "<h1>Calendário de Disponibilidade</h1>";
echo "<form method='GET'>
        <input type='hidden' name='page' value='calendario-disponibilidade'>
        <div class='row mb-3'>
          <div class='col'>
            <label for='mes' class='form-label'>Mês</label>
            <select name='mes' id='mes' class='form-control'>";
for ($i = 1; $i <= 12; $i++) {
    $selected = $i == $mes ? 'selected' : '';
    echo "<option value='$i' $selected>" . date('F', mktime(0, 0, 0, $i, 1)) . "</option>";
}
echo "    </select>
          </div>
          <div class='col'>
            <label for='ano' class='form-label'>Ano</label>
            <select name='ano' id='ano' class='form-control'>";
for ($i = date('Y') - 2; $i <= date('Y') + 2; $i++) {
    $selected = $i == $ano ? 'selected' : '';
    echo "<option value='$i' $selected>$i</option>";
}
echo "    </select>
          </div>
        </div>
        <button type='submit' class='btn btn-primary'>Mostrar</button>
      </form>";

echo "<div class='calendar'>";
foreach ($dias_uteis as $dia) {
    $classe = in_array($dia, $dias_preenchidos) ? 'filled' : 'available';
    $texto = date('d/m/Y', strtotime($dia));
    echo "<div class='day $classe'>$texto</div>";
}
echo "</div>";
?>

<style>
.calendar {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 20px;
}
.day {
    width: 100px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    font-weight: bold;
    color: white;
}
.day.available {
    background-color: green;
}
.day.filled {
    background-color: red;
}
</style>