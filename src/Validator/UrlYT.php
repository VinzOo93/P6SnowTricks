<?php

namespace App\Validator;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UrlYT extends  Constraint
{
    public string $message  = 'Please enter a valid URL.';
}