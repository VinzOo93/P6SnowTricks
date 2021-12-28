<?php

namespace App\Validator;


use App\Entity\Video;
use http\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UrlYTValidator extends ConstraintValidator
{

    public function validate($values, Constraint $constraint)
    {

        if (!$constraint instanceof UrlYT) {
            throw  new UnexpectedValueException($constraint, UrlYTValidator::class);
        }

        if (empty($values)) {
            return;
        }
        function ping($host): bool
        {
            exec(sprintf('ping -c 1 -W 5 %s', escapeshellarg($host)), $res, $rval);
            return $rval === 0;
        }


        /**
         * @var UrlYT $constraint
         */
        foreach ($values as $item) {
            $url = parse_url($item->getSlug());
            $up = ping($url['host']);

            if ($url['host'] != 'www.youtube.com') {
                $this->context->buildViolation($constraint->message)->addViolation();

            } else {
                if ($up === false) {
                    $this->context->buildViolation($constraint->message)->addViolation();
                }
            }
        }
    }

}