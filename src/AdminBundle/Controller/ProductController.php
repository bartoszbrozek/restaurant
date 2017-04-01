<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Form\ProductType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller
{

    /**
     * @Route("/admin/productsManagement", name="AdminBundle:productsManagement")
     */
    public function productsManagementAction(Request $request)
    {
        $time_start = microtime(true);
        $products = $this->getDoctrine()
            ->getRepository('AppBundle:Product');
        $test = $products->findAll();
        echo $time_elapsed = microtime(true) - $time_start;


        $product = new Product();

        // Adding product
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session = $this->container->get('session');
            $em = $this->getDoctrine()->getManager();

            try {
                $em->persist($product);
                $em->flush();

                $msg = "Successfully added a new product: " . $product->getName();
                $session->getFlashBag()->set('notice', $msg);

                return $this->redirectToRoute('AdminBundle:productsManagement');
            } catch (Doctrine_Manager_Exception $ex) {
                $session->getFlashBag()->set('error', "An error has occured during adding product: $ex");
                return $this->redirectToRoute('AdminBundle:productsManagement');
            }
        }

        return $this->render('AdminBundle:Product:productsManagement.html.twig', [
            'products' => $products->findAll(),
            'form' => $form->createView(),
            'page_title' => 'All products in your menu'
        ]);
    }


    /**
     * @Route("/admin/editProduct/{product_id}", name="AdminBundle:editProduct")
     */
    public function editProductAction(Request $request, $product_id)
    {
        $product = $this->getDoctrine()
            ->getRepository('AppBundle:Product')
            ->find($product_id);

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session = $this->container->get('session');
            $em = $this->getDoctrine()->getManager();

            try {
                $em->persist($product);
                $em->flush();

                $msg = "Successfully updated product: " . $product->getName();
                $session->getFlashBag()->set('notice', $msg);

                return $this->redirectToRoute('AdminBundle:productsManagement');
            } catch (Doctrine_Manager_Exception $ex) {
                $session->getFlashBag()->set('error', "An error has occured during editing product: $ex");
                return $this->redirectToRoute('AdminBundle:productsManagement');
            }
        }

        return $this->render('AdminBundle:Product:editProduct.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            'page_title' => 'Edit product'
        ]);
    }

    /**
     * @Route("/admin/removeProduct", name="AdminBundle:removeProduct")
     */
    public function removeProductAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $product_id = $request->get('entityId');
        $session = $this->container->get('session');

        if (!empty($product_id)) {
            try {
                $product = $em->getRepository('AppBundle:Product')->find($product_id);
                $em->remove($product);
                $em->flush();
                $session->getFlashBag()->set('notice', 'Successfully removed a selected product.');
            } catch (Doctrine_Manager_Exception $ex) {
                $session->getFlashBag()->set('error', "An error has occured while trying to remove product: $ex");
            }
        } else {
            // if no such an id
            $session->getFlashBag()->set('error', "An error has occured while trying to remove product: no such a product.");
        }
        return $this->redirectToRoute('AdminBundle:productsManagement');
    }
}
