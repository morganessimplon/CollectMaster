<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Item;
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
        $session = new Session();
        $item = new Item();
        $this -> denyAccessUnlessGranted('ROLE_USER');

        $form = $this -> createFormBuilder($item)
            -> add('title', TextType::class)
            -> add('description', TextType::class)
            -> add('code', TextType::class)
            -> add('collection', TextType::class)
            -> add('imageUrl', UrlType::class)
            -> add('submit', SubmitType::class)
            -> getForm()
            ;

        $form -> handleRequest($request);

        if ($form -> isValid()) {
            $item = $form -> getData();
            $item -> setUser($this -> getUser() -> getId());


            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();

            $session -> getFlashBag() ->add('infos', 'Objet correctement ajouté');

            return $this -> redirectToRoute('items');
        }

        // replace this example code with whatever you need
        return $this->render('item/add.html.twig', ['form' => $form -> createView()]);
    }


    /**
     * @Route ("/items", name="items")
     */

    public function itemsAction(Request $request) {

        $session = new Session();
        $session -> get('username');

        $repository = $this -> getDoctrine() -> getRepository('AppBundle:Item');
        $items = $repository -> findAll();
        $collections = $repository -> getCollections();

        dump($collections);


        return $this->render('item/list.html.twig', ['items' => $items, 'collections' => $collections]);
    }


    /**
     * @Route ("/item/{id}", name="oneItem")
     */

    public function oneItemAction(Request $request, $id) {

        $repository = $this -> getDoctrine() -> getRepository('AppBundle:Item');
        $item = $repository -> find($id);


        return $this->render('item/one.html.twig', ['item' => $item]);
    }

    /**
     * @Route ("/item/remove/{id}", name="removeItem")
     */

    public function removeAction(Request $request, $id) {

        $session = new Session();

        $this -> denyAccessUnlessGranted('ROLE_USER');

        $doctrine = $this -> getDoctrine();
        $em = $doctrine -> getManager();
        $repository = $doctrine -> getRepository('AppBundle:Item');

        $item = $repository -> find($id);

        if ($item -> isAuthor($this -> getUser())) {
            $em->remove($item);
            $em->flush();

            $session -> getFlashbag() -> add('infos', 'Objet supprimé !');
        } else {

            $session -> getFlashbag() -> add('errors', 'Objet appartenant à un autre utilisateur');
        }



        return $this -> redirectToRoute('items');
    }
}
