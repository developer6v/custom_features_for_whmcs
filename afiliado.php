<?php

include_once(__DIR__ . '/init.php');

use WHMCS\Database\Capsule;

$affiliateId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$affiliateId) {
    echo json_encode(['result' => 'error', 'message' => 'Parâmetro inválido: id']);
    exit;
}

// Obtendo dados do afiliado
$affiliate = Capsule::table('tblaffiliates')
    ->where('id', $affiliateId)
    ->first();

if (!$affiliate) {
    echo json_encode(['result' => 'error', 'message' => 'Afiliado não encontrado']);
    exit;
}

$client = Capsule::table('tblclients')
    ->where('id', $affiliate->clientid)
    ->select('firstname', 'lastname', 'email')
    ->first();

$fullName = $client ? $client->firstname . ' ' . $client->lastname : '';

// Total de comissões geradas pelo afiliado
$totalCommissions = Capsule::table('tblaffiliateshistory')
    ->where('affiliateid', $affiliateId)
    ->sum('amount');

// Quantidade de assinaturas ativas associadas ao afiliado
$activeSubscriptionsCount = Capsule::table('tblhosting')
    ->join('tblclients', 'tblhosting.userid', '=', 'tblclients.id')
    ->join('tblaffiliates', 'tblclients.id', '=', 'tblaffiliates.clientid')
    ->where('tblaffiliates.id', $affiliateId) // Agora associamos pelo afiliado
    ->where('tblhosting.domainstatus', 'Active')
    ->count();

// Valor total gerado pelas assinaturas ativas do afiliado
$totalGeneratedAmount = Capsule::table('tblclients')
    ->join('tblaffiliates', 'tblclients.id', '=', 'tblaffiliates.clientid')
    ->join('tblinvoices', 'tblclients.id', '=', 'tblinvoices.userid')
    ->where('tblaffiliates.id', $affiliateId) // Filtra pelo ID do afiliado
    ->where('tblinvoices.status', 'Paid') // Filtra faturas pagas
    ->sum('tblinvoices.total'); // Soma o total das faturas



$response = [
    'result' => 'success',
    'affiliate' => [
        'id' => $affiliate->id,
        'name' => $fullName,
        'email' => $client->email,
        'balance' => $affiliate->balance,
        'withdrawn' => $affiliate->withdrawn,
        'total_commissions' => $totalCommissions,
        'active_subscriptions_count' => $activeSubscriptionsCount,
        'total_generated_amount' => getAffiliateServiceLinkedTotals($affiliateId),
    ],
];




echo json_encode($response);

function getAffiliateServiceLinkedTotals(int $affiliateId, ?string $dateStart=null, ?string $dateEnd=null): float {
    // Serviços referidos
    $serviceIds = Capsule::table('tblaffiliatesaccounts')
        ->where('affiliateid', $affiliateId)
        ->pluck('relid');

    if ($serviceIds->isEmpty()) return 0.0;

    // Itens de fatura que apontam p/ esses serviços
    $items = Capsule::table('tblinvoiceitems as ii')
        ->join('tblinvoices as i', 'i.id', '=', 'ii.invoiceid')
        ->whereIn('ii.relid', $serviceIds)
        ->where('i.status', 'Paid')
        // Tipos comuns: Hosting, Addon, Domain, etc. (ajuste se necessário)
        ->whereIn('ii.type', ['Hosting','Addon','Domain']);

    if ($dateStart) $items->where('i.date','>=',$dateStart);
    if ($dateEnd)   $items->where('i.date','<=',$dateEnd);

    // Soma do amount dos itens (não inclui impostos rateados)
    return (float) $items->sum('ii.amount');
}