	Custome Query
	http://book.cakephp.org/3.0/en/controllers/components/authentication.html#customizing-find-query
	------------------------------------------------------
					/src/Controller/AppController
	------------------------------------------------------
	<?php
	public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('Auth', [
            'loginRedirect' => [
                'controller' => 'Users',
                'action' => 'index'
            ],
            'logoutRedirect' => [
                'controller' => 'Pages',
                'action' => 'display',
                'home'
            ]
        ]);
    }
	
	//Từ function để kiểm tra role để cho phép vào controller nào
	public function beforeFilter(Event $event)
    {
        $this->Auth->allow(['index', 'view', 'display']);
    }
	?>
	
	------------------------------------------------------
					/src/Model/Entity/User
	------------------------------------------------------
	<?php
	use Cake\Auth\DefaultPasswordHasher;
	protected function _setPassword($password)
	{
		return (new DefaultPasswordHasher)->hash($password);
	}
	?>
	
	------------------------------------------------------
					/src/Controller/UsersController
	------------------------------------------------------	
	<?php
	public function login()
    {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            }
            $this->Flash->error(__('Invalid username or password, try again'));
        }
    }

    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }
	?>
	
	------------------------------------------------------
					Views
	------------------------------------------------------		
	<div class="users form">
	<?= $this->Flash->render('auth') ?>
	<?= $this->Form->create() ?>
		<fieldset>
			<legend><?= __('Please enter your username and password') ?></legend>
			<?= $this->Form->input('username') ?>
			<?= $this->Form->input('password') ?>
		</fieldset>
	<?= $this->Form->button(__('Login')); ?>
	<?= $this->Form->end() ?>
	</div>