<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\LoginType;
use App\Form\ResetPasswordType;
use App\Repository\usersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @param Request $request
     * @Route("/login", name="login")
     */
    public function login(Request $request,usersRepository $repository): Response
    {
        $session = $request->getSession();
            if($session->get('email')!=null){
                return $this->redirectToRoute('users_index');
            }
            $user = new Users();
            $form = $this->createForm(LoginType::class, $user);
            $form->handleRequest($request);
            $email = $repository->findOneBy(['email' => $user->getEmail()]);
            if ($email != null) {
                if ($user->getPassword() == $email->getPassword()) {
                    if ($email->getRoles() == "admin") {
                        $session = new Session();
                        $session->set('id', $email->getId());
                        $session->set('email', $email->getEmail());
                        $session->set('password', $email->getPassword());
                        $session->set('role', $email->getRoles());
                        $session->set('username', $email->getUsername());
                        return $this->redirectToRoute('users_index');
                    } else {
                        $session = new Session();
                        $session->set('id', $email->getId());
                        $session->set('email', $email->getEmail());
                        $session->set('password', $email->getPassword());
                        $session->set('role', $email->getRoles());
                        $session->set('username', $email->getUsername());
                        return $this->redirectToRoute('transport_indexfront');
                    }
                }
            }
        return $this->render('users/Login.html.twig', [
            'controller_name' => 'UsersController',
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @Route("/logout", name="logout")
     */
    public function logout(Request $request,usersRepository $repository): Response
    {
        $session = $request->getSession();
        $session->clear();
        return $this->redirectToRoute('transport_indexfront');
    }

    /**
     * @param Request $request
     * @Route("/resetpassword", name="resetpassword")
     */
    public function ResetPassword(Request $request,usersRepository $repository): Response
    {
        $user = new Users();
        $session = $request->getSession();
        $id = $session->get('id');
        $users = $repository->findOneBy(['id' => $id]);
        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $user->setUsername($session->get('username'));
                $user->setEmail($session->get('email'));
            $user->setRoles($session->get('role'));
            $users->setPassword($user->getPassword());
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $session->clear();
            return $this->redirectToRoute('login');
        }

        return $this->render('users/reset.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
