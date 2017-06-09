<?php
namespace App\Middleware;

use Cake\Utility\Hash;
use Cake\View\CellTrait;
use Cake\Event\EventManager;
use ReflectionMethod;
use Exception;

class CellMiddleware
{
    use CellTrait;
     
    private function eventManager(){
        return new EventManager();
    }
    
    public function __invoke($request, $response, $next)
    {
        if($request->is('ajax') && ($name = $request->query('_cell'))){
            $this->request = $request;
            $this->response = $response;
            
            try {
                $cell = $this->cell($name);
                $ext = Hash::get($request->params, '_ext');

                if( $ext == 'json'){
                    $reflect = new ReflectionMethod($cell, $cell->action);
                    $reflect->invokeArgs($cell, $cell->args);

                    $response->type('json');
                    $response->body(json_encode($cell->viewVars));
                } else {
                    $response->body($cell);
                }
            } catch (Exception $e) {
                $response->body($e);
            }
        } else {
            $response = $next($request, $response);
        }
        
        return $response;
    }
}
