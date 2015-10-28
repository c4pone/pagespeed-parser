<?php namespace c4pone\PageSpeed;

class Screenshot { 

    private $data;

    public function hasData()
    {
        return $this->data != null;
    }

    public function setData($data)
    {
        $this->data = $data; 
    }

    public function getData()
    {
        return $this->data; 
    }

    public function save($path)
    {
        $data = str_replace(
            array('_', '-'), 
            array('/', '+'),
            $this->data);

        $data = base64_decode($data);

        file_put_contents($path, $data);
    }
}
