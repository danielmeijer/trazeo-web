<?php
/**
$user = $this->checkPrivateAccess($request);
if ($user == false || $user == null) {
$view = View::create()
->setStatusCode(200)
->setData($this->msgDenied());

return $this->get('fos_rest.view_handler')->handle($view);
}

$em = $this->get('doctrine.orm.entity_manager');

$id_group = $request->get('id_group');

$thread = $em->getRepository('SopinetTimelineBundle:Thread')->findOneById($id_group);
$comments = $em->getRepository('SopinetTimelineBundle:Comment')->findByThread($thread);
$data = array();
foreach ($comments as $comment) {
$comment->setAuthorName($comment->getAuthorName());
$data[] = $comment;
}

$view = View::create()
->setStatusCode(200)
->setData($this->doOK($data));

return $this->get('fos_rest.view_handler')->handle($view);
 **/