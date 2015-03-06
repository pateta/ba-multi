<?php

namespace ToYouDo\Model;


use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
/**
 * Description of UserTable
 *
 * @author Matias Thomsen
 */
class UserTable {
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    /**
     * Search for Users not assigned as friend.
     * @param string $key
     * @param int $userId
     * @return boolean|\Zend\Db\ResultSet\ResultSet Returns a max. of 25 matches
     */
    public function searchInvitableUser($key, $userId)
    {
        if ($key == "") {
            return false;
        }
        $key = "%" . $key . "%";
        
        $subSelect = new Select('friend');
        $subSelect->columns(array('test' => 'friend_userId'));
        $subSelect->where
                ->nest
                    ->equalTo('user_userId', $userId)
                    ->or
                    ->equalTo('friend_userId', $userId)
                ->unnest;
        $subSelect2 = new Select('friend');
        $subSelect2->columns(array('test' => 'user_userId'));
        $subSelect2->where
                ->nest
                    ->equalTo('user_userId', $userId)
                    ->or
                    ->equalTo('friend_userId', $userId)
                ->unnest;
        
        $test = $this->tableGateway->getSql()->getSqlstringForSqlObject($subSelect);
        $select = new Select('user');
        $select->where->like('username', $key);
        $select->where->notEqualTo('user_id', $userId);
        $select->where->notIn('user_id', $subSelect);
        $select->where->notIn('user_id', $subSelect2);
        $select->order('username ASC')->limit(25);
                
        $resultSet = $this->tableGateway->selectWith($select);
        
        return $resultSet;
    }
    
    /**
     * 
     * @param int $id
     * @return \ToYouDo\Entity\User
     * @throws \Exception
     */
    public function getUser($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array(
            'user_id' => $id,
        ));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Unknown user with ID $id");
        }
        return $row;
    }
}

?>
