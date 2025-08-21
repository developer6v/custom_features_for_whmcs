<?php
include_once('../../../../../init.php');

use WHMCS\Database\Capsule;

function formatCpfCnpj($cpfCnpj)
{
    // Remove todos os caracteres não numéricos
    $cpfCnpj = preg_replace('/\D/', '', $cpfCnpj);

    // Verifica se é CPF ou CNPJ
    if (strlen($cpfCnpj) == 11) {
        // Formato CPF: 000.000.000-00
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpfCnpj);
    } elseif (strlen($cpfCnpj) == 14) {
        // Formato CNPJ: 00.000.000/0000-00
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cpfCnpj);
    }

    return $cpfCnpj; // Retorna sem formatação se não for CPF nem CNPJ
}

function updateClientCpfCnpj()
{
    // Obter todos os valores de campos customizados com fieldid = 1
    $customFields = Capsule::table('tblcustomfieldvalues')
        ->where('fieldid', 1)
        ->get();
    
    $updatedCount = 0;

    foreach ($customFields as $customField) {
        $cpfCnpj = $customField->value; // Valor do CPF/CNPJ

        // Verificar e formatar CPF/CNPJ
        if ($cpfCnpj) {
            $formattedCpfCnpj = formatCpfCnpj($cpfCnpj);
            
            if ($cpfCnpj != $formattedCpfCnpj) {
                // Atualiza o campo no banco
                Capsule::table('tblcustomfieldvalues')
                    ->where('relid', $customField->relid)
                    ->where('fieldid', 1)
                    ->update(['value' => $formattedCpfCnpj]);
                $updatedCount++;
            }
        }
    }

    return $updatedCount;
}

$updatedClients = updateClientCpfCnpj();
echo json_encode(['updatedCount' => $updatedClients]);
