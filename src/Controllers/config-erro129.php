<?php
include_once('../../../../../init.php'); // Inclui a inicialização do WHMCS

use WHMCS\Database\Capsule;

// Verifica se os dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recupera os dados enviados via POST
    $max_trials = isset($_POST['tentativasRegistro']) ? (int) $_POST['tentativasRegistro'] : null;
    $interval_between_trials = isset($_POST['intervaloTentativas']) ? (int) $_POST['intervaloTentativas'] : null;
    $openTicketAfterTrials = isset($_POST['abrirTicket']) ? (bool) $_POST['abrirTicket'] : null;

    // Verifica se os dados necessários foram fornecidos
    if ($max_trials === null || $interval_between_trials === null || $openTicketAfterTrials === null) {
        echo json_encode(['status' => 'error', 'message' => 'Faltando dados necessários.']);
        exit;
    }

    try {
        // Atualiza os dados na tabela 'sr_cf_config'
        Capsule::table('sr_cf_config')
            ->where('id', 1) // Você pode ajustar o 'id' caso a tabela tenha múltiplos registros
            ->update([
                'max_trials' => $max_trials,
                'interval_between_trials' => $interval_between_trials,
                'openTicketAfterTrials' => $openTicketAfterTrials
            ]);

        // Retorna sucesso
        echo json_encode(['status' => 'success', 'message' => 'Configurações atualizadas com sucesso.']);
    } catch (Exception $e) {
        // Em caso de erro, retorna a mensagem de erro
        echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar configurações: ' . $e->getMessage()]);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Método inválido. Somente POST é permitido.']);
}
?>
