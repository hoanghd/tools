	------------------------------------------------------
						Controller
	------------------------------------------------------
	<?php
	public function index()
    {
		$this->loadModel( 'Users' );
		$this->loadModel( 'Comments' );		
		
		$subQuery = $this->Comments
						->find()
						->select(['post_id'=>'post_id', 'order_num' => 'COUNT(id)'])
						->group('post_id');
		
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
	?>	
	------------------------------------------------------
						View
	------------------------------------------------------
	<?php $post->Comments['order_num'] ?>
	<?php $post->Users['id'] ?>