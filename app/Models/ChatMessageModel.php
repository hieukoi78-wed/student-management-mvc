<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatMessageModel extends Model
{
    protected $table = 'chat_messages';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['room_type', 'member_id', 'sender_type', 'sender_id', 'message'];
    protected $useTimestamps = false;

    public function forRoom(string $roomType, ?int $memberId = null, int $afterId = 0, int $limit = 100): array
    {
        $builder = $this->select(
            "chat_messages.id, chat_messages.room_type, chat_messages.member_id,
            chat_messages.sender_type, chat_messages.sender_id, chat_messages.message,
            chat_messages.created_at,
            CASE WHEN chat_messages.sender_type = 'admin' THEN admins.full_name ELSE members.full_name END AS sender_name"
        )
            ->join(
                'admins',
                "chat_messages.sender_type = 'admin' AND admins.id = chat_messages.sender_id",
                'left',
                false
            )
            ->join(
                'members',
                "chat_messages.sender_type = 'member' AND members.id = chat_messages.sender_id",
                'left',
                false
            )
            ->where('chat_messages.room_type', $roomType)
            ->where('chat_messages.id >', $afterId);

        if ($roomType === 'admin_direct') {
            $builder->where('chat_messages.member_id', $memberId);
        } else {
            $builder->where('chat_messages.member_id', null);
        }

        return $builder->orderBy('chat_messages.id', 'ASC')->findAll($limit);
    }
}
