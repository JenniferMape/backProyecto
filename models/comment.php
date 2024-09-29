<?php
class Comment
{

    public function getAllComments()
    {
        $comments = ORM::for_table('comments')->find_many();
        return $this->convertCollection($comments);
    }

    public function getCommentById($id)
    {
        $comment = ORM::for_table('comments')->find_one($id);
        return $comment ? $this->convertObj($comment) : null;
    }

    public function getCommentsByUser($id_user)
    {
        $comments = ORM::for_table('comments')->where('id_user_comment', $id_user)->find_many();
        return $this->convertCollection($comments);
    }

    public function getCommentsByOffer($id_offer)
    {
        $comments = ORM::for_table('comments')->where('id_offer_comment', $id_offer)->find_many();
        return $this->convertCollection($comments);
    }

   
    public function addComment($data)
    {
        if (empty($data['id_user_comment']) || empty($data['id_offer_comment']) || empty($data['message_comment'])) {
            return false;
        }

        $comment = ORM::for_table('comments')->create();
        $comment->id_user_comment = $data['id_user_comment'];
        $comment->id_offer_comment = $data['id_offer_comment'];
        $comment->message_comment = $data['message_comment'];
        $comment->id_response_comment = $data['id_response_comment']; 
        $comment->save();

        return $this->convertObj($comment);
    }

    public function updateComment($data)
    {
        $comment = ORM::for_table('comments')->find_one($data['id']);
        //Comprueba que el comentario pertenece al usuario
        if($comment->id_user_comment != $data['id_user_comment']){
            return false;
        }
        elseif ($comment) {
            $comment->id_response_comment = $data['id_response_comment'] ?? $comment->id_response_comment;
            $comment->message_comment = $data['message_comment'] ?? $comment->message_comment;
            $comment->save();
            return true;
        }else{
           return false; 
        }

    }

    public function deleteComment($data)
    {
        $comment = ORM::for_table('comments')->find_one($data['id']);
        $id_user = $data['id_user_comment'];
        //Comprueba que el comentario pertenece al usuario
        if($comment->id_user_comment != $id_user){
            return false;
        }
        elseif ($comment) {
            $comment->delete();
            return true;
        }else{
            return false;
        }
    }

    private function convertObj($obj)
    {
        return [
            'id' => $obj->id ?? null,
            'id_user_comment' => $obj->id_user_comment ?? null,
            'id_offer_comment' => $obj->id_offer_comment ?? null,
            'id_response_comment' => $obj->id_response_comment ?? null,
            'message_comment' => $obj->message_comment ?? null,
            'created_comment' => $obj->created_comment ?? null,
            'updated_comment' => $obj->updated_comment ?? null
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
