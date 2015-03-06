<?php

namespace ToYouDo\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Delete;
use ToYouDo\Entity\TodoList;

/**
 * TodoListTable
 *
 * @author Matias Thomsen
 */
class TodoListTable {
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    /**
     * Get lists created BY $userId.
     * 
     * @param int $userId
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getCreatedLists($userId)
    {
        $userId = (int)$userId;
        
        $subSelect = new Select('user');
        $subSelect->columns(array('username'));
        $subSelect->where('todo_list.friendId = user.user_id');
        
        $select = new Select('todo_list');
        $select->columns(array(
            '*',
            'friendName' => new \Zend\Db\Sql\Expression('?', array($subSelect))
        ));
        
        $select->where->equalTo('userId', $userId);
        
        $resultSet = $this->tableGateway->selectWith($select);
        
        return $resultSet;
    }
    
    /**
     * Get lists created FOR $userId.
     * 
     * @param int $userId
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getToyoudoLists($userId)
    {
        $userId = (int)$userId;
        
        $subSelect = new Select('user');
        $subSelect->columns(array('username'));
        $subSelect->where('todo_list.userId = user.user_id');
        
        $select = new Select('todo_list');
        $select->columns(array(
            '*',
            'friendName' => new \Zend\Db\Sql\Expression('?', array($subSelect))
        ));
        $select->order('deadline DESC');
        $select->where->equalTo('friendId', $userId);
        
        $resultSet = $this->tableGateway->selectWith($select);
        
        return $resultSet;
    }
    
    /**
     * Get OWN list based on provided id if this list belongs
     * to the provided User.
     * 
     * @param int $id
     * @param int $userId
     * @return \ToYouDo\Entity\TodoList
     * @throws \Exception
     */
    public function getEditList($id, $userId)
    {
        $id  = (int) $id;
        
        $subSelect = new Select('user');
        $subSelect->columns(array('username'));
        $subSelect->where('todo_list.friendId = user.user_id');
        
        $select = new Select('todo_list');
        $select->columns(array(
            '*',
            'friendName' => new \Zend\Db\Sql\Expression('?', array($subSelect))
        ));
        
        $select->where->equalTo('userId', $userId);
        $select->where->equalTo('id', $id);
        
        $resultSet = $this->tableGateway->selectWith($select);
        $row = $resultSet->current();
        if (!$row) {
            throw new \Exception("Unknown list with ID $id");
        }
        
        return $row;
    }
    
    /**
     * Get FRIEND list based on provided id if this list belongs
     * to the provided User.
     * 
     * @param int $id
     * @param int $userId
     * @return type
     * @throws \Exception
     */
    public function getFriendList($id, $userId)
    {
        $id  = (int) $id;
        
        $subSelect = new Select('user');
        $subSelect->columns(array('username'));
        $subSelect->where('todo_list.friendId = user.user_id');
        
        $select = new Select('todo_list');
        $select->columns(array(
            '*',
            'friendName' => new \Zend\Db\Sql\Expression('?', array($subSelect))
        ));
        
        $select->where->equalTo('friendId', $userId);
        $select->where->equalTo('id', $id);
        
        $resultSet = $this->tableGateway->selectWith($select);
        $row = $resultSet->current();
        if (!$row) {
            throw new \Exception("Unknown list with ID $id");
        }
        
        return $row;
    }


    /**
     * Insert new TodoList if id is not set, otherwise update provided TodoList
     * 
     * @param ToYouDo\Entity\TodoList $list
     */
    public function save(TodoList $list)
    {
        $list->setDate(date("Y-m-d H:i:s"));
        $data = $list->getArrayCopy();
        
        unset ($data['userName']);
        unset ($data['friendName']);
        
        if (empty($data['id'])) {
            $this->tableGateway->insert($data);
        } else {
            unset($data['id']);
            
            $this->tableGateway->update($data, array(
                'id' => $list->getId()
            ));
        }
    }
    
    /**
     * 
     * @param ToYouDo\Entity\TodoList $list
     */
    public function delete(TodoList $list)
    {
        $this->tableGateway->delete(array('id' => (int) $list->getId()));
    }
    
    /**
     * 
     * @param ToYouDo\Entity\TodoList $list
     */
    public function uncheck(TodoList $list)
    {
        $this->tableGateway->update(array('status' => '0'), array('id' => (int) $list->getId()));
    }
    
    /**
     * 
     * @param ToYouDo\Entity\TodoList $list
     */
    public function check(TodoList $list)
    {
        $this->tableGateway->update(array('status' => '1'), array('id' => (int) $list->getId()));
    }
    
    /**
     * Delete all TodoLists related to provided userId AND friendId
     * @param int $userId
     * @param int $friendId
     */
    public function deleteAll($userId, $friendId)
    {
        $delete = new Delete('todo_list');
        $delete->where
                    ->nest()
                        ->equalTo('userId', $userId)
                        ->and
                        ->equalTo('friendId', $friendId)
                    ->unnest()
                ->or
                    ->nest()
                        ->equalTo('userId', $friendId)
                        ->and
                        ->equalTo('friendId', $userId)
                    ->unnest();
        
        $this->tableGateway->deleteWith($delete);
    }
}

?>
