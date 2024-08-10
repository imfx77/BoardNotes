<?php

/**
 * Class TodoNotesNotificationsModel
 * @package Kanboard\Plugin\TodoNotes\Model
 * @author  Im[F(x)]
 */

namespace Kanboard\Plugin\TodoNotes\Model;

use Kanboard\Core\Base;

class TodoNotesNotificationsModel extends Base
{
    private const TABLE_NOTES_WEBPN_SUBSCRIPTIONS   = 'todonotes_webpn_subscriptions';

    private const OPTIONS_FLAG_ALERT_MAIL           = 0x00000001;    // 0000-0000-0000-0000 0000-0000-0000-0001
    private const OPTIONS_FLAG_ALERT_WEBPN          = 0x00000002;    // 0000-0000-0000-0000 0000-0000-0000-0010
    private const OPTIONS_FLAG_ALERT_BEFORE1DAY     = 0x00000004;    // 0000-0000-0000-0000 0000-0000-0000-0100
    private const OPTIONS_FLAG_ALERT_BEFORE1HOUR    = 0x00000008;    // 0000-0000-0000-0000 0000-0000-0000-1000
    private const OPTIONS_FLAG_ALERT_AFTER1DAY      = 0x00000010;    // 0000-0000-0000-0000 0000-0000-0001-0000
    private const OPTIONS_FLAG_ALERT_AFTER1HOUR     = 0x00000020;    // 0000-0000-0000-0000 0000-0000-0010-0000

    private const OPTIONS_FLAG_POSTPONE             = 0x00010000;    // 0000-0000-0000-0001 0000-0000-0000-0000
    private const OPTIONS_MASK_POSTPONE_TYPE        = 0x000E0000;    // 0000-0000-0000-1110 0000-0000-0000-0000
    private const OPTIONS_MASK_POSTPONE_VALUE       = 0xFFF00000;    // 1111-1111-1111-0000 0000-0000-0000-0000
    private const OPTIONS_MASK_POSTPONE_TYPE_IX     = 17;
    private const OPTIONS_MASK_POSTPONE_VALUE_IX    = 20;

    public function GetWebPNSubscriptionsForUser($user_id)
    {
        $encoded_subscriptions = $this->db->table(self::TABLE_NOTES_WEBPN_SUBSCRIPTIONS)
            ->columns('subscription')
            ->eq('user_id', $user_id)
            ->findAll();

        $decoded_subscriptions = array();
        foreach ($encoded_subscriptions as $encoded_subscription) {
            $decoded_subscriptions[] = json_decode($encoded_subscription['subscription'], true);
        }

        return $decoded_subscriptions;
    }

    public function UpdateWebPNSubscription($user_id, $webpn_subscription)
    {
        // check existing subscription
        $existing = $this->db->table(self::TABLE_NOTES_WEBPN_SUBSCRIPTIONS)
            ->eq('endpoint', $webpn_subscription['endpoint'])
            ->findOne();

        if (!$existing) {
            // add new subscription
            $valuesNew = array(
                'endpoint' => $webpn_subscription['endpoint'],
                'user_id' => $user_id,
                'subscription' => json_encode($webpn_subscription),
            );
            $this->db->table(self::TABLE_NOTES_WEBPN_SUBSCRIPTIONS)
                ->insert($valuesNew);
        } else {
            // update existing subscription
            $valuesExisting = array(
                'user_id' => $user_id,
                'subscription' => json_encode($webpn_subscription),
            );
            $this->db->table(self::TABLE_NOTES_WEBPN_SUBSCRIPTIONS)
                ->eq('endpoint', $webpn_subscription['endpoint'])
                ->update($valuesExisting);
        }
    }

    public function RemoveWebPNSubscription($user_id, $webpn_subscription)
    {
        return $this->db->table(self::TABLE_NOTES_WEBPN_SUBSCRIPTIONS)
            ->eq('user_id', $user_id)
            ->eq('endpoint', $webpn_subscription['endpoint'])
            ->remove();
    }

    private function NotificationsOptionsToBitflags($notification_options)
    {
        $notification_options_bitflags = 0;

        if ($notification_options['alert_mail']) {
            $notification_options_bitflags |= self::OPTIONS_FLAG_ALERT_MAIL;
        }
        if ($notification_options['alert_webpn']) {
            $notification_options_bitflags |= self::OPTIONS_FLAG_ALERT_WEBPN;
        }
        if ($notification_options['alert_before1day']) {
            $notification_options_bitflags |= self::OPTIONS_FLAG_ALERT_BEFORE1DAY;
        }
        if ($notification_options['alert_before1hour']) {
            $notification_options_bitflags |= self::OPTIONS_FLAG_ALERT_BEFORE1HOUR;
        }
        if ($notification_options['alert_after1day']) {
            $notification_options_bitflags |= self::OPTIONS_FLAG_ALERT_AFTER1DAY;
        }
        if ($notification_options['alert_after1hour']) {
            $notification_options_bitflags |= self::OPTIONS_FLAG_ALERT_AFTER1HOUR;
        }

        if ($notification_options['postpone']) {
            $notification_options_bitflags |= self::OPTIONS_FLAG_POSTPONE;
        }
        $notification_options_bitflags |= (self::OPTIONS_MASK_POSTPONE_TYPE & ($notification_options['postpone_type'] << self::OPTIONS_MASK_POSTPONE_TYPE_IX));
        $notification_options_bitflags |= (self::OPTIONS_MASK_POSTPONE_VALUE & ($notification_options['postpone_value'] << self::OPTIONS_MASK_POSTPONE_VALUE_IX));

        return $notification_options_bitflags;
    }

    private function NotificationsOptionsFromBitflags($notification_options_bitflags)
    {
        $notification_options = array();

        $notification_options['alert_mail'] = ($notification_options_bitflags & self::OPTIONS_FLAG_ALERT_MAIL) ? true : false;
        $notification_options['alert_webpn'] = ($notification_options_bitflags & self::OPTIONS_FLAG_ALERT_WEBPN) ? true : false;
        $notification_options['alert_before1day'] = ($notification_options_bitflags & self::OPTIONS_FLAG_ALERT_BEFORE1DAY) ? true : false;
        $notification_options['alert_before1hour'] = ($notification_options_bitflags & self::OPTIONS_FLAG_ALERT_BEFORE1HOUR) ? true : false;
        $notification_options['alert_after1day'] = ($notification_options_bitflags & self::OPTIONS_FLAG_ALERT_AFTER1DAY) ? true : false;
        $notification_options['alert_after1hour'] = ($notification_options_bitflags & self::OPTIONS_FLAG_ALERT_AFTER1HOUR) ? true : false;

        $notification_options['postpone'] = ($notification_options_bitflags & self::OPTIONS_FLAG_POSTPONE) ? true : false;
        $notification_options['postpone_type'] = (($notification_options_bitflags & self::OPTIONS_MASK_POSTPONE_TYPE) >> self::OPTIONS_MASK_POSTPONE_TYPE_IX);
        $notification_options['postpone_value'] = (($notification_options_bitflags & self::OPTIONS_MASK_POSTPONE_VALUE) >> self::OPTIONS_MASK_POSTPONE_VALUE_IX);

        return $notification_options;
    }
}
