<?php

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Model\Repository\SponsorTicketRepository;
use AppBundle\Event\Model\SponsorTicket;
use AppBundle\Event\Ticket\SponsorTokenMail;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SendLastCallSponsorTokenAction
{
    /** @var EventActionHelper */
    private $eventActionHelper;
    /** @var SponsorTicketRepository */
    private $sponsorTicketRepository;
    /** @var SponsorTokenMail */
    private $sponsorTokenMail;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var FlashBagInterface */
    private $flashBag;

    public function __construct(
        EventActionHelper $eventActionHelper,
        SponsorTicketRepository $sponsorTicketRepository,
        SponsorTokenMail $sponsorTokenMail,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->eventActionHelper = $eventActionHelper;
        $this->sponsorTicketRepository = $sponsorTicketRepository;
        $this->sponsorTokenMail = $sponsorTokenMail;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(Request $request)
    {
        $event = $this->eventActionHelper->getEventById($request->query->get('id'), false);
        /** @var $tokens SponsorTicket[] */
        $tokens = $this->sponsorTicketRepository->getByEvent($event);
        $mailSent = 0;

        foreach ($tokens as $token) {
            if ($token->getPendingInvitations() > 0) {
                $mailSent++;
                $this->sponsorTokenMail->sendNotification($token, true);
            }
        }

        $this->flashBag->add('notice', sprintf('%s mails de relance ont été envoyés', $mailSent));

        return new RedirectResponse($this->urlGenerator->generate('admin_event_sponsor_ticket', ['id' => $event->getId()]));
    }
}
