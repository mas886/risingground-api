<?php

/**
 * Class to connect to character_monsters table
 *
 * @author PATATA and mas886
 */
include_once "./class/config.php";

class CharacterMonsterDAO {

    public function insertCharacterMonster($monsterName, $characterName) {
        $connection = connect();
        $sql = "INSERT INTO `character_monster` (`characterId`, `monsterId`) VALUES ((SELECT `id` FROM `user_character` WHERE `name` = :characterName),(SELECT `id` FROM monster WHERE `name` = :monsterName))";
        $sth = $connection->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->execute(array(':characterName' => $characterName, ':monsterName' => $monsterName));
        if ($sth->rowCount() != 0) {
            $characterMonsterId = mysqli_insert_id();
            return $this->setBaseStats($characterMonsterId);;
        } else {
            return 0;
        }
    }

    public function deleteCharacterMonster($characterMonsterId) {
        $connection = connect();
        $sql = "DELETE FROM `character_monster` WHERE `id` = :characterMonsterId;";
        $sth = $connection->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->execute(array(':characterMonsterId' => $characterMonsterId));
        if ($sth->rowCount() != 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function characterMonsterList($characterName) {
        //Return a array if there's some monster and empty array if there's no one
        $connection = connect();
        $sql = "SELECT `monsterId`,`id`,`experience`,`statsModifier` FROM `character_monster` WHERE characterId = (SELECT `id` FROM `user_character` WHERE name = :name)";
        $sth = $connection->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->execute(array(':name' => $characterName));
        $monsters = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $monsters;
    }

    function getCharacterMonster($characterMonsterId) {
        $connection = connect();
        $sql = "SELECT `id`, `experience`, 
            ((SELECT `accuracy` FROM `monster_stats` WHERE `monster_stats`.`monsterId`=`character_monster`.`monsterId`)+`character_monster_stats`.`accuracy`)as `accuracy`, 
            ((SELECT `speed` FROM `monster_stats` WHERE `monster_stats`.`monsterId`=`character_monster`.`monsterId`)+`character_monster_stats`.`speed`) as `speed`, 
            ((SELECT `strength` FROM `monster_stats` WHERE `monster_stats`.`monsterId`=`character_monster`.`monsterId`)+`character_monster_stats`.`strength`) as `strength`,
            ((SELECT `vitality` FROM `monster_stats` WHERE `monster_stats`.`monsterId`=`character_monster`.`monsterId`)+`character_monster_stats`.`vitality`) as `vitality`, 
            ((SELECT `defence` FROM `monster_stats` WHERE `monster_stats`.`monsterId`=`character_monster`.`monsterId`)+`character_monster_stats`.`defence`) as `defence` 
            FROM `character_monster` JOIN `character_monster_stats` WHERE `id`= :characterMonsterId";
        $sth = $connection->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->execute(array(':characterMonsterId' => $characterMonsterId));
        $monster = $sth->fetch(PDO::FETCH_ASSOC);
        return $monster;
    }

    public function addExp($experience, $characterMonsterId, $userId) {
        $connection = connect();
        $checkOwner = $this->checkMonsterOwner($characterMonsterId, $userId);
        if ($checkOwner == 1) {
            $sql = "UPDATE `character_monster` SET `experience` = `experience` + :experience WHERE `id` = :characterMonsterId";
            $sth = $connection->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $sth->execute(array(':experience' => $experience, ':characterMonsterId' => $characterMonsterId));
            if ($sth->rowCount() != 0) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return $checkOwner;
        }
    }

    private function checkMonsterOwner($characterMonsterId, $userId) {
        $connection = connect();
        $sql = "SELECT `userId` FROM `user_character` WHERE `id` = (SELECT `characterId` FROM `character_monster` WHERE `id` = :characterMonsterId)";
        $sth = $connection->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->execute(array(':characterMonsterId' => $characterMonsterId));
        $userIdValue = $sth->fetch(PDO::FETCH_ASSOC);
        if ($userIdValue['userId'] == $userId) {
            return 1;
        } else {
            return "Owner Error";
        }
    }

    private function setBaseStats($characterMonsterId) {
        $sql = "INSERT INTO `character_monster_stats` (characterMonsterId, accuracy, speed, strength, vitality, defence) VALUES (:characterMonsterId, 0, 0, 0, 0, 0)";
        $sth = $connection->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->execute(array('characterMonsterId' => $characterMonsterId));
        if($sth->rowCount() != 0){
            return 1;
        }else{
            return "Stats error";
        }
    }

}
