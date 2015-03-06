<?php

namespace ToYouDo\Model;

use Zend\Db\TableGateway\TableGateway;
use ToYouDo\Entity\TodoList;
use ToYouDo\Entity\Item;
/**
 * Description of TodoListItemTable
 *
 * @author Matias Thomsen
 */
class TodoListItemTable {
    protected $tableGateway;

    /**
     * 
     * @param TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    /**
     * 
     * @param \ToYouDo\Entity\TodoList $list
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getItems(TodoList $list)
    {   
        $resultSet = $this->tableGateway->select(array(
            'listId' => (int) $list->getId(),
        ));
        
        return $resultSet;
    }
    
    /**
     * 
     * @param int $id
     * @return \ToYouDo\Entity\Item
     * @throws \Exception
     */
    public function getListItem($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array(
            'id' => $id,
        ));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Unknown item with ID $id");
        }
        return $row;
    }
    
    /**
     * Insert new Item if id is not set, otherwise update provided Item
     * @param \ToYouDo\Entity\Item $item
     */
    public function save(Item $item)
    {
        $data = $item->getArrayCopy();
        
        if (empty($data['id'])) {
            $this->tableGateway->insert($data);
        } else {
            unset($data['id']);
            
            $this->tableGateway->update($data, array(
                'id' => $item->getId()
            ));
        }
    }
    
    /**
     * 
     * @param int $id
     */
    public function delete($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}

?>
