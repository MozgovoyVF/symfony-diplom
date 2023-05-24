<?php

namespace App\Service;

use App\Entity\User;
use Closure;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class Mailer
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }
    
    public function sendWelcomeMail(User $user)
    {
        $this->send('email/welcome.html.twig', 'Добро пожаловать на BlaBlaArticle', $user);
    }
    
    public function sendWeeklyNewsletter(User $user, array $articles)
    {
        $this->send(
            'email/newsletter.html.twig',
            'Еженедельная рассылка статей BlaBlaArticle',
            $user,
            function (TemplatedEmail $email) use ($articles) {
                $email
                    ->context([
                        'articles' => $articles,
                    ])
                ;
            }
        );
    }
    
    private function send(string $template, string $subject, User $user, Closure $callback = null)
    {
        $email = (new TemplatedEmail())
            ->from(new Address('noreply@symfony.skillbox', 'BlaBlaArticle'))
            ->to(new Address($user->getEmail(), $user->getFirstName()))
            ->htmlTemplate($template)
            ->subject($subject)
        ;
        
        if ($callback) {
            $callback($email);
        }

        $this->mailer->send($email);
    }
}
