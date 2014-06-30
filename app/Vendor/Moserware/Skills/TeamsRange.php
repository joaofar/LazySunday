<?php
namespace Moserware\Skills;

require "Numerics/Range.php";

use Moserware\Skills\Numerics\Range;

class TeamsRange extends Range
{
    public function __construct($min, $max)
    {
        parent::__construct($min, $max);
    }
    
    protected static function create($min, $max)
    {
        return new TeamsRange($min, $max);
    }
}

?>