<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Item;
use AppBundle\Form\Type\ItemType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

class ItemController extends Controller
{
    /**
     * @Route("/item", name="item")
     */
    public function addAction(Request $request)
    {
        $itemManager = $this -> get('item_manager');
        $item = new Item();
        $this -> denyAccessUnlessGranted('ROLE_USER');

        $form = $this -> createForm(ItemType::class, $item);
        $form -> handleRequest($request);

        if ($form -> isValid()) {
            $itemManager -> setForm($form) -> create();

            return $this -> redirectToRoute('items');
        }

        // replace this example code with whatever you need
        return $this->render('item/add.html.twig', ['form' => $form -> createView()]);
    }


    /**
     * @Route ("/items", name="items")
     */

    public function itemsAction(Request $request) {

        $itemManager = $this -> get('item_manager');

        return $this -> render('item/list.html.twig', [
            'items' => $itemManager -> getAll(),
            'collections' => $itemManager -> getCollection()
        ]);

    }


    /**
     * @Route ("/item/{id}", name="oneItem")
     */

    public function oneItemAction(Request $request, $id) {

        $itemManager = $this -> get('item_manager');
        $form = $this -> createForm(ItemType::class, $itemManager -> getOne($id));

        $form -> handleRequest($request);

        if ($form -> isValid()) {

            $itemManager -> setForm($form) -> update();

            return $this -> redirectToRoute('items');
        }

        $hideChangeOwner = $this -> getUser() -> hasRole('ROLE_ADMIN') ? '' : hide;

        return $this -> render('item/one.html.twig', ['form' => $form -> createView(), 'hideChangeOwner' => $hideChangeOwner]);

    }

    /**
     * @Route ("/item/remove/{id}", name="removeItem")
     */

    public function removeAction(Request $request, $id) {

        $this -> denyAccessUnlessGranted('ROLE_USER');

        $itemManager = $this -> get('item_manager');
        $itemManager -> remove($id);

        return $this -> redirectToRoute('items');
    }
}
