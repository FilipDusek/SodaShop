<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\ShopItem;
use AppBundle\Entity\Order;
use AppBundle\Form\ShopItemType;
use AppBundle\Form\OrderType;
use AppBundle\other\struct;

class AdminController extends Controller
{

    /**
     * @Route("/admin/items/", name="ViewEditItems")
     */
    public function itemsAction(Request $request)
    {
        $repo = $this->getDoctrine() ->getRepository('AppBundle:ShopItem');
        $items = $repo->findAll();

        $i = 0;
        #make form for reach item
        foreach ($items as $item) {
            $t = new struct();
            #save to class so both item and its form can be accessed in the same foreach loop
            $t->thing = $item;

            $t->form = $this->container
                ->get('form.factory')
                ->createNamedBuilder('form_'.$i, new ShopItemType(), $item)
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

        return $this->render('default/adminItems.html.twig', array('tuples' => $tuples));
    }

    /**
     * @Route("/admin/delete/{id}", name="Delete")
     */
    public function DeleteItemAction(Request $request, $id)
    {
        $item = $this->getDoctrine()
        ->getRepository('AppBundle:ShopItem')
        ->find($id);

        if (!$item) {
            throw $this->createNotFoundException(
                'No product found for ID '.$id);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($item);
        $em->flush();
     }

    /**
     * @Route("/admin/add/", name="AddItem")
     */
    public function AddItemAction(Request $request)
    {
        $item = new ShopItem();
        $form = $this->createForm(new ShopItemType(), $item);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();
            return $this->redirectToRoute('ViewEditItems');
        } 

        return $this->render('default/addItem.html.twig', array(
            'form' => $form->createView()
            ));
    }

    /**
     * @Route("/admin/orders/", name="ViewOrders")
     */
    public function OrdersAction(Request $request)
    {
        $repo = $this->getDoctrine() ->getRepository('AppBundle:Order');
        $orders = $repo->findAll();
        return $this->render('default/orders.html.twig', array('orders' => $orders));
    }

    /**
     * @Route("/admin/orders/accept/{id}", name="AcceptOrder")
     */
    public function AcceptOrderAction(Request $request, $id)
    {
        $order = $this->getDoctrine()->getRepository('AppBundle:Order')->find($id);

        if (!$order) {
            throw $this->createNotFoundException(
                'No order found for ID '.$id);
        }

        if ($order->getAmount() > $order->getItem()->getOnStock()) {
            return new Response('Not enough items of this kind in stock (Item ID = '.$order->getItem()->getId().')');
        }

        if ($order->getStatus() != 0) {
            return new Response('This order was already processed (Order ID = '.$order->getId().')');
        }


        $item = $order->getItem();
        $item->setOnStock($item->getOnStock() - $order->getAmount());

        $order->setStatus(1);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirectToRoute('ViewOrders');        
    }

    /**
     * @Route("/admin/orders/decline/{id}", name="DeclineOrder")
     */
    public function DeclineOrderAction(Request $request, $id)
    {
        $order = $this->getDoctrine()->getRepository('AppBundle:Order')->find($id);

        if (!$order) {
            throw $this->createNotFoundException(
                'No order found for ID '.$id);
        }

        if ($order->getStatus() != 0) {
            return new Response('This order was already processed (Order ID = '.$order->getId().')');
        }

        $order->setStatus(-1);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirectToRoute('ViewOrders');        
    }

    /**
     * @Route("/admin/")
     */
    public function IndexAction(Request $request)
    {
        return $this->redirectToRoute('ViewEditItems'); 
    }
}
