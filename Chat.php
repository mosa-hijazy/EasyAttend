<?php
session_start();
<<<<<<< HEAD
require_once __DIR__ . '/lang_loader.php';           
=======
require_once __DIR__ . '/lang_loader.php';            // ← load translations
>>>>>>> 267c7f8db6d46cb8eac9b5208e409ad222c0d007
require_once __DIR__ . '/classes/ChatManager.php';
require_once __DIR__ . '/classes/UserManager.php';
include __DIR__ . '/navbar.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$user        = $_SESSION['user'];
$userId      = $user['username'];
$chatManager = new ChatManager();
$userManager = new UserManager();

$otherUserId = $_GET['user'] ?? null;
$users       = $userManager->getAllUsers();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'], $_POST['to'])) {
    $chatManager->sendMessage($userId, $_POST['to'], trim($_POST['message']));
<<<<<<< HEAD
    header("Location: chat.php?user=" . urlencode($_POST['to']));
    exit;
}

$conversation = $otherUserId ? $chatManager->getMessages($userId, $otherUserId) : [];
$conversation = $conversation ?? [];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang'] ?? 'en', ENT_QUOTES, 'UTF-8') ?>">
=======
    header("Location: chat.php?user=" . $_POST['to']);
    exit;
}

$conversation = $otherUserId
    ? $chatManager->getMessages($userId, $otherUserId)
    : [];
$conversation = $conversation ?? [];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang'] ?? 'en') ?>">
>>>>>>> 267c7f8db6d46cb8eac9b5208e409ad222c0d007
<head>
  <meta charset="UTF-8">
  <title><?= __('chat') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { 
      background: #0f2027;
      color: white;
      height: 100vh;
      margin: 0;
      font-family: Arial, sans-serif;
    }
    .chat-container { display: flex; height: calc(100vh - 60px); }
    .contacts-panel {
      width: 30%; background: #1e1e1e; border-right: 1px solid #555; overflow-y: auto;
    }
    .chat-panel {
      width: 70%; display: flex; flex-direction: column; background: #0f2027;
    }
<<<<<<< HEAD
    .contact { padding: 12px 16px; border-bottom: 1px solid #555; cursor: pointer; transition: background 0.3s; display: flex; align-items: center; text-decoration: none; color: inherit; }
=======
    .contact { padding: 12px 16px; border-bottom: 1px solid #555; cursor: pointer; transition: background 0.3s; display: flex; align-items: center; }
>>>>>>> 267c7f8db6d46cb8eac9b5208e409ad222c0d007
    .contact:hover, .contact.active { background: #343a40; }
    .contact-avatar { width: 40px; height: 40px; border-radius: 50%; background: #555; display: flex; align-items: center; justify-content: center; margin-right: 12px; color: white; font-weight: bold; }
    .contact-info { flex: 1; }
    .contact-name { font-weight: 500; margin-bottom: 2px; }
    .contact-lastmsg { font-size: 12px; color: #aaa; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .chat-header { padding: 12px 16px; background: #1e1e1e; border-bottom: 1px solid #555; display: flex; align-items: center; }
    .chat-messages { flex: 1; padding: 20px; overflow-y: auto; background: #0f2027; }
    .message { margin-bottom: 10px; padding: 10px 14px; border-radius: 15px; max-width: 70%; word-wrap: break-word; position: relative; }
    .from-me { background: linear-gradient(to right, #007bff, #00c6ff); color: white; margin-left: auto; border-top-right-radius: 0; }
    .from-them { background: #343a40; color: white; margin-right: auto; border-top-left-radius: 0; }
    .message-time { font-size: 11px; color: #aaa; margin-top: 4px; text-align: right; }
    .chat-input { padding: 10px; background: #1e1e1e; border-top: 1px solid #555; display: flex; }
    .chat-input input { flex: 1; border-radius: 8px; border: 1px solid #555; padding: 10px 15px; outline: none; background: #343a40; color: white; }
    .chat-input button { margin-left: 10px; border: none; background: #007bff; color: white; border-radius: 8px; padding: 10px 20px; cursor: pointer; }
    .no-chat-selected { display: flex; align-items: center; justify-content: center; height: 100%; color: #aaa; font-size: 18px; }
    .search-box { padding: 10px; border-bottom: 1px solid #555; }
    .search-box input { width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid #555; outline: none; background: #343a40; color: white; }
<<<<<<< HEAD
    .no-contacts { padding: 10px; color: #aaa; font-size: 14px; display: none; }
=======
>>>>>>> 267c7f8db6d46cb8eac9b5208e409ad222c0d007
  </style>
</head>
<body>
<div class="chat-container">
  <div class="contacts-panel">
    <div class="search-box">
<<<<<<< HEAD
      <input id="contactSearch" type="text" class="form-control" placeholder="<?= __('search_contacts') ?>">
    </div>

    <div id="contactsList">
      <?php foreach ($users as $id => $info): ?>
        <?php
          if ($id === $userId) continue;
          if (($user['role'] ?? '') === 'employee' && ($info['role'] ?? '') !== 'admin') continue;
          $nameSafe = htmlspecialchars($info['name'] ?? $id, ENT_QUOTES, 'UTF-8');
          $idSafe   = htmlspecialchars($id, ENT_QUOTES, 'UTF-8');
        ?>
        <a
          href="chat.php?user=<?= $idSafe ?>"
          class="contact <?= $id === $otherUserId ? 'active' : '' ?>"
          data-name="<?= $nameSafe ?>"
          data-id="<?= $idSafe ?>"
        >
          <div class="contact-avatar"><?= strtoupper(substr($info['name'] ?? $id, 0, 1)) ?></div>
          <div class="contact-info">
            <div class="contact-name"><?= $nameSafe ?></div>
            <div class="contact-lastmsg"><?= $idSafe ?></div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>

    <div id="noContactsMsg" class="no-contacts"><?= __('no_results') ?: 'No results' ?></div>
=======
      <input type="text" class="form-control" placeholder="<?= __('search_contacts') ?>">
    </div>
    
    <?php foreach ($users as $id => $info): ?>
      <?php if ($id === $userId) continue; ?>
      <?php if ($user['role'] === 'employee' && $info['role'] !== 'admin') continue; ?>
      <a href="chat.php?user=<?= $id ?>" class="contact <?= $id === $otherUserId ? 'active' : '' ?>">
        <div class="contact-avatar"><?= strtoupper(substr($info['name'], 0, 1)) ?></div>
        <div class="contact-info">
          <div class="contact-name"><?= htmlspecialchars($info['name']) ?></div>
          <div class="contact-lastmsg"><?= htmlspecialchars($id) ?></div>
        </div>
      </a>
    <?php endforeach; ?>
>>>>>>> 267c7f8db6d46cb8eac9b5208e409ad222c0d007
  </div>
  
  <div class="chat-panel">
    <?php if ($otherUserId): ?>
      <?php $otherUser = $users[$otherUserId] ?? ['name' => __('unknown')]; ?>
      <div class="chat-header">
<<<<<<< HEAD
        <div class="contact-avatar"><?= strtoupper(substr($otherUser['name'] ?? $otherUserId, 0, 1)) ?></div>
        <div class="contact-info">
          <div class="contact-name"><?= htmlspecialchars($otherUser['name'] ?? $otherUserId, ENT_QUOTES, 'UTF-8') ?></div>
=======
        <div class="contact-avatar"><?= strtoupper(substr($otherUser['name'], 0, 1)) ?></div>
        <div class="contact-info">
          <div class="contact-name"><?= htmlspecialchars($otherUser['name']) ?></div>
>>>>>>> 267c7f8db6d46cb8eac9b5208e409ad222c0d007
        </div>
      </div>
      
      <div class="chat-messages" id="chatBox">
        <?php foreach ($conversation as $msg): ?>
<<<<<<< HEAD
          <?php
            $isMe = ($msg['from'] ?? '') === $userId;
            $text = htmlspecialchars($msg['message'] ?? '', ENT_QUOTES, 'UTF-8');
            $time = isset($msg['timestamp']) ? date('H:i', strtotime($msg['timestamp'])) : '';
          ?>
          <div class="message <?= $isMe ? 'from-me' : 'from-them' ?>">
            <div><?= $text ?></div>
            <div class="message-time"><?= $time ?></div>
=======
          <div class="message <?= $msg['from'] === $userId ? 'from-me' : 'from-them' ?>">
            <div><?= htmlspecialchars($msg['message']) ?></div>
            <div class="message-time"><?= date('H:i', strtotime($msg['timestamp'])) ?></div>
>>>>>>> 267c7f8db6d46cb8eac9b5208e409ad222c0d007
          </div>
        <?php endforeach; ?>
      </div>
      
<<<<<<< HEAD
      <form method="post" class="chat-input" autocomplete="off">
        <input type="hidden" name="to" value="<?= htmlspecialchars($otherUserId, ENT_QUOTES, 'UTF-8') ?>">
=======
      <form method="post" class="chat-input">
        <input type="hidden" name="to" value="<?= $otherUserId ?>">
>>>>>>> 267c7f8db6d46cb8eac9b5208e409ad222c0d007
        <input type="text" name="message" placeholder="<?= __('type_your_message') ?>" required>
        <button type="submit"><?= __('send') ?></button>
      </form>
    <?php else: ?>
      <div class="no-chat-selected">
        <?= __('select_contact') ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
<<<<<<< HEAD
 
  const chatBox = document.getElementById('chatBox');
  if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;


  const searchInput  = document.getElementById('contactSearch');
  const contactsWrap = document.getElementById('contactsList');
  const noContacts   = document.getElementById('noContactsMsg');
  const contacts     = Array.from(document.querySelectorAll('.contacts-panel .contact'));

  function normalize(s) { return (s || '').toString().toLowerCase().trim(); }

  function applyFilter(q) {
    let visibleCount = 0;
    const query = normalize(q);
    contacts.forEach(c => {
      const name = normalize(c.getAttribute('data-name'));
      const uid  = normalize(c.getAttribute('data-id'));
      const match = !query || name.includes(query) || uid.includes(query);
      c.style.display = match ? '' : 'none';
      if (match) visibleCount++;
    });
    if (noContacts) noContacts.style.display = visibleCount ? 'none' : 'block';
  }

  if (searchInput) {
    searchInput.addEventListener('input', function() {
      applyFilter(this.value);
    });
  }
  
  applyFilter('');
=======
  const chatBox = document.getElementById('chatBox');
  if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
>>>>>>> 267c7f8db6d46cb8eac9b5208e409ad222c0d007
</script>
</body>
</html>
