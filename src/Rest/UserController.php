<?php

namespace Weather\Rest;

use Weather\Exception\AccessDeniedException;
use Weather\Exception\FormInvalidException;
use Weather\Exception\UnauthorizedException;
use Weather\Exception\InvalidArgumentException;
use Weather\Form\UserForm;
use Weather\Service\Mail;
use Weather\Service\Session;
use Weather\Model\UserModel;
use Symfony\Component\HttpFoundation\Request;

class UserController
{
    protected $session;
    protected $user;
    protected $form;
    protected $mail;

    public function __construct(Session $session, UserModel $user, UserForm $form, Mail $mail)
    {
        $this->session = $session;
        $this->user = $user;
        $this->form = $form;
        $this->mail = $mail;

        if (!$this->session->isUser()) {
            throw new UnauthorizedException ('Please login or signup to continue');
        }
    }

    protected function sanitizeUserValues($values)
    {
        // Only return these fields if user is admin
        if (!$this->session->isAdmin()) {
            unset($values['created']);
            unset($values['updated']);
            unset($values['email']);
            unset($values['admin']);
        }

        return $values;
    }

    public function cgetAction()
    {
        $users = $this->user->findAll();
        $result = array();

        foreach($users as $user) {
            $result[] = $this->sanitizeUserValues($user->getValues());
        }

        return $result;
    }

    public function getAction($id)
    {
        $this->user->find($id);

        $result = $this->sanitizeUserValues($this->user->getValues());

        return $result;
    }

    public function deleteAction($id)
    {
        if (!$this->session->isAdmin()) {
            throw new AccessDeniedException('Users can not delete users');
        }

        $this->user->find($id)->delete();
    }

    public function putAction($id, Request $request)
    {
        if (!$this->session->isAdmin() && $id != $this->session->getUserId()) {
            throw new AccessDeniedException('User ID does not match');
        }

        $this->user->find($id);
        $this->form->setDefinedWritableValues($request->request->all())->validate();

        if($this->form->hasErrors()) {
            throw new FormInvalidException($this->form->getFirstError());
        } else {
            $this->user->update($this->form->getValues());
        }

        return $this->sanitizeUserValues($this->user->getValues());
    }

    public function postAction(Request $request)
    {
        if (!$this->session->isAdmin()) {
            throw new AccessDeniedException('Users can not create new users');
        }

        $this->form->setDefinedWritableValues($request->request->all())->validate();

        if($this->form->hasErrors()) {
            throw new FormInvalidException($this->form->getFirstError());
        } else {
            $this->user->transactional(function () {
                $this->user->create($this->form->getValues());
                $this->mail->newUser($this->user);
            });
        }

        return $this->sanitizeUserValues($this->user->getValues());
    }

    public function putPasswordAction($id, Request $request)
    {
        if ($id != $this->session->getUserId()) {
            throw new AccessDeniedException('User ID does not match');
        }

        $this->user->find($id);

        $password = $request->get('password');
        $new_password = $request->get('new_password');

        if ($this->user->passwordIsValid($this->user->password, $password)) {
            $this->user->updatePassword($new_password);
        } else {
            throw new InvalidArgumentException('Old password is invalid');
        }
    }

}