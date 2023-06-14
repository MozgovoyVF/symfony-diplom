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

    /**
     * @param User $user
     * @param mixed $signatureComponents
     * @return void
     */
    public function sendWelcomeMail(User $user, $signatureComponents): void
    {
        $this->send(
            'templates/email/welcome.html.twig',
            'Добро пожаловать на BlaBlaArticle',
            $user,
            function (TemplatedEmail $email) use ($signatureComponents) {
                $email
                    ->context([
                        'url' => $signatureComponents->getSignedUrl(),
                    ]);
            }
        );
    }

    /**
     * @param User $user
     * @param array $articles
     * @return void
     */
    public function sendWeeklyNewsletter(User $user, array $articles): void
    {
        $this->send(
            'email/newsletter.html.twig',
            'Еженедельная рассылка статей BlaBlaArticle',
            $user,
            function (TemplatedEmail $email) use ($articles) {
                $email
                    ->context([
                        'articles' => $articles,
                    ]);
            }
        );
    }

    /**
     * @param string $template
     * @param string $subject
     * @param User $user
     * @param Closure $callback
     * @return void
     */
    private function send(string $template, string $subject, User $user, Closure $callback = null): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('blablaarticle@symfony.ru', 'BlaBlaArticle'))
            ->to(new Address($user->getEmail(), $user->getFirstName()))
            ->htmlTemplate($template)
            ->subject($subject);

        if ($callback) {
            $callback($email);
        }

        $this->mailer->send($email);
    }
}
