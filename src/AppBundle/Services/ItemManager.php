<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class ItemManager {

    private $em;
    private $repository;
    private $tokenStorage;
    private $session;
    private $user;
    private $form;

    public function __construct(EntityManager $entityManager, TokenStorage $tokenStorage)
    {
        $this -> em = $entityManager;
        $this -> repository = $this -> em -> getRepository('AppBundle:Item');
        $this -> tokenStorage = $tokenStorage;
        $this -> user = $this -> tokenStorage -> getToken() -> getUser();
        $this -> session = new Session();
    }


    public function setForm($form) {

        $this -> form = $form;
        return $this;
    }

    public function create() {

        if ($this -> form -> isValid()) {

            $item = $this -> form -> getData();
            $item -> setUser($this -> user -> getId());

            $this -> flush($item);

            $this -> session -> getFlashBag() -> add('infos', 'Objet correctement ajouté');
        }
    }


    public function update() {

        $item = $this -> form -> getData();

        if ($this -> form -> get('changeOwner') -> isClicked()) {

            $item -> setUser($this -> getUser() -> getId);
        }

        $this -> flush($item);

        $this -> session -> getFlashBag() -> add('infos', 'Objet correctement modifié');
    }


    public function flush($item) {

        $this -> em -> persist($item);
        $this -> em -> flush();
    }


    public function getCollection() {

        return $this -> repository -> getCollections();
    }

    public function getAll() {

        return $this -> repository -> findAll();
    }


    public function getOne($id) {

        return $this -> repository -> find($id);
    }


    public function remove($id) {

        $item = $this -> repository -> find($id);

        if ($item -> isAuthor($this -> user)) {

            $this -> em -> remove($item);
            $this -> em -> flush();
        }
    }
}