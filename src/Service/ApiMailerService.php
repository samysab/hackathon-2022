<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ApiMailerService{
    private MailerInterface $mailer;
    function envoiMail( $to, $subject, $html){
        $email = (new Email())
            ->from('wbhackathon2022@example.com')
            ->to($to)
            ->subject($subject)
            ->text("text")
            ->html($html);
        $this->mailer->send($email);
    }
}