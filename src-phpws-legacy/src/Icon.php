<?php
namespace phpws;

/**
 * See docs/AUTHORS and docs/COPYRIGHT for relevant info.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 *
 
 * @author Matthew McNaney <mcnaneym@appstate.edu>
 * @package
 * @license http://opensource.org/licenses/gpl-3.0.html
 */
class Icon extends \phpws2\Tag {

    protected $open = true;
    private $type;

    public function __construct($type = null)
    {
        parent::__construct('i');
        if ($type) {
            $this->setType($type);
        }
    }

    public function setType($type)
    {
        $type = strip_tags($type);
        $this->type = preg_replace('/[\s_]/', '-', $type);
        if (empty($this->title)) {
            $this->setTitle(ucwords($type));
        }
    }

    public function setStyle($style)
    {
        $this->addStyle($style);
    }

    public function setAlt($alt)
    {
        $this->setTitle($alt);
    }

    public static function get($type)
    {
        return new self($type);
    }

    public function __toString()
    {
        $this->addIconClass();
        return parent::__toString();
    }

    private function addIconClass()
    {
        switch ($this->type) {
            case 'add':
                $this->addClass('fa fa-plus');
                break;
            case 'approved':
                $this->addClass('far fa-thumbs-up');
                break;

            case 'cancel':
                $this->addClass('fas fa-ban');
                break;
            case 'clear':
                $this->addClass('fas fa-eraser');
                break;
            case 'clip':
                $this->addClass('fas fa-paperclip');
                break;
            case 'close':
                $this->addClass('fas fa-times');
                break;

            case 'delete':
                $this->addClass('far fa-trash-alt');
                break;

            case 'email':
                $this->addClass('far fa-envelope');
                break;

            case 'error':
                $this->addClass('fas fa-exclamation-triangle');
                break;

            case 'image':
                $this->addClass('far fa-image');
                break;

            case 'up':
            case 'down':
                $this->addClass('fa fa-arrow-' . $this->type);
                break;

            case 'active':
                $this->addClass('fas fa-power-off text-success');
                break;

            case 'deactive':
            case 'inactive':
                $this->addClass('fas fa-power-off text-danger');
                break;

            case 'next':
                $this->addClass('fas fa-chevron-right');
                break;

            case 'previous':
                $this->addClass('fas fa-chevron-left');
                break;

            case 'forbidden':
                $this->addClass('fas fa-exclamation-triangle');
                break;

            case 'permission':
                $this->addClass('fas fa-key');
                break;

            default:
                $this->addClass('fas fa-' . $this->type);
        }
    }

    public static function show($type, $title = null)
    {
        $icon = new self($type);
        if ($title) {
            $icon->setTitle($title);
        }
        return $icon->__toString();
    }

    public static function demo()
    {

    }

}
