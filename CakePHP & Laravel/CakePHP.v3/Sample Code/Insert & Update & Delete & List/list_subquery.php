	------------------------------------------------------
						Controller
	------------------------------------------------------
	<?php
	public function index()
    {
		$this->loadModel( 'Users' );
		$this->loadModel( 'Comments' );	
		
		$select = $this->Posts->find()
					->select($this->Posts)
					->select($this->Users)
					->select('Comments.order_num')
					->leftJoin(['Users' => 'users' ], 'Users.id=Posts.user_id')
					->leftJoin( ['Comments' => (					
						$this->Comments
							->find()
							->select(['post_id'=>'post_id', 'order_num' => 'COUNT(id)'])
							->group('post_id')
					) ], 'Comments.post_id=Posts.id' );
		
		$posts = $this->paginate( $select );

        $this->set(compact('posts'));
        $this->set('_serialize', ['posts']);
    }
	
	Hoặc
	
	$this->paginate = [
            'contain' => ['Users'],
			'fields' => function( $q ){
				return array_merge(
					$q->aliasFields($this->Posts->schema()->columns(), $this->Posts->alias()),
					$q->aliasFields($this->Posts->Users->schema()->columns(), $this->Posts->Users->alias()),
					['Comments.order_num']
				);
			},
			'join' => [
				'Comments' => [
					'table' => $this->Posts->Comments
								->find()
								->select(['post_id'=>'post_id', 'order_num' => 'COUNT(id)'])
								->group('post_id'),
					'type' => 'LEFT',
					'conditions' => 'Comments.post_id=Posts.id'					
				]				
			]
        ];
	?>	
	------------------------------------------------------
						View
	------------------------------------------------------
	<?php $post->Comments['order_num'] ?>
	<?php $post->users->id ?>