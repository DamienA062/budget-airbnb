<?php

namespace Damien\RecaptchaBundle\Constraints;

use ReCaptcha\ReCaptcha;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\ConstraintValidator;

class RecaptchaValidator extends ConstraintValidator
{
    /**
     * permet de récupérer des informations sur la requête en cours
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var ReCaptcha
     */
    private $reCaptcha;

    public function __construct(RequestStack $requestStack, ReCaptcha $reCaptcha)
    {
        $this->requestStack = $requestStack;
        $this->reCaptcha = $reCaptcha;
    }
    
    public function validate($value, Constraint $constraint)
    {
        $request = $this->requestStack->getCurrentRequest();

        //on récupère notre clé g-recaptcha-response
        $recaptchaResponse = $request->request->get('g-recaptcha-response');

        if(empty($recaptchaResponse))
        {
            $this->addViolation($constraint);
            return;
        }

        $response = $this->reCaptcha
                        ->setExpectedHostname($request->getHost())
                        ->verify($recaptchaResponse, $request->getClientIp());
        
        if(!$response->isSuccess())
        {
            dump($response->getErrorCodes());
            $this->addViolation($constraint);
        }
    }

    private function addViolation(Constraint $constraint)
    {
        $this->context->buildViolation($constraint->message)->addViolation();
    }

}