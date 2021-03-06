<?php
namespace phpws2\View;
/**
 *
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 * @license http://opensource.org/licenses/lgpl-3.0.html
 */
class HtmlView implements \phpws2\View {

    private $content;

    public function __construct($content)
    {
        $this->setContent($content);
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function render()
    {
        return $this->getContent();
    }

    public function getContentType()
    {
        return 'text/html';
    }

}
