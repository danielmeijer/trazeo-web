<?php 

namespace Trazeo\BaseBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Trazeo\BaseBundle\Security\Authentication\Token\WsseUserToken;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class WsseListener implements ListenerInterface
{
    protected $securityContext;
    protected $authenticationManager;

    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        
        $x_wsse = 'UsernameToken Username="'.$request->get('username').'", PasswordDigest="'.$request->get('password').'", Nonce="'.$request->get('nonce').'", Created="'.$request->get('created').'"';
        //$x_wsse = 'UsernameToken Username="trazeo", PasswordDigest="f42cvwUKrAMzJfqNJ1Q/2SEorJ4=", Nonce="'.$nonce.'", Created="'.$created.'"';
		
        $request->headers->add(array('x-wsse' => $x_wsse));
        
        $wsseRegex = '/UsernameToken Username="([^"]+)", PasswordDigest="([^"]+)", Nonce="([^"]+)", Created="([^"]+)"/';
        
        if (!$request->headers->has('x-wsse') || 1 !== preg_match($wsseRegex, $request->headers->get('x-wsse'), $matches)) {
        	ldd("luismi".$request->headers->has('x-wsse').preg_match($wsseRegex, $request->headers->get('x-wsse'), $matches));
        	return;
        }
        
        $token = new WsseUserToken();
        $token->setUser($matches[1]);
        
        $token->digest   = $matches[2];
        $token->nonce    = $matches[3];
        $token->created  = $matches[4];

        try {
        	
            $authToken = $this->authenticationManager->authenticate($token);
            $this->securityContext->setToken($authToken);

            return;
        } catch (AuthenticationException $failed) {
            // ... you might log something here

            // To deny the authentication clear the token. This will redirect to the login page.
            // Make sure to only clear your token, not those of other authentication listeners.
            // $token = $this->securityContext->getToken();
            // if ($token instanceof WsseUserToken && $this->providerKey === $token->getProviderKey()) {
            //     $this->securityContext->setToken(null);
            // }
            // return;

            // Deny authentication with a '403 Forbidden' HTTP response
            $response = new Response();
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            $event->setResponse($response);

        }
        
        // By default deny authorization
        $response = new Response();
        $response->setStatusCode(Response::HTTP_FORBIDDEN);
        $event->setResponse($response);
    }
}
?>