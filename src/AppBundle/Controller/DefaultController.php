<?php

namespace AppBundle\Controller;

use UserBundle\Entity\User;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $user = new User();

        if ($this->isGranted('ROLE_USER')) {
            // LOGIN
        } else {
            // SIGN IN
            $form = $this->createForm(UserType::class, $user);
            $form->remove('isActive');
            $form->remove('role');
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $session = $this->container->get('session');
                $em = $this->getDoctrine()->getManager();

                try {
                    $password = $this->get('security.password_encoder')
                        ->encodePassword($user, $user->getPassword());
                    $user->setPassword($password);
                    $user->setIsActive(1);
                    $user->setRole('USER');

                    $em->persist($user);
                    $em->flush();

                    $msg = "Your registration has been completed successfully! Login using your username: " . $user->getUsername() . " and password.";
                    $session->getFlashBag()->set('notice', $msg);
                } catch (UniqueConstraintViolationException $ex) {
                    $session->getFlashBag()->set('error', "An error has occured during registration: " . $ex->getErrorCode());
                }

                return $this->redirectToRoute('homepage');
            }
        }


        return $this->render('default/index.html.twig', [
            'products' => $this->getProducts(),
            'form_sign_in' => $form->createView()
        ]);
    }

    private function getProducts()
    {
        $em = $this->getDoctrine()->getRepository('AppBundle:Product');
        return $em->findAll();
    }
}
