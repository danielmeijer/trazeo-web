<?php

namespace Sopinet\NotifierBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class NotificationController extends Controller
{

	/**
	 * @Route("/notification/{id}", name="notification")
	 * @ParamConverter("notification", class="SopinetNotifierBundle:Notification")
	 * @param Notification $notification
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function notificationAction(Notification $notification) {
		$em = $this->getDoctrine()->getEntityManager();
		$user = $this->get('security.context')->getToken()->getUser();
		 
		if ($user->getId() != $notification->getUser()->getId()) {
			die("Access error");
		}
		$notification->setViewComplete(true);
		$em->persist($notification);
		$em->flush();
		 
	}
	
	private function getNotifications() {
		$em = $this->getDoctrine()->getEntityManager();
		$user = $this->get('security.context')->getToken()->getUser();
		$reNOT = $em->getRepository("SopinetNotifierBundle:Notification");
		$notifications_pre = $reNOT->findBy(
				array('userextend' => $user, 'view' => 0),
				array('date_register' => 'DESC'),
				6
		);
			
		foreach($notifications_pre as $not) {
			if ($not->getObjectComplete() != null) {
				$notifications[] = $not;
			}
		}
		return $notifications;
	}
}
