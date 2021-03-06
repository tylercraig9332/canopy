<?php

namespace phpws2\Database;

/**
 *
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 * @license http://opensource.org/licenses/lgpl-3.0.html
 */
class Exists extends Conditional {

    private $exists;
    private $subselect;

    public function __construct(Subselect $subselect, $exists = true)
    {
        $this->setSubselect($subselect);
        $this->setExists($exists);
    }

    public function setSubselect(Subselect $subselect)
    {
        $this->subselect = $subselect;
    }

    public function setExists($exists)
    {
        $this->exists = (bool) $exists;
    }

    public function __toString()
    {
        $cond = $this->exists ? 'EXISTS ' : 'NOT EXISTS ';
        $cond .= (string)$this->subselect;
        return $cond;
    }

}

