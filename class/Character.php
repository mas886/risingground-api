<?php

include_once("../db/CharacterDAO.php");
include_once("Token.php");

class Character {

    //ADD character
    function addCharacter($characterName, $token) {
        //Add character to db given token
        if (strlen($characterName) > 20 && strlen($characterName) < 1 && strlen($token) != 30) {
            return 0;
        }
        $tkn = new Token;
        $userId = $tkn->getUserIdByToken($token);
        if ($userId == "Expired" || $userId == "Bad token") {
            return $userId;
        }
        if ($this->exist($characterName)) {
            return "Name exist";
        }
        //Return 1 if is succesfull, 0 if character is not added
        $dao = new CharacterDAO;
        return $dao->insertCharacterIntoDb($characterName, $userId);
    }

    //LIST character
    function characterList($token) {
        //Returns a list with all the characters's ID of the user given token
        if (strlen($token) != 30) {
            return 0;
        }
        $tkn = new Token;
        $userId = $tkn->getUserIdByToken($token);
        if ($userId == "Expired" || $userId == "Bad token") {
            return $userId;
        }
        $dao = new CharacterDAO;
        return $dao->selectCharacterList($userId);
    }

    //GET EXPERIENCE
    function getExp($characterName, $token) {
        //Returns the character's experience given token
        if (strlen($characterName) > 20 && strlen($characterName) < 1 && $token != 30) {
            return 0;
        }
        $tkn = new Token;
        $userId = $tkn->getUserIdByToken($token);
        if ($userId == "Expired" || $userId == "Bad token") {
            return $userId;
        }
        if (!$this->exist($characterName)) {
            return 0;
        }
        $dao = new CharacterDAO;
        return $dao->selectExp($characterName);
    }

    //ADD EXPERIENCE

    function addExp($battleExp, $characterName, $token) {
        if ($token != 30 && !ctype_digit($battleExp) && strlen($characterName) > 12 && strlen($characterName) < 1) {
            return 0;
        }
        $tkn = new Token;
        $userId = $tkn->getUserIdByToken($token);
        if ($userId == "Expired" || $userId == "Bad token") {
            return $userId;
        }
        $dao = new CharacterDAO;
        return $dao->updateExp($battleExp, $characterName, $userId);
    }

    //SELECT BUILD
    function selectBuild($buildId, $characterName, $token) {
        //select the build for battle of the character
        if (strlen($characterName) > 20 && strlen($characterName) < 1 && strlen($token) != 30 && !ctype_digit($buildId)) {
            return 0;
        }
        $tkn = new Token;
        $userId = $tkn->getUserIdByToken($token);
        if ($userId == "Expired" || $userId == "Bad token") {
            return $userId;
        }
        $dao = new CharacterDAO;
        return $dao->updateBuild($buildId, $characterName);
    }

    //VALIDATE general function

    function exist($characterName) {
        //Check name existence on user_character
        $dao = new CharacterDAO;
        return $dao->exist($characterName);
    }

}
