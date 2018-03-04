<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\ContactType;
use AppBundle\Form\RegisterType;
use AppBundle\Service\UserServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Swift_SmtpTransport;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends Controller
{

    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * UserController constructor.
     * @param UserServiceInterface $userService
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }


    /**
     * @Route("/register", name="register_user")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();

        $form = $this->createForm(RegisterType::class,$user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->userService->register($user,$passwordEncoder);

            return $this->redirectToRoute('login_user');
        }

        return $this->render('user/register.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/users", name="all_users")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function allUsers(Request $request){

        $allUsers = $this->userService->viewAll();

        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate($allUsers,$request->query->getInt('page',1),$request->query->getInt('limit',10));

        return $this->render('users.html.twig',['allUsers' => $result]);
    }

    /**
     * @Route("/contactUs",name="contact_us")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function contactUs(Request $request){

        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();

            $message = \Swift_Message::newInstance()
            ->setSubject('From Pictures')
            ->setFrom($data['email'])
            ->setTo('viktor.matsanov@gmail.com')
            ->setBody($data['message'],'text/plain');


            $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')->setUsername('viktor.matsanov@gmail.com')->setPassword('raqlora9401raqlora2334');;
//Supposed to allow local domain sending to work from what I read
            $transport->setLocalDomain('[127.0.0.1]');

            $this->get('mailer')->send($message);
        }

        return $this->render('contactUs.html.twig', array(
            'form' => $form->createView()
        ));

    }

}
