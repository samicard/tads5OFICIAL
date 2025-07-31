<?php

namespace App\Controller;

use App\Controller\AppController;

class VisualizacaosController extends AppController
{

    public function visualManutencao(){
        $response = null;
        $statusCode = 200;

        if($this->request->is('post')){
            $manutencaos = $this->fetchTable('manutencaos');
            if(empty($this->request->getData()['id'])){
                $sql = "SELECT manutencaos.numntfiscal AS NF, manutencaos.valor, manutencaos.data, fornecedors.nome AS fornecedor, fornecedors.cnpj, veiculos.modelo, veiculos.placa, fabricantes.nome AS fabricante FROM manutencaos INNER JOIN fornecedors ON manutencaos.fornecedor_id = fornecedors.id INNER JOIN veiculos ON manutencaos.veiculo_id = veiculos.id INNER JOIN fabricantes ON veiculos.fabricante_id = fabricantes.id";
                $response = $GLOBALS['connection']->execute($sql)->fetchAll('assoc');
            } else {
                $id = $this->request->getData()['id'];
                $sql = "SELECT manutencaos.numntfiscal AS NF, manutencaos.valor, manutencaos.data, fornecedors.nome AS fornecedor, fornecedors.cnpj, veiculos.modelo, veiculos.placa, fabricantes.nome AS fabricante FROM manutencaos INNER JOIN fornecedors ON manutencaos.fornecedor_id = fornecedors.id INNER JOIN veiculos ON manutencaos.veiculo_id = veiculos.id INNER JOIN fabricantes ON veiculos.fabricante_id = fabricantes.id WHERE manutencaos.id = :id";
                $response = $GLOBALS['connection']->execute($sql, ['id' => $id])->fetchAll('assoc');
            }
            return $this->response
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withStatus($statusCode)
                ->withType('application/json')
                ->withStringBody(json_encode($response));
        }

    }
}
