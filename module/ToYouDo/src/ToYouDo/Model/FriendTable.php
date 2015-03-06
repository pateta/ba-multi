<?php

namespace ToYouDo\Model;

use Zend\Db\TableGateway\TableGateway;
use ToYouDo\Entity\Friend;
use Zend\Db\Sql\Select;
/**
 * Description of FriendTable
 *
 * @author Matias Thomsen
 */
class FriendTable {
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    /**
     * Get list of friends
     * 
     * @param int $userId
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getFriends($userId = 0)
    {
        $userId = (int)$userId;
        $subSelect = new Select('friend');
        $subSelect->join('user', 'friend_userId = user_id', array(
            'friend_username' => 'username',
            'friendId' => 'user_id',
            ));
        $subSelect->where->equalTo('user_userId', $userId);
        $subSelect->where->equalTo('accepted', 1);
        
        $select = new Select('friend');
        $select->join('user', 'user_userId = user_id', array(
            'friend_username' => 'username',
            'friendId' => 'user_id',
            ));
        $select->where->equalTo('friend_userId', $userId);
        $select->where->equalTo('accepted', 1);
        $select->combine($subSelect);
        
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
    
    /**
     * Get list of friends marked as invited friends - outgoing
     * 
     * @param int $userId
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getOutgoingInvitations($userId = 0)
    {
        $userId = (int)$userId;
        $select = new Select('friend');
        $select->join('user', 'friend_userId = user_id', array('friend_username' => 'username'));
        $select->where->equalTo('user_userId', $userId);
        $select->where->notEqualTo('accepted', 1);
        
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
    
    /**
     * Get list of friends marked as invited friends - incoming
     * 
     * @param int $userId
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getInvitations($userId = 0)
    {
        $userId = (int)$userId;
        $select = new Select('friend');
        $select->join('user', 'user_userId = user_id', array('friend_username' => 'username'));
        $select->where->equalTo('friend_userId', $userId);
        $select->where->equalTo('accepted', 0);
        
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
    
    /**
     * 
     * @param int $id
     * @return \ToYouDo\Entity\Friend
     * @throws \Exception
     */
    public function getFriend($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array(
            'id' => $id,
        ));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Unknown friend with ID $id");
        }
        return $row;
    }
    
    /**
     * Insert new Friend if id is not set, otherwise update provided Friend
     * 
     * @param \ToYouDo\Entity\Friend $friend
     */
    public function save(Friend $friend)
    {
        $data = $friend->getArrayCopy();
        unset($data['user_username']);
        unset($data['friend_username']);
        unset($data['friendId']);
        if (empty($data['id'])) {
            $this->tableGateway->insert($data);
        } else {
            unset($data['id']);
            
            $this->tableGateway->update($data, array(
                'id' => $friend->getId()
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
