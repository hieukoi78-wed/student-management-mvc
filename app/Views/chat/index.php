<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="mb-3">
    <div>
        <h1 class="h3 mb-1">Chat nhà sách</h1>
        <p class="text-secondary mb-0">Tin nhắn cập nhật tự động mỗi 3 giây.</p>
    </div>
</div>

<?php foreach ($errors as $error): ?><div class="alert alert-danger"><?= esc($error) ?></div><?php endforeach; ?>

<div class="row g-3">
    <aside class="col-lg-3">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Phòng chat</div>
            <div class="list-group list-group-flush">
                <a class="list-group-item list-group-item-action <?= $room === 'lobby' ? 'active' : '' ?>" href="<?= site_url('chat') ?>">Phòng chung</a>
                <?php if (session()->get('admin_id') !== null): ?>
                    <?php foreach ($members as $member): ?>
                        <a class="list-group-item list-group-item-action <?= $room === 'admin_direct' && $memberId === (int) $member['id'] ? 'active' : '' ?>" href="<?= site_url('chat') . '?room=admin_direct&member_id=' . (int) $member['id'] ?>">
                            <?= esc($member['full_name']) ?>
                            <small class="d-block opacity-75">@<?= esc($member['username']) ?></small>
                        </a>
                    <?php endforeach; ?>
                    <?php if ($members === []): ?><div class="list-group-item text-secondary">Chưa có thành viên đăng ký.</div><?php endif; ?>
                <?php else: ?>
                    <a class="list-group-item list-group-item-action <?= $room === 'admin_direct' ? 'active' : '' ?>" href="<?= site_url('chat') . '?room=admin_direct' ?>">Chat riêng với quản trị viên</a>
                <?php endif; ?>
            </div>
        </div>
    </aside>
    <section class="col-lg-9">
        <div class="card">
            <div class="card-header bg-white fw-semibold"><?= esc($roomTitle) ?></div>
            <div class="chat-messages" id="chat-messages" aria-live="polite">
                <?php if ($messages === []): ?>
                    <p class="text-secondary text-center my-4" id="chat-empty">Chưa có tin nhắn. Hãy bắt đầu trò chuyện.</p>
                <?php else: ?>
                    <?php foreach ($messages as $message): ?>
                        <?php $isMine = ($message['sender_type'] === 'admin' && session()->get('admin_id') !== null && (int) $message['sender_id'] === (int) session()->get('admin_id')) || ($message['sender_type'] === 'member' && session()->get('member_id') !== null && (int) $message['sender_id'] === (int) session()->get('member_id')); ?>
                        <article class="chat-message <?= $isMine ? 'chat-message-mine' : '' ?>" data-message-id="<?= esc((string) $message['id']) ?>">
                            <div class="chat-message-meta"><?= esc($message['sender_name'] ?? 'Tài khoản') ?> · <?= esc($message['created_at']) ?></div>
                            <div class="chat-message-body"><?= nl2br(esc($message['message'])) ?></div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="card-body border-top">
                <form method="post" action="<?= site_url('chat/send') ?>" class="d-flex gap-2">
                    <?= csrf_field() ?>
                    <input type="hidden" name="room" value="<?= esc($room) ?>">
                    <?php if ($room === 'admin_direct'): ?><input type="hidden" name="member_id" value="<?= esc((string) $memberId) ?>"><?php endif; ?>
                    <label class="visually-hidden" for="message">Tin nhắn</label>
                    <input class="form-control" id="message" name="message" maxlength="2000" placeholder="Nhập tin nhắn..." autocomplete="off" required>
                    <button class="btn btn-primary" type="submit">Gửi</button>
                </form>
            </div>
        </div>
    </section>
</div>

<script>
(() => {
    const container = document.getElementById('chat-messages');
    let lastId = <?= (int) $lastMessageId ?>;
    const pollUrl = <?= json_encode($pollUrl, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

    const appendMessage = (message) => {
        document.getElementById('chat-empty')?.remove();
        const article = document.createElement('article');
        const mine = (message.sender_type === 'admin' && <?= session()->get('admin_id') !== null ? 'true' : 'false' ?> && Number(message.sender_id) === <?= (int) (session()->get('admin_id') ?? 0) ?>)
            || (message.sender_type === 'member' && <?= session()->get('member_id') !== null ? 'true' : 'false' ?> && Number(message.sender_id) === <?= (int) (session()->get('member_id') ?? 0) ?>);
        article.className = `chat-message${mine ? ' chat-message-mine' : ''}`;
        article.dataset.messageId = message.id;
        const meta = document.createElement('div');
        meta.className = 'chat-message-meta';
        meta.textContent = `${message.sender_name || 'Tài khoản'} · ${message.created_at}`;
        const body = document.createElement('div');
        body.className = 'chat-message-body';
        body.textContent = message.message;
        article.append(meta, body);
        container.append(article);
        lastId = Math.max(lastId, Number(message.id));
        container.scrollTop = container.scrollHeight;
    };

    container.scrollTop = container.scrollHeight;
    window.setInterval(async () => {
        try {
            const response = await fetch(`${pollUrl}&after_id=${lastId}`, {headers: {'Accept': 'application/json'}});
            if (!response.ok) return;
            const data = await response.json();
            data.messages.forEach(appendMessage);
        } catch (error) {
            console.error('Không thể tải tin nhắn mới:', error);
        }
    }, 3000);
})();
</script>
<?= $this->endSection() ?>
