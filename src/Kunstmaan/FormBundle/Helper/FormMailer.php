<?php

namespace Kunstmaan\FormBundle\Helper;

use Kunstmaan\FormBundle\Entity\FormSubmission;
use Swift_Mailer;
use Swift_Message;
use Swift_Mime_Message;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The form mailer.
 */
class FormMailer implements FormMailerInterface
{
    /** @var \Swift_Mailer */
    protected $mailer;

    /** @var EngineInterface */
    protected $templating;

    /** @var \Symfony\Component\DependencyInjection\ContainerInterface */
    protected $container;

    /**
     * @param Swift_Mailer       $mailer     The mailer service
     * @param TwigEngine         $templating The templating service
     * @param ContainerInterface $container  The container
     */
    public function __construct(Swift_Mailer $mailer, EngineInterface $templating, ContainerInterface $container)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->container = $container;
    }

    /**
     * @param FormSubmission $submission The submission
     * @param string         $from       The from address
     * @param string         $to         The to address(es) seperated by \n
     * @param string         $subject    The subject
     */
    public function sendContactMail(FormSubmission $submission, $from, $to, $subject)
    {
        if (empty($from) || empty($to) || empty($subject)) {
            return;
        }

        $request = $this->container->get('request_stack')->getCurrentRequest();

        $toArr = explode("\r\n", $to);
        // @var $message Swift_Mime_Message
        $message = Swift_Message::newInstance()->setSubject($subject)->setFrom($from)->setTo($toArr);
        $message->setBody(
            $this->templating->render(
                'KunstmaanFormBundle:Mailer:mail.html.twig',
                [
                    'submission' => $submission,
                    'host' => $request->getScheme().'://'.$request->getHttpHost(),
                ]
            ),
            'text/html'
        );
        $this->mailer->send($message);
    }
}
