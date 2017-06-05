<?php
namespace App\Middleware;

use Cake\Utility\Hash;
use Cake\View\CellTrait;
use Cake\Event\EventManager;
use ReflectionException;
use ReflectionMethod;
use BadMethodCallException;

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
                    
            $cell = $this->cell($name);
            $ext = Hash::get($request->params, '_ext');
            
            if( $ext == 'json'){
                try {
                    $reflect = new ReflectionMethod($cell, $cell->action);
                    $reflect->invokeArgs($cell, $cell->args);
                    
                    $response->type('json');
                    $response->body(json_encode($cell->viewVars));
                } catch (ReflectionException $e) {
                    throw new BadMethodCallException(sprintf(
                        'Class %s does not have a "%s" method.',
                        get_class($cell),
                        $cell->action
                    ));
                }
            } else {
                $response->body($cell->render());
            }
        } else {
            $response = $next($request, $response);
        }
        
        return $response;
    }
}
