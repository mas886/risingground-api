<?php

/**
 * Shop of monsters and items
 *
 * @author PATATA
 */
include_once("Token.php");
include_once ('User.php');
include_once("./db/ShopDAO.php");

class Shop {

    public function getArticles($token) {
        if (strlen($token) != 30) {
            return 0;
        }
        $tkn = new Token;
        $userId = $tkn->getUserIdByToken($token);
        if ($userId == "Expired" || $userId == "Bad token") {
            return $userId;
        }
        $dao = new ShopDAO;
        $shop = array("gems" => $dao->getShopGems(), "gold" => $dao->getShopGold());
        return $shop;
    }

    public function buyArticleGold($articleId, $characterName, $token) {
        if (strlen($token) != 30 || strlen($characterName) < 3 || strlen($characterName) > 15 || !is_numeric($articleId)) {
            return 0;
        }
        $tkn = new Token;
        $userId = $tkn->getUserIdByToken($token);
        if ($userId == "Expired" || $userId == "Bad token") {
            return $userId;
        }

        $dao = new ShopDAO;
        $article = $dao->getItemGold($articleId);
        $user = new UserDAO;

        $gold = $user->getGold($userId);
        $amount = intval($article['amount']);
        $value = floatval($article['value']);

        if ($gold < ($amount * $value)) {
            return "No money";
        }
        
        if (!$dao->buy(intval($article['amount']), floatval($article['value']), $userId, "gold")) {
            return "Money transition failed";
        } else {
            return $dao->addCharacterItem(intval($article['itemId']), intval($article['amount']), $characterName);
        }
    }

    public function buyArticleGems($articleId, $characterName, $token) {
        if (strlen($token) != 30 || strlen($characterName) < 3 || strlen($characterName) > 15 || !is_numeric($articleId)) {
            return 0;
        }
        $tkn = new Token;
        $userId = $tkn->getUserIdByToken($token);
        if ($userId == "Expired" || $userId == "Bad token") {
            return $userId;
        }

        $dao = new ShopDAO;
        $article = $dao->getItemGold($articleId);
        $user = new UserDAO;

        $gems = $user->getGems($userId);
        $amount = intval($article['amount']);
        $value = floatval($article['value']);

        if ($gems < ($amount * $value)) {
            return "No money";
        }
        if (!$dao->buy(intval($article['amount']), floatval($article['value']), $userId, "gold")) {
            return "Money transition failed";
        } else {
            return $dao->addCharacterItem(intval($article['itemId']), intval($article['amount']), $characterName);
        }
    }

}