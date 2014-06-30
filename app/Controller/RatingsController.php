<?php
App::uses('AppController', 'Controller');
/**
 * Ratings Controller
 *
 * @property Rating $Rating
 */
class RatingsController extends AppController {


/**
 * index method
 *
 * @return void
 */
    public function index() {
        $this->Rating->recursive = 0;
        $this->set('Ratings', $this->paginate());
    }

/**
 * view method
 *
 * @param string $id
 * @return void
 */
    public function view($id = null) {
        $this->Rating->id = $id;
        if (!$this->Rating->exists()) {
            throw new NotFoundException(__('Invalid Rating'));
        }
        $this->set('Rating', $this->Rating->read(null, $id));
    }

/**
 * add method
 *
 * @return void
 */
    public function add() {
        if ($this->request->is('post')) {
            $this->Rating->create();
            if ($this->Rating->save($this->request->data)) {
                $this->Session->setFlash(__('The Rating has been saved'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Rating could not be saved. Please, try again.'));
            }
        }
        $games = $this->Rating->Game->find('list');
        $players = $this->Rating->Player->find('list');
        $this->set(compact('games', 'players'));
    }

/**
 * edit method
 *
 * @param string $id
 * @return void
 */
    public function edit($id = null) {
        $this->Rating->id = $id;
        if (!$this->Rating->exists()) {
            throw new NotFoundException(__('Invalid Rating'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Rating->save($this->request->data)) {
                $this->Session->setFlash(__('The Rating has been saved'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Rating could not be saved. Please, try again.'));
            }
        } else {
            $this->request->data = $this->Rating->read(null, $id);
        }
        $games = $this->Rating->Game->find('list');
        $players = $this->Rating->Player->find('list');
        $this->set(compact('games', 'players'));
    }

/**
 * delete method
 *
 * @param string $id
 * @return void
 */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->Rating->id = $id;
        if (!$this->Rating->exists()) {
            throw new NotFoundException(__('Invalid Rating'));
        }
        if ($this->Rating->delete()) {
            $this->Session->setFlash(__('Rating deleted'));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Rating was not deleted'));
        $this->redirect(array('action' => 'index'));
    }



}
