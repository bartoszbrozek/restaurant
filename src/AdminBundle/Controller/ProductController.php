<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProductController extends Controller
{
    /**
     * @Route("/admin/addProduct")
     */
    public function addProductAction(Request $request)
    {
        $product = new Product();

        $form = $this->createFormBuilder($product)
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('price', NumberType::class)
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn-lg btn-success'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session = new Session();
            $em = $this->getDoctrine()->getManager();

            try {
                $em->persist($product);
                $em->flush();

                $msg = "Poprawnie dodano nowy produkt: ". $product->getName();
                $session->getFlashBag()->set('notice', $msg);

            } catch(Doctrine_Manager_Exception $ex) {
                $session->getFlashBag()->set('error', 'Wystąpił błąd podczas dodawania produktu.');
            }
        }


        return $this->render('AdminBundle:Product:addProduct.html.twig', [
            'form' => $form->createView(),
            'page_title' => 'Add new product'
        ]);
    }

    /**
     * @Route("/admin/removeProduct/{product_id}")
     */
    public function removeProductAction(Request $request, $product_id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('AppBundle:Product')->find($product_id);
        try {
            $em->remove($product);
            $em->flush();
        } catch (Doctrine_Manager_Exception $ex) {
            echo $ex;
        }

        return $this->render('AdminBundle:Default:index.html.twig', [
            'page_title' => 'Delete specific product'
        ]);
    }
}
