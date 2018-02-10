<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class ElaboratTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getElaborat($id)
    {
        $id  = $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        //if (!$row) {
        //    throw new \Exception("Could not find row $id");
        //}
        return $row;
    }

    public function saveElaborat(Elaborat $elaborat)
    {
        $data = array(
            'id' => $elaborat->id,
            'post' => json_encode($elaborat->post),
        );

        $id = $elaborat->id;
        if ($this->getElaborat($id)) {
            if ($this->getElaborat($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('elaborat id does not exist');
            }
        } else {
            $this->tableGateway->insert($data);
        }
    }

    public function deleteElaborat($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}