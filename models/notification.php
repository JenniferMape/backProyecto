<?php

class Notification{
    
    public function getFilterNotification($data){
        
        $notifications=ORM::for_table('notifications')->where($data)->find_many();
        return $this->convertCollection($notifications);
        
    }
    public function createNotification($data){
        $notification=ORM::for_table('notifications')->create();
        $notification->set($data);
        $notification->save();
        
        return $this->convertObj($notification);
    }
    public function updateNotification($id){
        $notification=ORM::for_table('notifications')->find_one($id);
        if($notification){
            //Toogle 
            $notification->is_read_notification = !$notification->is_read_notification;
            $notification->save();
            return $notification->is_read_notification;
        }else{
            return false;
        }

    }
    public function deleteNotification($id){
        $notification=ORM::for_table('notifications')->find_one($id);
        if($notification){
            $notification->delete();
            return true;
        }else{
            return false;
        }
    }
    public function getNotification($id){
        return ORM::for_table('notifications')->find_one($id);
    }

    private function convertObj($obj)
    {
        return [
            'id' => $obj->id ?? null,
            'id_category_notification' => $obj->id_category_notification ?? null,
            'id_offer_notification' => $obj->id_offer_notification ?? null,
            'id_user_notification ' => $obj->id_user_notification  ?? null,
            'id_comment_notification' => $obj->id_comment_notification ?? null,
            'type_notification' => $obj->type_notification ?? null,
            'is_read_notification' => $obj->is_read_notification ?? null,
            'created_notification' => $obj->created_notification ?? null,
            'updated_notification' => $obj->updated_notification ?? null
        ];
    }

    private function convertCollection($collection)
    {
        $result = [];
        foreach ($collection as $item) {

            $result[] = $this->convertObj($item);
        }
        return $result;
    }
}
?>