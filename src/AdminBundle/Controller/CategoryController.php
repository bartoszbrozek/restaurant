<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Form\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends Controller
{

    /**
     * @Route("/admin/categoriesManagement", name="AdminBundle:categoriesManagement")
     */
    public function categoriesManagementAction(Request $request)
    {
        $category = new Category();

        // Adding category
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session = $this->container->get('session');
            $em = $this->getDoctrine()->getManager();

            try {
                $em->persist($category);
                $em->flush();

                $msg = "Successfully added a new category: " . $category->getName();
                $session->getFlashBag()->set('notice', $msg);

                return $this->redirectToRoute('AdminBundle:categoriesManagement');
            } catch (Doctrine_Manager_Exception $ex) {
                $session->getFlashBag()->set('error', "An error has occured during adding category: $ex");
                return $this->redirectToRoute('AdminBundle:categoriesManagement');
            }
        }

        $categories = $this->getDoctrine()->getRepository('AppBundle:Category');

        return $this->render('AdminBundle:Category:categoriesManagement.html.twig', [
            'categories' => $categories->findAll(),
            'form' => $form->createView(),
            'page_title' => 'All categories in your menu'
        ]);
    }


    /**
     * @Route("/admin/editCategory/{category_id}", name="AdminBundle:editCategory")
     */
    public function editCategoryAction(Request $request, $category_id)
    {
        $category = $this->getDoctrine()
            ->getRepository('AppBundle:Category')
            ->find($category_id);

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session = $this->container->get('session');
            $em = $this->getDoctrine()->getManager();

            try {
                $em->persist($category);
                $em->flush();

                $msg = "Successfully updated category: " . $category->getName();
                $session->getFlashBag()->set('notice', $msg);

                return $this->redirectToRoute('AdminBundle:categoriesManagement');
            } catch (Doctrine_Manager_Exception $ex) {
                $session->getFlashBag()->set('error', "An error has occured during editing product: $ex");
                return $this->redirectToRoute('AdminBundle:categoriesManagement');
            }
        }

        return $this->render('AdminBundle:Category:editCategory.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'page_title' => 'Edit category'
        ]);
    }

    /**
     * @Route("/admin/removeCategory", name="AdminBundle:removeCategory")
     */
    public function removeCategoryAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $category_id = $request->get('entityId');
        $session = $this->container->get('session');

        if (!empty($category_id)) {
            try {
                $category = $em->getRepository('AppBundle:Category')->find($category_id);
                $em->remove($category);
                $em->flush();
                $session->getFlashBag()->set('notice', 'Successfully removed a selected category.');
            } catch (Doctrine_Manager_Exception $ex) {
                $session->getFlashBag()->set('error', "An error has occured while trying to remove category: $ex");
            }
        } else {
            // if no such an id
            $session->getFlashBag()->set('error', "An error has occured while trying to remove category: no such a category.");
        }
        return $this->redirectToRoute('AdminBundle:categoriesManagement');
    }
}
