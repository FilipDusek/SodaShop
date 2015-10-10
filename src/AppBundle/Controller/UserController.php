<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\ShopItem;
use AppBundle\Entity\Order;
use AppBundle\Form\OrderType;
use AppBundle\other\struct;

class UserController extends Controller
{
    /**
     * @Route("/items/", name="ViewOrderItems")
     */
    public function itemsAction(Request $request)
    {
        #get all items
        $repo = $this->getDoctrine() ->getRepository('AppBundle:ShopItem');
        $items = $repo->findAll();

        $i = 0;
        #make form for reach item
        foreach ($items as $item) {
            $order = new Order();
            $order->setItem($item);

            #save to class so both item and its form can be accessed in the same foreach loop
            $t = new struct();
            $t->thing = $order;

            $t->form = $this->container
                ->get('form.factory')
                ->createNamedBuilder('form_'.$i, new OrderType(), $order)
                ->getForm();
            $tuples[]=$t;
            $i++;
        }

        #is form submited and if so, is it valid?
        foreach ($tuples as $tuple) {
            $tuple->form->handleRequest($request);
            if($tuple->form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($tuple->thing);
                $em->flush();
            }
            $tuple->form = $tuple->form->createView();
        }

        return $this->render('default/items.html.twig', array('tuples' => $tuples));
    }

    /**
     * @Route("/", name="homepage")
     */
    public function IndexAction(Request $request)
    {
        return $this->redirectToRoute('ViewOrderItems'); 
    }
}
