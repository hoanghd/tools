<?php
namespace App\View;

use Cake\Controller\Component\PaginatorComponent;
use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\View\Cell;

class CCell extends Cell
{
    public $paginate = [];
    
    public function paginate($object = null, array $settings = []){
        $component = new ComponentRegistry();
        $component->setController(
            new Controller($this->request)
        );
        
        $paginator = new PaginatorComponent($component);
        
        $settings += $this->paginate;
        
        return $paginator->paginate($object, $settings);
    }
}
