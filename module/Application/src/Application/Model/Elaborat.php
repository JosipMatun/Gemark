<?php

namespace Application\Model;

class Elaborat
{
    public $id;
    public $post;

    public function exchangeArray($data)
    {
        $this->id     = (!empty($data['id'])) ? $data['id'] : null;
        $this->post = (!empty($data['post'])) ? $data['post'] : null;
    }
}