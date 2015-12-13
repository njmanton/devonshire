<?php

class PostsController extends AppController {
	public $name = 'Posts';
	public $helpers = ['Html', 'Form'];
	
	public function index($limit = null) {

		// set up parameters
		$arr = [
			'order' => ['sticky DESC', 'created DESC'],
			'contain' => 'User.username'
		];

		// set the limit for number of posts returned
		if (is_null($limit)) {
			$arr['limit'] = 9999;
			$arr['conditions'] = 'datediff(now(), created) < 30';
		} else {
			$arr['limit'] = $limit;
		}

		$data = $this->Post->find('all', $arr);

		// push returned data to the view
		if ($this->request->is('requested')) {
			return $data;
		} else {
			$this->set('posts', $data);	
		}
		
	} // end index

	public function add() {

		// only admins can edit posts	
		if (!$this->Auth->user('admin')) {
			$this->flash('Sorry, only admins can create posts.', $this->referer(), 2);
		}

		if ($this->request->is('post')) {
			$tags = '<p><a><img><strong><span><strike><em><ul><li><ol><b><i>';
			$this->request->data['Post']['body'] = strip_tags($this->request->data['Post']['body'], $tags);
			if ($this->Post->save($this->request->data)) {
				$this->Session->setFlash(__('Your post has been saved'), 'custom-flash', ['myclass' => 'alert-box success']);
				$this->redirect(['action' => 'index']);
			} else {
				$this->Session->setFlash(__('Unable to save your post'), 'custom-flash', ['myclass' => 'alert-box warning']);
			}
		}

	} // end add

	public function edit($id = null) {

		// only admins can edit posts	
		if (!$this->Auth->user('admin')) {
			$this->flash('Sorry, only admins can edit posts.', $this->referer(), 2);
		}

		$this->Post->id = $id;
		if ($this->request->is('get')) {
			$this->request->data = $this->Post->read();
		} else {
			$tags = '<p><a><img><strong><span><strike><em><ul><li><ol><b><i>';
			$this->request->data['Post']['body'] = strip_tags($this->request->data['Post']['body'], $tags);
			if ($this->Post->save($this->request->data)) {
				$this->Session->setFlash(__('Your post has been updated'), 'custom-flash', ['myclass' => 'alert-box success']);
				$this->redirect(['action' => 'index']);
			} else {
				$this->Session->setFlash(__('Unable to update your post'), 'custom-flash', ['myclass' => 'alert-box warning']);
			}
		}

	} // end edit

	public function delete($id = null) {
		
		if ($this->request->is('get')) {
			throw new MethodNotAllowedException();
		}

		if ($this->Auth->user('admin')) {
			if ($this->Post->delete($id)) {
				$this->Session->setFlash(__('The post with id: %s has been deleted', $id), 'custom-flash', ['myclass' => 'alert-box info']);
				$this->redirect(['action' => 'index']);
			}
		} else {
			// shouldn't get this far, as non-admins should not see the link. But just in case
			$this->flash(__('Only admin users may delete posts'), $this->referer, 2);
		}
		
	} // end delete

	public function beforeFilter() {

		parent::beforeFilter();
		$this->Auth->allow();
		
	}

} // end class