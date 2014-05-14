<?php

namespace JLM\SerializerExpression\Tests\Model;

use JLM\SerializerExpression\Serializer\Annotation\ExcludeIf;

class User
{
    /**
     * @ExcludeIf("hasAccessIfTrue(true)")
     */
    public $firstName = 'Jason';
    /**
     * @ExcludeIf("hasAccessIfTrue(true)")
     */
    public $lastName = 'McClellan';
    /**
     * @ExcludeIf("hasAccessIfTrue(false)")
     */
    public $phone = '555-555-5555';
    /**
     * @ExcludeIf("hasAccessIfTrue(false)")
     */
    public $address ='New York, NY';

    public $occupation = 'Software';
}