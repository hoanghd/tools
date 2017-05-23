<?php
namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;

/**
 * User behavior
 */
class UserBehavior extends Behavior
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'events' => [
            'Model.beforeSave' => [
                'created_user_id' => 'new',
                'modified_user_id' => 'always'
            ]
        ]
    ];
    
    public function initialize(array $config)
    {
        if (isset($config['events'])) {
            $this->setConfig('events', $config['events'], false);
        }
    }
    
    public function handleEvent(Event $event, EntityInterface $entity)
    {
        $eventName = $event->getName();
        $events = $this->_config['events'];
        
        $new = $entity->isNew() !== false;
        
        foreach ($events[$eventName] as $field => $when) {
            if (!in_array($when, ['always', 'new', 'existing'])) {
                throw new UnexpectedValueException(
                    sprintf('When should be one of "always", "new" or "existing". The passed value "%s" is invalid', $when)
                );
            }
            if ($when === 'always' ||
                ($when === 'new' && $new) ||
                ($when === 'existing' && !$new)
            ) {
                $this->_updateField($entity, $field);
            }
        }
        
        return true;
    }
    
    public function implementedEvents()
    {
        return array_fill_keys(array_keys($this->_config['events']), 'handleEvent');
    }
    
    protected function _updateField($entity, $field)
    {
        $session  = new \Cake\Network\Session();
        $entity->set($field, $session->read('Auth.User.id'));
    }
}
