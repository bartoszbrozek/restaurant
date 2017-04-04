<?php

namespace AdminBundle\Controller;

use UserBundle\Entity\User;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class UserController extends Controller
{

    /**
     * @Route("/admin/usersManagement", name="AdminBundle:usersManagement")
     */
    public function usersManagementAction(Request $request)
    {
        $users = $this->getDoctrine()
            ->getRepository('UserBundle:User');

        $user = new User();

        // Adding user
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session = $this->container->get('session');
            $em = $this->getDoctrine()->getManager();

            try {
                $password = $this->get('security.password_encoder')
                    ->encodePassword($user, $user->getPassword());
                $user->setPassword($password);

                $em->persist($user);
                $em->flush();

                $msg = "Successfully added a new user: " . $user->getUsername();
                $session->getFlashBag()->set('notice', $msg);
            } catch (UniqueConstraintViolationException $ex) {
                $session->getFlashBag()->set('error', "An error has occured during adding user: " . $ex->getErrorCode());
            }

            return $this->redirectToRoute('AdminBundle:usersManagement');
        }

        return $this->render('AdminBundle:User:usersManagement.html.twig', [
            'users' => $users->findAll(),
            'form' => $form->createView(),
            'page_title' => 'All users'
        ]);
    }


    /**
     * @Route("/admin/editUser/{user_id}", name="AdminBundle:editUser")
     */
    public function editUserAction(Request $request, $user_id)
    {
        $user = $this->getDoctrine()
            ->getRepository('UserBundle:User')
            ->find($user_id);

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session = $this->container->get('session');
            $em = $this->getDoctrine()->getManager();

            try {
                $password = $this->get('security.password_encoder')
                    ->encodePassword($user, $user->getPassword());
                $user->setPassword($password);

                $em->persist($user);
                $em->flush();

                $msg = "Successfully updated user: " . $user->getUsername();
                $session->getFlashBag()->set('notice', $msg);

            } catch (Doctrine_Manager_Exception $ex) {
                $session->getFlashBag()->set('error', "An error has occured during editing user: $ex");
            }

            return $this->redirectToRoute('AdminBundle:usersManagement');
        }


        return $this->render('AdminBundle:User:editUser.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'page_title' => 'Edit user'
        ]);
    }

    /**
     * @Route("/admin/removeUser", name="AdminBundle:removeUser")
     */
    public function removeUserAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user_id = $request->get('entityId');
        $session = $this->container->get('session');

        if (!empty($user_id)) {
            try {
                $user = $em->getRepository('UserBundle:User')->find($user_id);
                $em->remove($user);
                $em->flush();
                $session->getFlashBag()->set('notice', 'Successfully removed a selected user.');
            } catch (Doctrine_Manager_Exception $ex) {
                $session->getFlashBag()->set('error', "An error has occured while trying to remove user: $ex");
            }
        } else {
            // if no such an id
            $session->getFlashBag()->set('error', "An error has occured while trying to remove user: no such a user.");
        }
        return $this->redirectToRoute('AdminBundle:usersManagement');
    }

    /**
     * @Route("/admin/changeUserIsActive/{user_id}", name="AdminBundle:changeUserIsActive")
     */
    public function changeUserIsActiveAction($user_id)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $this->container->get('session');

        if (!empty($user_id)) {
            try {
                $user = $em->getRepository('UserBundle:User')->find($user_id);
                if ($user->getIsactive() == false) {
                    $user->setIsactive(1);
                    $msg = $user->getUsername() . " is now active.";
                } else {
                    $user->setIsactive(0);
                    $msg = $user->getUsername() . " is now not active.";
                }

                $em->persist($user);
                $em->flush();
                $session->getFlashBag()->set('notice', $msg);
            } catch (Doctrine_Manager_Exception $ex) {
                $session->getFlashBag()->set('error', "An error has occured while trying to activate/deactivate user: $ex");
            }
        } else {
            // if no such an id
            $session->getFlashBag()->set('error', "An error has occured while trying to activate/deactivate user: no such a user.");
        }
        return $this->redirectToRoute('AdminBundle:usersManagement');
    }

}
