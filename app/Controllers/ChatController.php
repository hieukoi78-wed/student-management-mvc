<?php

namespace App\Controllers;

use App\Models\ChatMessageModel;
use App\Models\MemberModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class ChatController extends BaseController
{
    public function index()
    {
        $room = $this->roomContext();
        $messages = (new ChatMessageModel())->forRoom($room['type'], $room['member_id']);
        $lastMessage = $messages === [] ? 0 : (int) end($messages)['id'];
        $roomQuery = http_build_query([
            'room' => $room['type'],
            'member_id' => $room['member_id'],
        ]);

        return view('chat/index', [
            'room' => $room['type'],
            'memberId' => $room['member_id'],
            'roomTitle' => $room['title'],
            'messages' => $messages,
            'lastMessageId' => $lastMessage,
            'pollUrl' => site_url('chat/messages') . '?' . $roomQuery,
            'chatUrl' => site_url('chat') . '?' . $roomQuery,
            'members' => session()->get('admin_id') !== null
                ? (new MemberModel())->select('id, full_name, username')->where('is_active', 1)->orderBy('full_name')->findAll()
                : [],
            'errors' => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function messages()
    {
        $room = $this->roomContext();
        $afterId = filter_var(
            $this->request->getGet('after_id') ?? 0,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 0]]
        );
        if ($afterId === false) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'after_id không hợp lệ.']);
        }

        return $this->response->setJSON([
            'messages' => (new ChatMessageModel())->forRoom($room['type'], $room['member_id'], $afterId, 100),
        ]);
    }

    public function send()
    {
        $roomType = $this->request->getPost('room') === 'admin_direct' ? 'admin_direct' : 'lobby';
        $isAdmin = session()->get('admin_id') !== null;
        $memberId = null;

        if ($roomType === 'admin_direct') {
            $memberId = $isAdmin
                ? filter_var($this->request->getPost('member_id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
                : (int) session()->get('member_id');

            if ($memberId === false || $memberId === null || (new MemberModel())->find($memberId) === null) {
                throw PageNotFoundException::forPageNotFound('Không tìm thấy thành viên chat.');
            }
        }

        $message = trim((string) $this->request->getPost('message'));
        if ($message === '' || mb_strlen($message) > 2000) {
            return redirect()->to($this->roomUrl($roomType, $memberId))
                ->with('errors', ['message' => 'Tin nhắn không được để trống và tối đa 2.000 ký tự.']);
        }

        (new ChatMessageModel())->insert([
            'room_type' => $roomType,
            'member_id' => $memberId,
            'sender_type' => $isAdmin ? 'admin' : 'member',
            'sender_id' => (int) session()->get($isAdmin ? 'admin_id' : 'member_id'),
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to($this->roomUrl($roomType, $memberId));
    }

    private function roomContext(): array
    {
        $isAdmin = session()->get('admin_id') !== null;
        $roomType = $this->request->getGet('room') === 'admin_direct' ? 'admin_direct' : 'lobby';
        $memberId = null;
        $title = 'Phòng chat chung';

        if ($roomType === 'admin_direct') {
            $memberId = $isAdmin
                ? filter_var($this->request->getGet('member_id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
                : (int) session()->get('member_id');

            if ($memberId === false || $memberId === null) {
                $roomType = 'lobby';
            } else {
                $member = (new MemberModel())->find($memberId);
                if ($member === null) {
                    throw PageNotFoundException::forPageNotFound('Không tìm thấy thành viên chat.');
                }

                if ($isAdmin) {
                    $title = 'Chat riêng với ' . $member['full_name'];
                } else {
                    $title = 'Chat riêng với quản trị viên';
                }
            }
        }

        return [
            'type' => $roomType,
            'member_id' => $roomType === 'admin_direct' ? (int) $memberId : null,
            'title' => $title,
        ];
    }

    private function roomUrl(string $roomType, ?int $memberId): string
    {
        $query = ['room' => $roomType];
        if ($roomType === 'admin_direct' && $memberId !== null) {
            $query['member_id'] = $memberId;
        }

        return site_url('chat') . '?' . http_build_query($query);
    }
}
