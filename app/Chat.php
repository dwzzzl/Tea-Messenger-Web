<?php

/**
 *  Generated by IceTea Framework 0.0.1
 *  Created at 2017-12-13 23:19:42
 *  Namespace App
 */

namespace App;

use PDO;
use App\User;
use IceTea\Database\DB;
use IceTea\Support\Model;

class Chat extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public static function getList($user_id)
    {
        // $st = DB::prepare("SELECT `private_me`")
    }

    /**
     * @param string $self  user_id
     * @param string $to    username
     */
    public static function getChatInfo($self, $to)
    {
        return User::getInfo($to, "a.username");
    }

    public static function getChatRoom($self, $username)
    {
        $a = User::getInfo($username, "a.username");        
    }

    public static function privatePost($sender, $receiver, $text)
    {
        $st = DB::prepare("INSERT INTO `private_messages` (`sender`, `receiver`, `type`, `is_read`, `reply_to_message_id`, `created_at`, `updated_at`) VALUES (:sender, :receiver, :type, 0, :reply_to_message_id, :created_at, NULL);");
        pc($st->execute(
            [
                ":sender"   => $sender,
                ":receiver" => $receiver,
                ":type"     => 'text',
                ":reply_to_message_id"  => null,
                ":created_at" => date("Y-m-d H:i:s")
            ]
        ), $st);
        $last = DB::pdoInstance()->lastInsertId();
        $st = DB::prepare("INSERT INTO `private_messages_data` (`message_id`, `text`, `file`) VALUES (:message_id, :txt, :file);");
        pc($st->execute(
            [
                ":message_id" => $last,
                ":txt"        => $text,
                ":file"       => null
            ]
        ), $st);
    }

    public static function getPrivateConversation($user1, $user2, $offset = 0)
    {
        $st = DB::prepare("SELECT `a`.`message_id`,`a`.`sender`,`a`.`receiver`,`a`.`type`,`a`.`is_read`,`a`.`reply_to_message_id`,`a`.`created_at`,`a`.`updated_at`,`b`.`text`,`b`.`file` FROM `private_messages` AS `a` INNER JOIN `private_messages_data` AS `b` ON `a`.`message_id`=`b`.`message_id` WHERE (`a`.`sender`=:user_1 AND `a`.`receiver`=:user_2) OR (`a`.`sender`=:user_2 AND `a`.`receiver`=:user_1) ORDER BY `a`.`created_at` DESC LIMIT {$offset},10;");
        pc($st->execute(
            [
                ":user_1" => $user1,
                ":user_2" => $user2
            ]
        ), $st);
        $st =  $st->fetchAll(PDO::FETCH_ASSOC);
        if (! empty($st)) {
            
            
            $std = DB::prepare("UPDATE `private_messages` SET `is_read`=1 WHERE `sender`=:user_1 AND `receiver`=:user_2 AND `message_id` <= :latest;");
            pc($std->execute(
                [
                    ":user_2" => $user2,
                    ":user_1" => $user1,
                    ":latest"=> $st[0]['message_id']
                ]
            ), $std);
            array_walk($st, function (&$a) {
                $a['text'] = htmlspecialchars($a['text']);
            });
            return $st;
        }
        return [];
    }

    public static function getPrivateConversationRealtimeUpdate($user1, $user2, $offset = 0)
    {
        $st = DB::prepare("SELECT `a`.`message_id`,`a`.`sender`,`a`.`receiver`,`a`.`type`,`a`.`is_read`,`a`.`reply_to_message_id`,`a`.`created_at`,`a`.`updated_at`,`b`.`text`,`b`.`file` FROM `private_messages` AS `a` INNER JOIN `private_messages_data` AS `b` ON `a`.`message_id`=`b`.`message_id` WHERE ((`a`.`sender`=:user_1 AND `a`.`receiver`=:user_2) OR (`a`.`sender`=:user_2 AND `a`.`receiver`=:user_1)) AND `a`.`is_read`=0 ORDER BY `a`.`created_at` DESC LIMIT {$offset},10;");
        pc($st->execute(
            [
                ":user_1" => $user1,
                ":user_2" => $user2
            ]
        ), $st);
        $st =  $st->fetchAll(PDO::FETCH_ASSOC);
        if ($st !== false) {
            array_walk($st, function (&$a) {
                $a['text'] = htmlspecialchars($a['text']);
            });
            return $st;
        }
        return [];
    }
}
