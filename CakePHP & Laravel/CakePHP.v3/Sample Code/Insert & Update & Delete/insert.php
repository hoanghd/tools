	------------------------------------------------------
						Model
	------------------------------------------------------
	<?php
	public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('posts');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Comments', [
            'foreignKey' => 'post_id'
        ]);
        $this->belongsToMany('Tags', [
            'foreignKey' => 'post_id',
            'targetForeignKey' => 'tag_id',
            'joinTable' => 'posts_tags'
        ]);
    }
	?>
	------------------------------------------------------
						Controller
	------------------------------------------------------
	<?php
	public function add()
    {		
        $post = $this->Posts->newEntity();
		
        if ($this->request->is('post')) {
            $post = $this->Posts->patchEntity($post, $this->request->data);
            if ($this->Posts->save($post)) {
                $this->Flash->success(__('The post has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The post could not be saved. Please, try again.'));
            }
        }
		
        $users = $this->Posts->Users->find('list');
        $tags = $this->Posts->Tags->find('list');
		
        $this->set(compact('post', 'users', 'tags'));
        $this->set('_serialize', ['post']);
    }	
	?>
	
	------------------------------------------------------
						Views
	------------------------------------------------------
	<?= $this->Form->create($post) ?>
    <fieldset>
        <?php
            echo $this->Form->input('name');
            echo $this->Form->input('date', ['empty' => true]);
            echo $this->Form->input('content');
            echo $this->Form->input('user_id', ['options' => $users, 'empty' => true]);
            echo $this->Form->input('tags._ids', ['options' => $tags]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>