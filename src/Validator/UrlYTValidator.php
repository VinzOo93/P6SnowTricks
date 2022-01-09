<?php

namespace App\Validator;


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
        function ping($url): bool
        {
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_exec($ch);
            $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if (200==$retcode) {
                return true;
            } else {
                return false;
            }

        }


        /**
         * @var UrlYT $constraint
         */
        foreach ($values as $item) {
            $url = $item->getSlug();
            $up = ping($url);

            if (!strpos($url,'www.youtube.com')) {
                $this->context->buildViolation($constraint->message)->addViolation();

            } else {
                if ($up === false) {
                    $this->context->buildViolation($constraint->message)->addViolation();
                }
            }
        }
    }

}