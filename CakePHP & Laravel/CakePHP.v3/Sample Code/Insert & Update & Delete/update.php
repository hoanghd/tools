	------------------------------------------------------
						Controller
	------------------------------------------------------
	<?php
	public function edit($id = null)
    {
        $post = $this->Posts->get($id, [
            'contain' => ['Tags']
        ]);
		
        if ($this->request->is(['patch', 'post', 'put'])) {
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