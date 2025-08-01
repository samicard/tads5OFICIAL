<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\View\Exception\MissingTemplateException;

/**
 * Static content controller
 *
 * This controller will render views from templates/Pages/
 *
 * @link https://book.cakephp.org/5/en/controllers/pages-controller.html
 */
class PagesController extends AppController
{
    public function initialize(): void
    {
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    public function logar()
    {
        $response = null;
        $statusCode = 200;
        $this->loadComponent('Authentication.Authentication');
        $this->Authentication->logout();
        $result = $this->Authentication->getResult();
        if ($result && $result->isValid()) {
            $senha = $this->request->getData('email') . date('YmdHis');
            $salvar['hash'] = hash('sha256', $senha);
            $salvar['user_id'] = $result->getData()['id'];
            $salvar['expira'] = date('Y-m-d', strtotime('+4 days'));
            try {
                $sql = 'SELECT * FROM autenticacaos WHERE user_id = :id';
                if ($GLOBALS['connection']->execute($sql, ['id' => $result->getData()['id']])->fetch('assoc')) {
                    $autenticacao = $this->Autenticacaos->get($result->getData()['id'], contain: []);
                } else {
                    $autenticacao = $this->Autenticacaos->newEmptyEntity();
                }
                $autenticacao = $this->Autenticacaos->patchEntity($autenticacao, $salvar);
                $this->Autenticacaos->saveOrFail($autenticacao);
                $response['hash'] = $autenticacao['hash'];
                $response['msg'] = 'Login realizado com sucesso!';
            } catch (PersistenceFailedException $e) {
                $statusCode = 400;
                $response = $e->getAttributes();
            }
        } else {
            $statusCode = 400;
            $response = 'Erro ao realizar login';
        }

        return $this->response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withStatus($statusCode)
            ->withType('application/json')
            ->withStringBody(json_encode($response));
    }

    public function buscaruser() {
        $response = null;
        $statusCode = 200;

        if ($this->request->is('post') && !empty($this->request->getData('id')[0])) {
            $sql = 'SELECT * FROM users WHERE users.id = ' . $this->request->getData('id')[0];
        } else {
            $sql = 'SELECT * FROM users ORDER BY users.nome ASC';
        }

        $response = $GLOBALS['connection']->execute($sql)->fetchAll('assoc');

        return $this->response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withStatus($statusCode)
            ->withType('application/json')
            ->withStringBody(json_encode($response));
    }
}
